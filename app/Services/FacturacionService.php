<?php

namespace App\Services;

use App\Models\Venta;
use App\Models\Comprobante;
use App\Models\Serie;
use App\Models\Empresa;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\Ws\Services\SunatEndpoints;
use Greenter\See;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado; // 👈 AGREGAR ESTO

class FacturacionService
{
    protected $see;
    protected $empresa;

    public function __construct()
    {
        // La empresa se configurará cuando se llame al método
    }

    /**
     * Configurar Greenter con datos de la empresa
     */
    protected function configurarGreenter(Empresa $empresa)
{
    $this->empresa = $empresa;

    // Verificar que la empresa tenga facturación activa
    if (!$empresa->tieneFacturacionActiva()) {
        throw new \Exception('La empresa no tiene facturación electrónica activa.');
    }

    // Crear instancia de See (Greenter)
    $this->see = new See();
    
    // Determinar ambiente
    $esBeta = $empresa->esBeta();
    
    // Configurar servicio SUNAT
    $this->see->setService($esBeta 
        ? SunatEndpoints::FE_BETA 
        : SunatEndpoints::FE_PRODUCCION
    );
    
    // Configurar credenciales SOL
    $this->see->setClaveSOL(
        $empresa->ruc,
        $empresa->getSolUser() ?? '',
        $empresa->getSolPass() ?? ''
    );

    // ==========================================
    // CONFIGURAR CERTIFICADO
    // ==========================================
    
    if ($esBeta) {
        // Usar certificado de prueba de Greenter
        $certPath = storage_path('app/certificados/certificate.pem');
        
        if (!file_exists($certPath)) {
            throw new \Exception('Certificado Beta no encontrado en: ' . $certPath);
        }
        
        $pem = file_get_contents($certPath);
        $this->see->setCertificate($pem);
        
        Log::info('Greenter configurado para BETA con certificado de prueba', [
            'empresa_id' => $empresa->id,
            'ruc' => $empresa->ruc,
        ]);
    } else {
        // PRODUCCIÓN: Usar certificado propio
        $certificadoPath = $empresa->getRutaCertificado();
        
        if (!$certificadoPath || !file_exists($certificadoPath)) {
            throw new \Exception('Certificado digital no encontrado para producción');
        }

        $pem = file_get_contents($certificadoPath);
        $this->see->setCertificate($pem);
        
        Log::info('Greenter configurado para PRODUCCIÓN', [
            'empresa_id' => $empresa->id,
            'ruc' => $empresa->ruc,
        ]);
    }
}



/**
 * Configura el certificado de prueba para ambiente Beta
 */
/**
 * Configura el certificado de prueba para ambiente Beta
 */



    /**
     * Emitir comprobante electrónico desde una venta
     */
    public function emitirComprobante(Venta $venta, string $tipoComprobante = null,$usuarioId = null)
    {
        try {
            // Configurar Greenter con la empresa de la venta
            $this->configurarGreenter($venta->empresa);

            // Validar que la venta no tenga ya un comprobante
            if ($venta->tieneComprobanteElectronico()) {
                throw new \Exception('Esta venta ya tiene un comprobante electrónico.');
            }

            // Validar que la venta tenga detalles
            if ($venta->detalles->isEmpty()) {
                throw new \Exception('La venta no tiene detalles (repuestos).');
            }

            // Determinar tipo de comprobante si no se especificó
            if (!$tipoComprobante) {
                $tipoComprobante = $venta->getTipoComprobanteSugerido();
            }

            // Iniciar transacción
            DB::beginTransaction();

            // Obtener serie y correlativo
            $serie = $this->obtenerSerie($tipoComprobante);
            $correlativo = $serie->obtenerSiguienteCorrelativo();

            // Crear invoice (factura o boleta)
            $invoice = $this->crearInvoice($venta, $tipoComprobante, $serie->serie, $correlativo);

            Log::info('Enviando comprobante a SUNAT', [
                'venta_id' => $venta->id,
                'tipo' => $tipoComprobante,
                'serie' => $serie->serie,
                'correlativo' => $correlativo,
            ]);

            // Enviar a SUNAT
            $result = $this->see->send($invoice);

            // Guardar comprobante en BD
            $comprobante = $this->guardarComprobante($venta, $invoice, $result);

            // Guardar archivos XML, CDR y PDF
            $this->guardarArchivos($comprobante, $result, $invoice);

            // Commit de la transacción
            DB::commit();

            Log::info('Comprobante emitido exitosamente', [
                'comprobante_id' => $comprobante->id,
                'numero' => $comprobante->numero_completo,
                'estado' => $comprobante->estado_sunat,
            ]);

            return [
                'success' => $result->isSuccess(),
                'comprobante' => $comprobante,
                'mensaje' => $result->isSuccess() 
                    ? 'Comprobante emitido y aceptado por SUNAT' 
                    : 'Error: ' . $result->getError()->getMessage(),
                'codigo_sunat' => $result->isSuccess() ? $result->getCdrResponse()->getCode() : null,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al emitir comprobante', [
                'venta_id' => $venta->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'mensaje' => $e->getMessage(),
                'comprobante' => null,
            ];
        }
    }

