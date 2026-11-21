<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\Empresa;
use App\Models\Serie;
use App\Models\Venta;
use App\Support\NumeroALetras;
use Barryvdh\DomPDF\Facade\Pdf;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Response\BillResult;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Summary\Summary;
use Greenter\Model\Summary\SummaryDetail;
use Greenter\Model\Voided\Voided;
use Greenter\Model\Voided\VoidedDetail;
use Greenter\See;
use Greenter\Ws\Services\SunatEndpoints;
use Illuminate\Support\Facades\Log;

/**
 * Emisión y anulación de comprobantes electrónicos con Greenter.
 *
 * Reglas clave:
 * - Los precios de venta INCLUYEN IGV (18%).
 * - El correlativo se reserva con bloqueo de fila y el comprobante se registra
 *   ANTES de enviarlo, así nunca se pierde un número aunque SUNAT o la red fallen.
 * - El envío a SUNAT ocurre FUERA de transacciones de BD.
 */
class FacturacionService
{
    private const IGV = 0.18;
    private const LIMITE_BOLETA_SIN_DOC = 700.00;

    protected ?See $see = null;
    protected ?Empresa $empresa = null;

    // =====================================================================
    // CONFIGURACIÓN
    // =====================================================================

    protected function configurarGreenter(Empresa $empresa): void
    {
        $this->empresa = $empresa;

        if (! $empresa->tieneFacturacionActiva()) {
            throw new \RuntimeException('La empresa no tiene la facturación electrónica activa. Actívala en Configuración de Empresa.');
        }

        if (empty($empresa->ruc) || strlen($empresa->ruc) !== 11) {
            throw new \RuntimeException('Configura un RUC válido (11 dígitos) en Configuración de Empresa.');
        }

        $esBeta = $empresa->esBeta();

        $see = new See();
        $see->setService($esBeta ? SunatEndpoints::FE_BETA : SunatEndpoints::FE_PRODUCCION);
        $see->setClaveSOL($empresa->ruc, (string) $empresa->getSolUser(), (string) $empresa->getSolPass());

        $certPath = $esBeta
            ? storage_path('app/certificados/certificate.pem') // certificado de prueba de Greenter
            : $empresa->getRutaCertificado();

        if (! $certPath || ! file_exists($certPath)) {
            throw new \RuntimeException($esBeta
                ? 'No se encontró el certificado de pruebas en storage/app/certificados/certificate.pem.'
                : 'No se encontró el certificado digital. Súbelo en Configuración de Empresa.');
        }

        if (! $esBeta && ! $empresa->tieneCredencialesSol()) {
            throw new \RuntimeException('Faltan el usuario y la clave SOL para producción.');
        }

        $see->setCertificate(file_get_contents($certPath));

        $this->see = $see;
    }

    // =====================================================================
    // EMISIÓN
    // =====================================================================

    /**
     * Emite factura (01) o boleta (03) a partir de una venta.
     *
     * @return array{success: bool, comprobante: ?Comprobante, mensaje: string, codigo_sunat: ?string}
     */
    public function emitirComprobante(Venta $venta, ?string $tipoComprobante = null, ?int $usuarioId = null): array
    {
        $comprobante = null;

        try {
            $venta->loadMissing(['empresa', 'cliente', 'detalles.repuesto']);

            $this->configurarGreenter($venta->empresa);
            $tipoComprobante ??= $venta->getTipoComprobanteSugerido();
            $this->validarVenta($venta, $tipoComprobante);

            // 1. Reservar número (atómico) y registrar el comprobante como "pendiente"
            $serie = $this->obtenerSerie($tipoComprobante);
            $correlativo = $serie->obtenerSiguienteCorrelativo();
            $invoice = $this->crearInvoice($venta, $tipoComprobante, $serie->serie, $correlativo);
            $comprobante = $this->registrarComprobante($venta, $invoice, $usuarioId);

            // 2. Enviar a SUNAT (fuera de transacciones)
            Log::info('Enviando comprobante a SUNAT', ['comprobante_id' => $comprobante->id, 'numero' => $comprobante->numero_completo]);

            try {
                $result = $this->see->send($invoice);
            } catch (\Throwable $e) {
                // Sin respuesta: queda pendiente para reenviar con "Consultar estado"
                $this->guardarXml($comprobante);
                $comprobante->update([
                    'estado_sunat' => 'pendiente',
                    'mensaje_sunat' => 'No se pudo conectar con SUNAT: ' . $e->getMessage(),
                ]);
                $this->generarPDF($comprobante);

                return $this->respuesta(false, $comprobante, 'No hubo respuesta de SUNAT. El comprobante quedó pendiente; usa "Consultar estado" para reenviarlo.');
            }

            // 3. Guardar resultado
            $this->guardarXml($comprobante);
            $this->procesarResultado($comprobante, $result);
            $this->generarPDF($comprobante);

            $comprobante->refresh();
            $ok = in_array($comprobante->estado_sunat, ['aceptado', 'observado']);

            return $this->respuesta(
                $ok,
                $comprobante,
                $ok ? 'Comprobante emitido y aceptado por SUNAT.' : 'SUNAT rechazó el comprobante: ' . $comprobante->mensaje_sunat,
                $comprobante->codigo_sunat
            );
        } catch (\Throwable $e) {
            Log::error('Error al emitir comprobante', ['venta_id' => $venta->id, 'error' => $e->getMessage()]);

            if ($comprobante && $comprobante->estado_sunat === 'pendiente') {
                $comprobante->update(['mensaje_sunat' => $e->getMessage()]);
            }

            return $this->respuesta(false, $comprobante, $e->getMessage());
        }
    }

