<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use App\Models\Venta;
use App\Services\FacturacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ComprobanteController extends Controller
{
    protected $facturacionService;

    public function __construct(FacturacionService $facturacionService)
    {
        $this->facturacionService = $facturacionService;
    }

    /**
     * Listar comprobantes
     */
    public function index(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;

        $query = Comprobante::with(['venta', 'cliente', 'usuarioEmision'])
            ->where('empresa_id', $empresaId)
            ->orderBy('created_at', 'desc');

        // Filtros
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
            $query->where(function($q) use ($buscar) {
                $q->where('numero_completo', 'like', "%{$buscar}%")
                  ->orWhere('cliente_nombre', 'like', "%{$buscar}%")
                  ->orWhere('cliente_num_doc', 'like', "%{$buscar}%");
            });
        }

        $comprobantes = $query->paginate(20);

        return view('comprobantes.index', compact('comprobantes'));
    }

    /**
     * Ver detalle del comprobante
     */
    public function show(Comprobante $comprobante)
    {
        // Verificar que el comprobante pertenece a la empresa del usuario
        if ($comprobante->empresa_id !== auth()->user()->empresa_id) {
            abort(403, 'No autorizado');
        }

        $comprobante->load(['venta.detalles.repuesto', 'cliente', 'usuarioEmision']);

        return view('comprobantes.show', compact('comprobante'));
    }

    /**
     * Emitir comprobante desde una venta
     */
    public function emitir(Request $request, Venta $venta)
{
    // 👇 AGREGAR ESTO TEMPORALMENTE PARA DEBUG
   
    // Verificar que la venta pertenece a la empresa del usuario
    if ($venta->empresa_id !== auth()->user()->empresa_id) {
        abort(403, 'No autorizado');
    }

    try {
        // Auto-detectar tipo de comprobante si no se envió
        $tipoComprobante = $request->input('tipo_comprobante');
        
        if (!$tipoComprobante) {
            // Si el cliente tiene RUC, emitir factura, sino boleta
            $tipoComprobante = $venta->cliente && $venta->cliente->ruc ? '01' : '03';
        }

        // Validar que sea un tipo válido
        if (!in_array($tipoComprobante, ['01', '03'])) {
            return back()->with('error', 'Tipo de comprobante inválido');
        }

        // Emitir comprobante pasando el ID del usuario autenticado
        $resultado = $this->facturacionService->emitirComprobante(
            $venta, 
            $tipoComprobante, 
            auth()->id() // 👈 AGREGAR ESTO
        );

        if ($resultado['success']) {
            return redirect()
                ->route('comprobantes.show', $resultado['comprobante'])
                ->with('success', '✅ ' . $resultado['mensaje']);
        } else {
            return back()
                ->with('error', '❌ ' . $resultado['mensaje'])
                ->withInput();
        }

    } catch (\Exception $e) {
        Log::error('Error en controlador al emitir comprobante', [
            'venta_id' => $venta->id,
            'error' => $e->getMessage()
        ]);

        return back()
            ->with('error', '❌ Error al emitir comprobante: ' . $e->getMessage())
            ->withInput();
    }
}


    /**
     * Descargar XML
     */
    public function descargarXml(Comprobante $comprobante)
{
    // Verificar que existe el XML path
    if (!$comprobante->xml_path) {
        return back()->with('error', 'Este comprobante no tiene XML generado');
    }

    // Construir ruta completa (sin usar Storage para evitar problemas en Windows)
    $rutaCompleta = storage_path('app/' . $comprobante->xml_path);

    // Verificar que el archivo existe físicamente
    if (!file_exists($rutaCompleta)) {
        return back()->with('error', 'Archivo XML no encontrado en el servidor');
    }

    // Descargar el archivo
    return response()->download(
        $rutaCompleta,
        $comprobante->numero_completo . '.xml',
        ['Content-Type' => 'application/xml']
    );
}





    /**
     * Descargar PDF
     */
   public function descargarPdf(Comprobante $comprobante)
{
    // Verificar permisos
    if ($comprobante->empresa_id !== auth()->user()->empresa_id) {
        abort(403);
    }

    // Verificar que tiene PDF generado
    if (!$comprobante->pdf_path) {
        return back()->with('error', 'Este comprobante no tiene PDF generado');
    }

    // Construir ruta completa
    $rutaCompleta = storage_path('app/' . $comprobante->pdf_path);

    // Verificar que el archivo existe
    if (!file_exists($rutaCompleta)) {
        return back()->with('error', 'Archivo PDF no encontrado en el servidor');
    }

    // Descargar el archivo
    return response()->download(
        $rutaCompleta,
        $comprobante->numero_completo . '.pdf',
        ['Content-Type' => 'application/pdf']
    );
}


    /**
     * Descargar CDR
     */
    public function descargarCdr(Comprobante $comprobante)
{
    if (!$comprobante->cdr_path) {
        return back()->with('error', 'Este comprobante no tiene CDR (Constancia de Recepción)');
    }

    $rutaCompleta = storage_path('app/' . $comprobante->cdr_path);

    if (!file_exists($rutaCompleta)) {
        return back()->with('error', 'Archivo CDR no encontrado');
    }

    return response()->download(
        $rutaCompleta,
        'R-' . $comprobante->numero_completo . '.zip',
        ['Content-Type' => 'application/zip']
    );
}


    /**
     * Consultar estado en SUNAT
     */
    public function consultarEstado(Comprobante $comprobante)
    {
        // Verificar permisos
        if ($comprobante->empresa_id !== auth()->user()->empresa_id) {
            abort(403);
        }

        try {
            // Aquí implementaremos la consulta de estado
            // Por ahora retornamos el estado actual
            
            return response()->json([
                'success' => true,
                'estado' => $comprobante->estado_sunat,
                'mensaje' => $comprobante->mensaje_sunat,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Anular comprobante
     */
    public function anular(Request $request, Comprobante $comprobante)
    {
        // Verificar permisos
        if ($comprobante->empresa_id !== auth()->user()->empresa_id) {
            abort(403);
        }

        // Validar
        $request->validate([
            'motivo_anulacion' => 'required|string|min:10|max:500',
        ], [
            'motivo_anulacion.required' => 'Debes indicar el motivo de anulación',
            'motivo_anulacion.min' => 'El motivo debe tener al menos 10 caracteres',
        ]);

        try {
            // Verificar que puede ser anulado
            if (!$comprobante->puedeSerAnulado()) {
                return back()->with('error', 'Este comprobante no puede ser anulado. Solo se pueden anular comprobantes del mismo día y sin notas asociadas.');
            }

            // Marcar como anulado
            $comprobante->update([
                'anulado' => true,
                'fecha_anulacion' => now(),
                'motivo_anulacion' => $request->motivo_anulacion,
                'usuario_anulacion_id' => auth()->id(),
                'estado_sunat' => 'anulado',
            ]);

            Log::info('Comprobante anulado', [
                'comprobante_id' => $comprobante->id,
                'usuario_id' => auth()->id(),
                'motivo' => $request->motivo_anulacion,
            ]);

            return back()->with('success', 'Comprobante anulado correctamente. Se enviará la comunicación de baja a SUNAT.');

        } catch (\Exception $e) {
            Log::error('Error al anular comprobante', [
                'comprobante_id' => $comprobante->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Error al anular: ' . $e->getMessage());
        }
    }

    /**
     * Ver PDF en navegador
     */
    public function verPdf(Comprobante $comprobante)
    {
        // Verificar permisos
        if ($comprobante->empresa_id !== auth()->user()->empresa_id) {
            abort(403);
        }

        if ($comprobante->pdf_path && \Storage::exists($comprobante->pdf_path)) {
            return response()->file(storage_path('app/' . $comprobante->pdf_path));
        }

        return back()->with('error', 'PDF no disponible');
    }
}