    /**
     * Crear objeto Invoice de Greenter
     */
    protected function crearInvoice(Venta $venta, string $tipo, string $serie, int $correlativo)
{
    $invoice = new Invoice();
    
    // Datos del comprobante
    $invoice
        ->setUblVersion('2.1')
        ->setTipoOperacion('0101') // 0101 = Venta interna
        ->setTipoDoc($tipo) // 01=Factura, 03=Boleta
        ->setSerie($serie)
        ->setCorrelativo(str_pad($correlativo, 8, '0', STR_PAD_LEFT))
        ->setFechaEmision(new \DateTime())
        ->setFecVencimiento(new \DateTime()) // 👈 AGREGAR
        ->setTipoMoneda('PEN'); // Soles
         // 👇 AGREGAR ESTO: Forma de pago (CONTADO)
    $formaPago = new FormaPagoContado();
    $formaPago->setMoneda('PEN')
              ->setMonto($venta->total);
    $invoice->setFormaPago($formaPago);

    // Datos de la empresa emisora
    $company = $this->crearCompany();
    $invoice->setCompany($company);

    // Datos del cliente
    $client = $this->crearClient($venta->cliente, $tipo);
    $invoice->setClient($client);

    // Detalles (repuestos vendidos)
    $details = [];
    foreach ($venta->detalles as $index => $detalle) {
        $item = new SaleDetail();
        
        // Calcular valores sin IGV
        $precioUnitarioSinIgv = round($detalle->precio_unitario / 1.18, 2);
        $valorVenta = round($precioUnitarioSinIgv * $detalle->cantidad, 2);
        $igvItem = round($valorVenta * 0.18, 2);
        
        $item
            ->setCodProducto($detalle->repuesto->codigo ?? 'P' . str_pad($detalle->repuesto_id, 6, '0', STR_PAD_LEFT))
            ->setUnidad('NIU') // NIU = Unidad (pieza)
            ->setCantidad($detalle->cantidad)
            ->setDescripcion(mb_strtoupper(substr($detalle->repuesto->nombre, 0, 250))) // Máximo 250 caracteres
            ->setMtoBaseIgv($valorVenta) // Base imponible
            ->setPorcentajeIgv(18.00) // 18%
            ->setIgv($igvItem) // IGV del item
            ->setTipAfeIgv('10') // 10 = Gravado - Operación Onerosa
            ->setTotalImpuestos($igvItem)
            ->setMtoValorVenta($valorVenta) // Valor de venta sin IGV
            ->setMtoValorUnitario($precioUnitarioSinIgv) // Precio unitario sin IGV
            ->setMtoPrecioUnitario($detalle->precio_unitario); // Precio unitario con IGV

        $details[] = $item;
    }
    $invoice->setDetails($details);

    // Calcular totales
    $subtotal = round($venta->total / 1.18, 2); // Base imponible
    $igv = round($venta->total - $subtotal, 2); // IGV

    $invoice
        ->setMtoOperGravadas($subtotal) // Operaciones gravadas
        ->setMtoOperExoneradas(0.00) // 👈 AGREGAR
        ->setMtoOperInafectas(0.00) // 👈 AGREGAR
        ->setMtoIGV($igv) // Total IGV
        ->setMtoISC(0.00) // 👈 AGREGAR (Impuesto Selectivo al Consumo)
        ->setMtoOtrosTributos(0.00) // 👈 AGREGAR
        ->setIcbper(0.00) // 👈 AGREGAR (Impuesto a las bolsas plásticas)
        ->setTotalImpuestos($igv) // Total impuestos
        ->setValorVenta($subtotal) // Valor de venta
        ->setSubTotal($venta->total) // Subtotal
        ->setRedondeo(0.00) // 👈 AGREGAR
        ->setMtoImpVenta($venta->total); // Total a pagar

    // Leyenda (monto en letras)
    $legend = new Legend();
    $legend
        ->setCode('1000') // Monto en letras
        ->setValue($this->numeroALetras($venta->total));
    $invoice->setLegends([$legend]);

    return $invoice;
}