    /**
     * Reenvía un comprobante que quedó "pendiente" (sin respuesta de SUNAT).
     */
    public function reenviarPendiente(Comprobante $comprobante): array
    {
        $this->configurarGreenter($comprobante->empresa);

        $xmlPath = $comprobante->rutaArchivo('xml_path');
        if (! $xmlPath) {
            throw new \RuntimeException('No se encontró el XML del comprobante para reenviarlo.');
        }

        $result = $this->see->sendXmlFile(file_get_contents($xmlPath));
        $this->procesarResultado($comprobante, $result);
        $comprobante->refresh();

        return $this->respuesta(
            in_array($comprobante->estado_sunat, ['aceptado', 'observado']),
            $comprobante,
            $comprobante->mensaje_sunat ?? 'Estado actualizado.',
            $comprobante->codigo_sunat
        );
    }

    protected function validarVenta(Venta $venta, string $tipo): void
    {
        if (! in_array($tipo, ['01', '03'], true)) {
            throw new \RuntimeException('Tipo de comprobante inválido.');
        }

        if ($venta->estado !== 'pagado') {
            throw new \RuntimeException('Solo se pueden emitir comprobantes de ventas pagadas.');
        }

        if ($venta->tieneComprobante()) {
            throw new \RuntimeException('Esta venta ya tiene un comprobante electrónico vigente.');
        }

        if ($venta->detalles->isEmpty()) {
            throw new \RuntimeException('La venta no tiene productos.');
        }

        if ($tipo === '01' && ! $venta->cliente?->tieneRuc()) {
            throw new \RuntimeException('Para emitir una FACTURA el cliente debe tener RUC (11 dígitos). Edita el cliente o emite una boleta.');
        }

        if ($tipo === '03' && (float) $venta->total >= self::LIMITE_BOLETA_SIN_DOC
            && $this->documentoCliente($venta->cliente)[0] === '0') {
            throw new \RuntimeException('Las boletas de S/ 700 o más requieren DNI o RUC del cliente.');
        }
    }

