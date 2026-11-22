<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use App\Models\Venta;
use App\Services\FacturacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ComprobanteController extends Controller
{
    public function __construct(protected FacturacionService $facturacionService)
    {
    }

    public function index(Request $request)
    {
        $query = Comprobante::with(['venta', 'cliente', 'usuarioEmision'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('tipo')) {
            $query->where('tipo_comprobante', $request->tipo);
        }

        if ($request->filled('estado')) {
            $query->where('estado_sunat', $request->estado);
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_emision', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_emision', '<=', $request->fecha_fin);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->whereLike('numero_completo', "%{$buscar}%")
                  ->orWhereLike('cliente_nombre', "%{$buscar}%")
                  ->orWhereLike('cliente_num_doc', "%{$buscar}%");
            });
        }

        $comprobantes = $query->paginate(20)->withQueryString();

        return view('comprobantes.index', compact('comprobantes'));
    }

    public function show(Comprobante $comprobante)
    {
        $this->autorizar($comprobante);

        $comprobante->load(['venta.detalles.repuesto', 'cliente', 'usuarioEmision']);

        return view('comprobantes.show', compact('comprobante'));
    }

    public function emitir(Request $request, Venta $venta)
    {
        abort_unless((int) $venta->empresa_id === (int) auth()->user()->empresa_id, 403);

        $request->validate([
            'tipo_comprobante' => 'nullable|in:01,03',
        ]);

        $resultado = $this->facturacionService->emitirComprobante(
            $venta,
            $request->input('tipo_comprobante') ?: null,
            auth()->id()
        );

        if ($resultado['comprobante']) {
            return redirect()
                ->route('comprobantes.show', $resultado['comprobante'])
                ->with($resultado['success'] ? 'success' : 'error', $resultado['mensaje']);
        }

        return back()->with('error', $resultado['mensaje']);
    }

    public function descargarXml(Comprobante $comprobante)
    {
        return $this->descargar($comprobante, 'xml_path', $comprobante->numero_completo . '.xml', 'application/xml');
    }

    public function descargarCdr(Comprobante $comprobante)
    {
        return $this->descargar($comprobante, 'cdr_path', 'R-' . $comprobante->numero_completo . '.zip', 'application/zip');
    }

    public function descargarPdf(Comprobante $comprobante)
    {
        return $this->descargar($comprobante, 'pdf_path', $comprobante->numero_completo . '.pdf', 'application/pdf');
    }

    public function verPdf(Comprobante $comprobante)
    {
        $this->autorizar($comprobante);

        $ruta = $comprobante->rutaArchivo('pdf_path');

        return $ruta
            ? response()->file($ruta, ['Content-Type' => 'application/pdf'])
            : back()->with('error', 'PDF no disponible.');
    }

    /**
     * Consulta/actualiza el estado:
     * - Baja en proceso: consulta el ticket en SUNAT.
     * - Pendiente (sin respuesta): reenvía el XML.
     * - Otros: devuelve el estado guardado.
     */
    public function consultarEstado(Request $request, Comprobante $comprobante)
    {
        $this->autorizar($comprobante);

        try {
            if ($comprobante->ticket_baja && $comprobante->estado_sunat === 'baja_pendiente') {
                $resultado = $this->facturacionService->consultarBaja($comprobante);
            } elseif ($comprobante->estado_sunat === 'pendiente') {
                $resultado = $this->facturacionService->reenviarPendiente($comprobante);
            } else {
                $resultado = ['success' => true, 'mensaje' => $comprobante->mensaje_sunat ?? 'Sin cambios.'];
            }

            $comprobante->refresh();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => $resultado['success'],
                    'estado' => $comprobante->estado_sunat,
                    'mensaje' => $resultado['mensaje'],
                ]);
            }

            return back()->with($resultado['success'] ? 'success' : 'error', $resultado['mensaje']);
        } catch (\Throwable $e) {
            Log::error('Error al consultar estado', ['comprobante_id' => $comprobante->id, 'error' => $e->getMessage()]);

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'mensaje' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Error al consultar: ' . $e->getMessage());
        }
    }

    /**
     * Anula el comprobante enviando la Comunicación de Baja (facturas)
     * o el Resumen Diario de anulación (boletas) a SUNAT.
     */
    public function anular(Request $request, Comprobante $comprobante)
    {
        $this->autorizar($comprobante);

        $request->validate([
            'motivo_anulacion' => 'required|string|min:10|max:100',
        ], [
            'motivo_anulacion.required' => 'Debes indicar el motivo de anulación',
            'motivo_anulacion.min' => 'El motivo debe tener al menos 10 caracteres',
            'motivo_anulacion.max' => 'El motivo no puede superar los 100 caracteres',
        ]);

        try {
            $resultado = $this->facturacionService->anularComprobante($comprobante, $request->motivo_anulacion, auth()->id());

            Log::info('Anulación de comprobante enviada', [
                'comprobante_id' => $comprobante->id,
                'usuario_id' => auth()->id(),
            ]);

            return back()->with($resultado['success'] ? 'success' : 'error', $resultado['mensaje']);
        } catch (\Throwable $e) {
            Log::error('Error al anular comprobante', ['comprobante_id' => $comprobante->id, 'error' => $e->getMessage()]);

            return back()->with('error', 'Error al anular: ' . $e->getMessage());
        }
    }

    private function autorizar(Comprobante $comprobante): void
    {
        abort_unless((int) $comprobante->empresa_id === (int) auth()->user()->empresa_id, 403);
    }

    private function descargar(Comprobante $comprobante, string $campo, string $nombre, string $mime)
    {
        $this->autorizar($comprobante);

        $ruta = $comprobante->rutaArchivo($campo);

        if (! $ruta) {
            return back()->with('error', 'El archivo no está disponible en el servidor.');
        }

        return response()->download($ruta, $nombre, ['Content-Type' => $mime]);
    }
}