    /**
     * Crear Company (datos de la empresa emisora)
     */
    protected function crearCompany()
    {
        $address = new Address();
        $address
            ->setUbigueo($this->empresa->ubigeo ?? '150101')
            ->setDepartamento(mb_strtoupper($this->empresa->departamento ?? 'LIMA'))
            ->setProvincia(mb_strtoupper($this->empresa->provincia ?? 'LIMA'))
            ->setDistrito(mb_strtoupper($this->empresa->distrito ?? 'LIMA'))
            ->setUrbanizacion(mb_strtoupper($this->empresa->urbanizacion ?? '-'))
            ->setDireccion(mb_strtoupper($this->empresa->direccion_fiscal ?? $this->empresa->direccion ?? 'AV. PRINCIPAL 123'))
            ->setCodLocal('0000'); // Código del establecimiento (0000 = principal)

        $company = new Company();
        $company
            ->setRuc($this->empresa->ruc)
            ->setRazonSocial(mb_strtoupper($this->empresa->razon_social ?? $this->empresa->nombre))
            ->setNombreComercial(mb_strtoupper($this->empresa->nombre_comercial ?? $this->empresa->nombre))
            ->setAddress($address);

        return $company;
    }

    /**
     * Crear Client (datos del cliente)
     */
    protected function crearClient($cliente, $tipoComprobante)
    {
        $client = new Client();
        
        // Para factura (01): requiere RUC
        if ($tipoComprobante === '01') {
            if (!$cliente->ruc) {
                throw new \Exception('El cliente debe tener RUC para emitir una factura.');
            }
            
            $client
                ->setTipoDoc('6') // 6 = RUC
                ->setNumDoc($cliente->ruc)
                ->setRznSocial(mb_strtoupper($cliente->nombre));
        } 
        // Para boleta (03): puede ser DNI o RUC
        else {
            $client
                ->setTipoDoc($cliente->ruc ? '6' : '1') // 1=DNI, 6=RUC
                ->setNumDoc($cliente->ruc ?: $cliente->dni)
                ->setRznSocial(mb_strtoupper($cliente->nombre));
        }

        // Dirección (opcional pero recomendado)
        if ($cliente->direccion) {
            $client->setAddress((new Address())
                ->setDireccion(mb_strtoupper($cliente->direccion)));
        }

        return $client;
    }

    /**
     * Obtener o crear serie para el tipo de comprobante
     */
    protected function obtenerSerie(string $tipoComprobante)
    {
        // Buscar serie activa existente
        $serie = Serie::where('empresa_id', $this->empresa->id)
            ->where('tipo_comprobante', $tipoComprobante)
            ->where('activo', true)
            ->first();

        // Si no existe, crear una nueva
        if (!$serie) {
            $serieCodigo = match($tipoComprobante) {
                '01' => config('sunat.series.factura', 'F001'),
                '03' => config('sunat.series.boleta', 'B001'),
                '07' => config('sunat.series.nota_credito_factura', 'FC01'),
                '08' => config('sunat.series.nota_debito_factura', 'FD01'),
                default => 'F001',
            };

            $serie = Serie::create([
                'empresa_id' => $this->empresa->id,
                'tipo_comprobante' => $tipoComprobante,
                'serie' => $serieCodigo,
                'correlativo_actual' => 0,
                'activo' => true,
                'descripcion' => 'Serie por defecto',
            ]);

            Log::info('Serie creada automáticamente', [
                'empresa_id' => $this->empresa->id,
                'serie' => $serieCodigo,
                'tipo' => $tipoComprobante,
            ]);
        }

        return $serie;
    }

    /**
     * Guardar comprobante en base de datos
     */
    protected function guardarComprobante(Venta $venta, Invoice $invoice, $result)
{
    $subtotal = round($venta->total / 1.18, 2);
    $igv = round($venta->total - $subtotal, 2);

    // ✅ Definir tipo y número de documento
    $tipoDoc = $venta->cliente->ruc ? '6' : '1';
    $numDoc = $venta->cliente->ruc ?: $venta->cliente->dni;

    $comprobante = Comprobante::create([
        'venta_id' => $venta->id,
        'empresa_id' => $this->empresa->id,
        'cliente_id' => $venta->cliente_id,
        'tipo_comprobante' => $invoice->getTipoDoc(),
        'serie' => $invoice->getSerie(),
        'correlativo' => (int)$invoice->getCorrelativo(),
        'numero_completo' => $invoice->getSerie() . '-' . str_pad($invoice->getCorrelativo(), 8, '0', STR_PAD_LEFT),
        'cliente_tipo_doc' => $tipoDoc,
        'cliente_num_doc' => $numDoc,
        'cliente_nombre' => $venta->cliente->nombre,
        'subtotal' => $subtotal,
        'igv' => $igv,
        'total' => $venta->total,
        'total_letras' => $invoice->getLegends()[0]->getValue(),
        'moneda' => 'PEN',
        'fecha_emision' => now(),
        'fecha_envio_sunat' => now(),
        'estado_sunat' => $result->isSuccess() ? 'aceptado' : 'rechazado',
        'codigo_sunat' => $result->isSuccess() ? $result->getCdrResponse()->getCode() : null,
        'mensaje_sunat' => $result->isSuccess() 
            ? $result->getCdrResponse()->getDescription()
            : $result->getError()->getMessage(),
        'observaciones_sunat' => $result->isSuccess() && $result->getCdrResponse()->getNotes()
            ? implode(' | ', $result->getCdrResponse()->getNotes())
            : null,
        'ambiente' => $this->empresa->ambiente_sunat,
'usuario_emision_id' => auth()->check() ? auth()->id() : null,
    ]);

    return $comprobante;
}