    protected function crearInvoice(Venta $venta, string $tipo, string $serie, int $correlativo): Invoice
    {
        $fecha = new \DateTime();

        $invoice = (new Invoice())
            ->setUblVersion('2.1')
            ->setTipoOperacion('0101') // Venta interna
            ->setTipoDoc($tipo)
            ->setSerie($serie)
            ->setCorrelativo((string) $correlativo)
            ->setFechaEmision($fecha)
            ->setTipoMoneda('PEN')
            ->setCompany($this->crearCompany())
            ->setClient($this->crearClient($venta->cliente, $tipo));

        // Detalle: el total de cada línea = cantidad * precio, sin descuadres.
        // Gravado (10): el precio incluye IGV. Exonerado (20) / Inafecto (30): sin IGV.
        $details = [];
        $totales = ['10' => 0.0, '20' => 0.0, '30' => 0.0];
        $totalIgv = 0.0;

        foreach ($venta->detalles as $detalle) {
            $afectacion = $detalle->afectacion_igv ?: ($detalle->repuesto->afectacion_igv ?? '10');
            $afectacion = array_key_exists($afectacion, $totales) ? $afectacion : '10';
            $unidad = $detalle->unidad_medida ?: ($detalle->repuesto->unidad_medida ?? 'NIU');
            $esServicio = ($detalle->repuesto->tipo ?? 'producto') === 'servicio';

            $precio = round((float) $detalle->precio_unitario, 2);
            $cantidad = round((float) $detalle->cantidad, 3);
            $totalLinea = round($precio * $cantidad, 2);

            if ($afectacion === '10') {
                $valorVenta = round($totalLinea / (1 + self::IGV), 2);
                $igvLinea = round($totalLinea - $valorVenta, 2);
                $valorUnitario = round($precio / (1 + self::IGV), 6);
                $porcentaje = self::IGV * 100;
            } else {
                $valorVenta = $totalLinea;
                $igvLinea = 0.0;
                $valorUnitario = $precio;
                $porcentaje = 0;
            }

            $nombre = $detalle->repuesto->nombre ?? 'PRODUCTO';

            $details[] = (new SaleDetail())
                ->setCodProducto($detalle->repuesto->codigo ?? 'P' . str_pad((string) $detalle->repuesto_id, 6, '0', STR_PAD_LEFT))
                ->setUnidad($esServicio && $unidad === 'NIU' ? 'ZZ' : $unidad)
                ->setCantidad($cantidad)
                ->setDescripcion(mb_strtoupper(mb_substr($nombre, 0, 250)))
                ->setMtoBaseIgv($valorVenta)
                ->setPorcentajeIgv($porcentaje)
                ->setIgv($igvLinea)
                ->setTipAfeIgv($afectacion)
                ->setTotalImpuestos($igvLinea)
                ->setMtoValorVenta($valorVenta)
                ->setMtoValorUnitario($valorUnitario)
                ->setMtoPrecioUnitario($precio);

            $totales[$afectacion] += $valorVenta;
            $totalIgv += $igvLinea;
        }

        $totalGravado = round($totales['10'], 2);
        $totalExonerado = round($totales['20'], 2);
        $totalInafecto = round($totales['30'], 2);
        $totalIgv = round($totalIgv, 2);
        $valorVentaTotal = round($totalGravado + $totalExonerado + $totalInafecto, 2);
        $total = round($valorVentaTotal + $totalIgv, 2);

        $invoice->setDetails($details)
            ->setMtoOperGravadas($totalGravado)
            ->setMtoOperExoneradas($totalExonerado)
            ->setMtoOperInafectas($totalInafecto)
            ->setMtoIGV($totalIgv)
            ->setTotalImpuestos($totalIgv)
            ->setValorVenta($valorVentaTotal)
            ->setSubTotal($total)
            ->setMtoImpVenta($total)
            ->setLegends([
                (new Legend())->setCode('1000')->setValue(NumeroALetras::monto($total)),
            ]);

        if ($tipo === '01') {
            $invoice->setFecVencimiento($fecha)
                ->setFormaPago((new FormaPagoContado())->setMoneda('PEN')->setMonto($total));
        }

        return $invoice;
    }

    protected function crearCompany(): Company
    {
        $e = $this->empresa;

        $address = (new Address())
            ->setUbigueo($e->ubigeo ?: '150101')
            ->setDepartamento(mb_strtoupper($e->departamento ?: 'LIMA'))
            ->setProvincia(mb_strtoupper($e->provincia ?: 'LIMA'))
            ->setDistrito(mb_strtoupper($e->distrito ?: 'LIMA'))
            ->setUrbanizacion(mb_strtoupper($e->urbanizacion ?: '-'))
            ->setDireccion(mb_strtoupper($e->direccion_fiscal ?: ($e->direccion ?: '-')))
            ->setCodLocal('0000');

        return (new Company())
            ->setRuc($e->ruc)
            ->setRazonSocial(mb_strtoupper($e->razon_social ?: $e->nombre))
            ->setNombreComercial(mb_strtoupper($e->nombre_comercial ?: $e->nombre))
            ->setAddress($address);
    }

    /**
     * Devuelve [tipoDoc, numeroDoc, nombre] según catálogo 06 de SUNAT.
     */
    protected function documentoCliente(?Cliente $cliente, string $tipoComprobante = '03'): array
    {
        if ($cliente && $cliente->tieneRuc() && $tipoComprobante === '01') {
            return ['6', $cliente->ruc, $cliente->nombre];
        }

        if ($cliente && ! empty($cliente->dni) && strlen($cliente->dni) === 8) {
            return ['1', $cliente->dni, $cliente->nombre];
        }

        if ($cliente && $cliente->tieneRuc()) {
            return ['6', $cliente->ruc, $cliente->nombre];
        }

        // Boleta a "clientes varios" (montos menores a S/ 700)
        return ['0', '-', $cliente->nombre ?? 'CLIENTES VARIOS'];
    }

    protected function crearClient(?Cliente $cliente, string $tipoComprobante): Client
    {
        [$tipoDoc, $numDoc, $nombre] = $this->documentoCliente($cliente, $tipoComprobante);

        $client = (new Client())
            ->setTipoDoc($tipoDoc)
            ->setNumDoc($numDoc)
            ->setRznSocial(mb_strtoupper($nombre ?: 'CLIENTES VARIOS'));

        if ($cliente?->direccion) {
            $client->setAddress((new Address())->setDireccion(mb_strtoupper($cliente->direccion)));
        }

        return $client;
    }

    protected function obtenerSerie(string $tipoComprobante): Serie
    {
        $serie = Serie::withoutGlobalScope('tenant')
            ->where('empresa_id', $this->empresa->id)
            ->where('tipo_comprobante', $tipoComprobante)
            ->where('activo', true)
            ->first();

        if ($serie) {
            return $serie;
        }

        $codigo = match ($tipoComprobante) {
            '01' => config('sunat.series.factura', 'F001'),
            '03' => config('sunat.series.boleta', 'B001'),
            '07' => config('sunat.series.nota_credito_factura', 'FC01'),
            '08' => config('sunat.series.nota_debito_factura', 'FD01'),
            default => throw new \RuntimeException('Tipo de comprobante sin serie configurada.'),
        };

        return Serie::create([
            'empresa_id' => $this->empresa->id,
            'tipo_comprobante' => $tipoComprobante,
            'serie' => $codigo,
            'correlativo_actual' => 0,
            'activo' => true,
            'descripcion' => 'Serie por defecto',
        ]);
    }

    protected function registrarComprobante(Venta $venta, Invoice $invoice, ?int $usuarioId): Comprobante
    {
        [$tipoDoc, $numDoc, $nombre] = $this->documentoCliente($venta->cliente, $invoice->getTipoDoc());

        return Comprobante::create([
            'venta_id' => $venta->id,
            'empresa_id' => $this->empresa->id,
            'cliente_id' => $venta->cliente_id,
            'tipo_comprobante' => $invoice->getTipoDoc(),
            'serie' => $invoice->getSerie(),
            'correlativo' => (int) $invoice->getCorrelativo(),
            'numero_completo' => $invoice->getSerie() . '-' . str_pad($invoice->getCorrelativo(), 8, '0', STR_PAD_LEFT),
            'cliente_tipo_doc' => $tipoDoc,
            'cliente_num_doc' => $numDoc,
            'cliente_nombre' => mb_strtoupper($nombre ?: 'CLIENTES VARIOS'),
            'subtotal' => $invoice->getValorVenta(),
            'igv' => $invoice->getMtoIGV(),
            'total' => $invoice->getMtoImpVenta(),
            'total_gravadas' => $invoice->getMtoOperGravadas(),
            'total_exoneradas' => $invoice->getMtoOperExoneradas(),
            'total_inafectas' => $invoice->getMtoOperInafectas(),
            'total_igv' => $invoice->getMtoIGV(),
            'total_letras' => $invoice->getLegends()[0]->getValue(),
            'moneda' => 'PEN',
            'fecha_emision' => $invoice->getFechaEmision(),
            'estado_sunat' => 'pendiente',
            'ambiente' => $this->empresa->esBeta() ? 'beta' : 'produccion',
            'usuario_emision_id' => $usuarioId ?? auth()->id(),
        ]);
    }