    /**
     * Guardar archivos XML, CDR y PDF
     */
    protected function guardarArchivos(Comprobante $comprobante, $result, Invoice $invoice)
{
    $filename = $comprobante->numero_completo;
    $empresaId = $this->empresa->id;

    try {
        // ==========================================
        // GUARDAR XML
        // ==========================================
        $xmlDir = storage_path("app/sunat/xml/{$empresaId}");
        
        // Crear directorio si no existe
        if (!is_dir($xmlDir)) {
            mkdir($xmlDir, 0755, true);
            Log::info("Directorio XML creado: {$xmlDir}");
        }

        // Guardar XML con file_put_contents
        $xmlContent = $this->see->getFactory()->getLastXml();
        $xmlPath = "sunat/xml/{$empresaId}/{$filename}.xml";
        $xmlFullPath = storage_path("app/{$xmlPath}");
        
        file_put_contents($xmlFullPath, $xmlContent);
        
        if (file_exists($xmlFullPath)) {
            $comprobante->update(['xml_path' => $xmlPath]);
            Log::info('✅ XML guardado exitosamente', [
                'path' => $xmlPath,
                'size' => filesize($xmlFullPath) . ' bytes'
            ]);
        } else {
            Log::error('❌ XML no se guardó físicamente', ['path' => $xmlFullPath]);
        }

        // ==========================================
        // GUARDAR CDR (si fue aceptado)
        // ==========================================
        if ($result->isSuccess() && $result->getCdrZip()) {
            $cdrDir = storage_path("app/sunat/cdr/{$empresaId}");
            
            if (!is_dir($cdrDir)) {
                mkdir($cdrDir, 0755, true);
            }
            
            $cdrPath = "sunat/cdr/{$empresaId}/R-{$filename}.zip";
            $cdrFullPath = storage_path("app/{$cdrPath}");
            
            file_put_contents($cdrFullPath, $result->getCdrZip());
            
            $comprobante->update([
                'cdr_path' => $cdrPath,
                'hash_cdr' => $result->getCdrResponse()->getId()
            ]);
            
            Log::info('✅ CDR guardado', ['path' => $cdrPath]);
        }

        // ==========================================
        // GENERAR PDF
        // ==========================================
        $this->generarPDF($comprobante, $invoice);

    } catch (\Exception $e) {
        Log::error('Error al guardar archivos', [
            'comprobante_id' => $comprobante->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}



    /**
     * Generar PDF del comprobante
     */
    protected function generarPDF(Comprobante $comprobante, Invoice $invoice)
{
    try {
        // Crear directorio si no existe
        $pdfDir = storage_path("app/sunat/pdf/{$comprobante->empresa_id}");
        
        if (!is_dir($pdfDir)) {
            mkdir($pdfDir, 0755, true);
            Log::info("Directorio PDF creado: {$pdfDir}");
        }
        
        // Generar PDF usando vista Blade
        $pdf = \PDF::loadView('comprobantes.pdf', [
            'comprobante' => $comprobante,
            'empresa' => $this->empresa,
            'detalles' => $comprobante->venta->detalles()->with('repuesto')->get()
        ]);
        
        // Configurar PDF
        $pdf->setPaper('a4', 'portrait');
        
        // Guardar PDF
        $pdfPath = "sunat/pdf/{$comprobante->empresa_id}/{$comprobante->numero_completo}.pdf";
        $pdfFullPath = storage_path("app/{$pdfPath}");
        
        $pdf->save($pdfFullPath);
        
        // Actualizar comprobante con la ruta del PDF
        $comprobante->update(['pdf_path' => $pdfPath]);
        
        if (file_exists($pdfFullPath)) {
            Log::info('✅ PDF generado exitosamente', [
                'path' => $pdfPath,
                'size' => filesize($pdfFullPath) . ' bytes'
            ]);
        }
        
    } catch (\Exception $e) {
        Log::error('Error al generar PDF', [
            'comprobante_id' => $comprobante->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}


    /**
     * Convertir número a letras
     */
    protected function numeroALetras($numero)
    {
        $formatter = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
        $entero = floor($numero);
        $decimal = round(($numero - $entero) * 100);
        
        $letras = mb_strtoupper($formatter->format($entero));
        
        return "{$letras} CON " . str_pad($decimal, 2, '0', STR_PAD_LEFT) . "/100 SOLES";
    }
}