    /**
     * Interpreta la respuesta de SUNAT:
     * - CDR con código 0          => aceptado
     * - CDR con código >= 4000    => observado (válido, con observaciones)
     * - CDR con código 2000-3999  => rechazado
     * - Error sin CDR             => rechazado (o excepción < 2000: el documento no se registró)
     */
    protected function procesarResultado(Comprobante $comprobante, $result): void
    {
        $datos = ['fecha_envio_sunat' => now()];

        if ($result && $result->isSuccess() && $result instanceof BillResult && $result->getCdrResponse()) {
            $cdr = $result->getCdrResponse();
            $notas = $cdr->getNotes() ? implode(' | ', $cdr->getNotes()) : null;

            $datos['codigo_sunat'] = (string) $cdr->getCode();
            $datos['mensaje_sunat'] = $cdr->getDescription();
            $datos['observaciones_sunat'] = $notas;
            $datos['estado_sunat'] = $cdr->isAccepted()
                ? ((int) $cdr->getCode() >= 4000 || $notas ? 'observado' : 'aceptado')
                : 'rechazado';
            $datos['hash_cdr'] = $cdr->getId();

            if ($result->getCdrZip()) {
                $datos['cdr_path'] = $this->guardarArchivo('cdr', 'R-' . $comprobante->numero_completo . '.zip', $result->getCdrZip());
            }
        } else {
            $error = $result?->getError();
            $datos['estado_sunat'] = 'rechazado';
            $datos['codigo_sunat'] = $error?->getCode();
            $datos['mensaje_sunat'] = $error?->getMessage() ?? 'Respuesta inválida de SUNAT';
        }

        $comprobante->update($datos);
    }

    // =====================================================================
    // ANULACIÓN (Comunicación de Baja / Resumen Diario)
    // =====================================================================

    /**
     * Envía la anulación a SUNAT:
     * - Facturas: Comunicación de Baja (RA).
     * - Boletas: Resumen Diario con estado 3 = anulado (RC).
     */
    public function anularComprobante(Comprobante $comprobante, string $motivo, ?int $usuarioId = null): array
    {
        if (! $comprobante->puedeSerAnulado()) {
            throw new \RuntimeException('Este comprobante no puede ser anulado (estado, plazo o notas asociadas).');
        }

        $this->configurarGreenter($comprobante->empresa);

        $hoy = new \DateTime();
        $correlativo = (string) $this->siguienteCorrelativoBaja($comprobante->empresa_id);
        $fechaEmision = \DateTime::createFromInterface($comprobante->fecha_emision);

        if (str_starts_with($comprobante->serie, 'B')) {
            [$tipoDoc, $numDoc] = [$comprobante->cliente_tipo_doc ?: '0', $comprobante->cliente_num_doc ?: '-'];

            $documento = (new Summary())
                ->setFecGeneracion($fechaEmision)
                ->setFecResumen($hoy)
                ->setCorrelativo($correlativo)
                ->setCompany($this->crearCompany())
                ->setDetails([
                    (new SummaryDetail())
                        ->setTipoDoc($comprobante->tipo_comprobante)
                        ->setSerieNro($comprobante->serie . '-' . $comprobante->correlativo)
                        ->setEstado('3') // 3 = Anulado
                        ->setClienteTipo($tipoDoc)
                        ->setClienteNro($numDoc)
                        ->setTotal((float) $comprobante->total)
                        ->setMtoOperGravadas((float) ($comprobante->total_gravadas ?? $comprobante->subtotal))
                        ->setMtoOperInafectas((float) ($comprobante->total_inafectas ?? 0))
                        ->setMtoOperExoneradas((float) ($comprobante->total_exoneradas ?? 0))
                        ->setMtoOtrosCargos(0)
                        ->setMtoIGV((float) $comprobante->igv),
                ]);
        } else {
            $documento = (new Voided())
                ->setCorrelativo($correlativo)
                ->setFecGeneracion($fechaEmision)
                ->setFecComunicacion($hoy)
                ->setCompany($this->crearCompany())
                ->setDetails([
                    (new VoidedDetail())
                        ->setTipoDoc($comprobante->tipo_comprobante)
                        ->setSerie($comprobante->serie)
                        ->setCorrelativo((string) $comprobante->correlativo)
                        ->setDesMotivoBaja(mb_strtoupper(mb_substr($motivo, 0, 100))),
                ]);
        }

        $result = $this->see->send($documento);

        if (! $result || ! $result->isSuccess()) {
            $error = $result?->getError();
            throw new \RuntimeException('SUNAT no aceptó la solicitud de baja: ' . ($error?->getMessage() ?? 'sin respuesta'));
        }

        $comprobante->update([
            'ticket_baja' => $result->getTicket(),
            'estado_sunat' => 'baja_pendiente',
            'fecha_anulacion' => now(),
            'motivo_anulacion' => $motivo,
            'usuario_anulacion_id' => $usuarioId ?? auth()->id(),
            'mensaje_sunat' => 'Baja enviada. Ticket: ' . $result->getTicket(),
        ]);

        // SUNAT procesa los tickets de forma asíncrona; intentamos consultar de inmediato.
        return $this->consultarBaja($comprobante);
    }

    /**
     * Consulta el ticket de baja y actualiza el comprobante.
     */
    public function consultarBaja(Comprobante $comprobante): array
    {
        if (! $comprobante->ticket_baja) {
            throw new \RuntimeException('El comprobante no tiene una baja en proceso.');
        }

        $this->configurarGreenter($comprobante->empresa);

        $status = $this->see->getStatus($comprobante->ticket_baja);
        $codigo = $status->getCode();

        if ($status->isSuccess() && $status->getCdrResponse()?->isAccepted()) {
            $comprobante->update([
                'anulado' => true,
                'estado_sunat' => 'anulado',
                'mensaje_sunat' => 'Anulación aceptada por SUNAT. ' . $status->getCdrResponse()->getDescription(),
            ]);

            if ($status->getCdrZip()) {
                $this->guardarArchivo('cdr', 'R-BAJA-' . $comprobante->numero_completo . '.zip', $status->getCdrZip());
            }

            return $this->respuesta(true, $comprobante, 'Comprobante anulado y aceptado por SUNAT.');
        }

        if ($codigo === '98') {
            return $this->respuesta(true, $comprobante, 'SUNAT aún está procesando la baja. Consulta el estado en unos minutos.');
        }

        // Rechazada: el comprobante sigue vigente
        $mensaje = $status->getCdrResponse()?->getDescription() ?? $status->getError()?->getMessage() ?? 'Baja rechazada';
        $comprobante->update([
            'ticket_baja' => null,
            'estado_sunat' => 'aceptado',
            'fecha_anulacion' => null,
            'mensaje_sunat' => 'Baja rechazada por SUNAT: ' . $mensaje,
        ]);

        return $this->respuesta(false, $comprobante, 'SUNAT rechazó la baja: ' . $mensaje);
    }

    protected function siguienteCorrelativoBaja(int $empresaId): int
    {
        return Comprobante::withoutGlobalScope('tenant')
            ->where('empresa_id', $empresaId)
            ->whereNotNull('fecha_anulacion')
            ->whereDate('fecha_anulacion', today())
            ->count() + 1;
    }

    // =====================================================================
    // ARCHIVOS
    // =====================================================================

    protected function guardarXml(Comprobante $comprobante): void
    {
        $xml = $this->see->getFactory()->getLastXml();

        if ($xml) {
            $comprobante->update([
                'xml_path' => $this->guardarArchivo('xml', $comprobante->numero_completo . '.xml', $xml),
            ]);
        }
    }

    /**
     * Guarda en storage/app/sunat/{tipo}/{empresa_id}/ y devuelve la ruta relativa a storage/app.
     */
    protected function guardarArchivo(string $tipo, string $nombre, string $contenido): string
    {
        $relativa = "sunat/{$tipo}/{$this->empresa->id}/{$nombre}";
        $absoluta = storage_path('app/' . $relativa);

        if (! is_dir(dirname($absoluta))) {
            mkdir(dirname($absoluta), 0755, true);
        }

        file_put_contents($absoluta, $contenido);

        return $relativa;
    }

    protected function generarPDF(Comprobante $comprobante): void
    {
        try {
            $pdf = Pdf::loadView('comprobantes.pdf', [
                'comprobante' => $comprobante->fresh(),
                'empresa' => $this->empresa,
                'detalles' => $comprobante->venta->detalles()->with('repuesto')->get(),
            ])->setPaper('a4', 'portrait');

            $comprobante->update([
                'pdf_path' => $this->guardarArchivo('pdf', $comprobante->numero_completo . '.pdf', $pdf->output()),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al generar PDF del comprobante', ['comprobante_id' => $comprobante->id, 'error' => $e->getMessage()]);
        }
    }

    protected function respuesta(bool $success, ?Comprobante $comprobante, string $mensaje, ?string $codigo = null): array
    {
        return [
            'success' => $success,
            'comprobante' => $comprobante,
            'mensaje' => $mensaje,
            'codigo_sunat' => $codigo,
        ];
    }
}
