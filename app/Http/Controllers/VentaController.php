<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Repuesto;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // 👈 AGREGAR ESTE IMPORT

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with('cliente')->latest()->get();

        return view('ventas.index', compact('ventas'));
    }

    public function create()
    {
        $repuestos = Repuesto::with('categoria')->get() ?? collect([]);
        $clientes  = Cliente::all() ?? collect([]);

        return view('ventas.create', compact('repuestos', 'clientes'));
    }

    public function store(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                // Crear la venta
                $venta = Venta::create([
                    'cliente_id' => $request->cliente_id ?? null,
                    'fecha'      => now(),
                    'total'      => $request->total,
                    'estado'     => 'pagado'
                ]);

                // Verificar que lleguen repuestos
                if (!$request->has('repuestos') || empty($request->repuestos)) {
                    throw new \Exception("No se seleccionó ningún repuesto para la venta.");
                }

                foreach ($request->repuestos as $rep) {
                    $repuesto = Repuesto::findOrFail($rep['id']);

                    // Validar stock
                    if ($rep['cantidad'] > $repuesto->cantidad) {
                        throw new \Exception("No hay suficiente stock de {$repuesto->nombre}");
                    }

                    // Crear detalle de venta
                    DetalleVenta::create([
                        'venta_id'        => $venta->id,
                        'repuesto_id'     => $repuesto->id,
                        'cantidad'        => $rep['cantidad'],
                        'precio_unitario' => $rep['precio'],
                        'subtotal'        => $rep['cantidad'] * $rep['precio'],
                    ]);

                    // Descontar stock
                    $repuesto->decrement('cantidad', $rep['cantidad']);
                }
            });

            return redirect()->route('ventas.index')
                ->with('success', '✅ Venta confirmada correctamente');

        } catch (\Exception $e) {
            return redirect()->route('ventas.index')
                ->with('error', '❌ Error al registrar la venta: ' . $e->getMessage());
        }
    }

    public function success()
    {
        return view('ventas.success');
    }

    // 👉 Endpoint AJAX para buscar repuestos
    public function buscarRepuesto(Request $request)
    {
        $query = $request->input('query');
        $repuestos = Repuesto::where('nombre', 'like', "%$query%")
            ->orWhereHas('categoria', function($q) use ($query) {
                $q->where('nombre', 'like', "%$query%");
            })
            ->take(10)
            ->get();

        return response()->json($repuestos);
    }

    public function anular($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $venta = Venta::with('detalles.repuesto')->findOrFail($id);

                if ($venta->estado === 'anulado') {
                    throw new \Exception("La venta ya está anulada.");
                }

                // Devolver stock de cada repuesto
                foreach ($venta->detalles as $detalle) {
                    $detalle->repuesto->increment('cantidad', $detalle->cantidad);
                }

                // Cambiar estado de la venta
                $venta->update(['estado' => 'anulado']);
            });

            return redirect()->route('ventas.index')
                ->with('success', '✅ Venta anulada y stock devuelto correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('ventas.index')
                ->with('error', '❌ No se pudo anular la venta: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $venta = Venta::with(['cliente', 'detalles.repuesto'])->findOrFail($id);

        return view('ventas.show', compact('venta'));
    }

    // 👉 Método para filtrar el historial de ventas de un cliente por fecha
    public function historialCliente(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        $ventas = Venta::with('detalles.repuesto')->where('cliente_id', $id);

        // Filtrar por fecha según el valor del filtro
        if ($request->has('filter') && $request->filter != 'all') {
            $filter = $request->filter;

            switch ($filter) {
                case 'today':
                    $ventas->whereDate('fecha', Carbon::today());
                    break;
                case 'yesterday':
                    $ventas->whereDate('fecha', Carbon::yesterday());
                    break;
                case 'last_7_days':
                    $ventas->whereBetween('fecha', [Carbon::now()->subDays(7), Carbon::now()]);
                    break;
                case 'last_30_days':
                    $ventas->whereBetween('fecha', [Carbon::now()->subDays(30), Carbon::now()]);
                    break;
                case 'this_week':
                    $ventas->whereBetween('fecha', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'last_week':
                    $ventas->whereBetween('fecha', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $ventas->whereMonth('fecha', Carbon::now()->month);
                    break;
                case 'last_month':
                    $ventas->whereMonth('fecha', Carbon::now()->subMonth()->month);
                    break;
                case 'this_year':
                    $ventas->whereYear('fecha', Carbon::now()->year);
                    break;
                case 'last_year':
                    $ventas->whereYear('fecha', Carbon::now()->subYear()->year);
                    break;
                case 'custom':
                    // Filtro personalizado por fechas
                    $startDate = $request->start_date;
                    $endDate = $request->end_date;
                    if ($startDate && $endDate) {
                        $ventas->whereBetween('fecha', [$startDate, $endDate]);
                    }
                    break;
            }
        }

        // Obtener las ventas filtradas
        $ventas = $ventas->get();

        return view('ventas.historial', compact('cliente', 'ventas'));
    } 
    
    public function detalles($id)
    {
        $venta = Venta::with(['cliente', 'detalles.repuesto'])->findOrFail($id);

        return response()->json([
            'id' => $venta->id,
            'fecha' => \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i'),
            'total' => number_format($venta->total, 2),
            'estado' => ucfirst($venta->estado),
            'cliente' => [
                'nombre' => $venta->cliente->nombre ?? 'Cliente General',
                'dni' => $venta->cliente->dni ?? 'No especificado',
                'telefono' => $venta->cliente->telefono ?? 'No especificado',
            ],
            'detalles' => $venta->detalles->map(function($detalle) {
                return [
                    'repuesto' => [
                        'nombre' => $detalle->repuesto->nombre,
                        'codigo' => $detalle->repuesto->codigo ?? 'N/A',
                    ],
                    'cantidad' => $detalle->cantidad,
                    'precio_unitario' => number_format($detalle->precio_unitario, 2),
                    'subtotal' => number_format($detalle->subtotal, 2),
                ];
            })
        ]);
    }

    /**
     * Cambiar estado de la venta
     */
    public function cambiarEstado(Request $request, $id)
    {
        try {
            $request->validate([
                'estado' => 'required|in:pendiente,pagado,anulado'
            ]);
            
            $venta = Venta::findOrFail($id);
            
            // Si se anula, restaurar stock
            if ($request->estado === 'anulado' && $venta->estado !== 'anulado') {
                foreach ($venta->detalles as $detalle) {
                    $repuesto = $detalle->repuesto;
                    $repuesto->cantidad += $detalle->cantidad;
                    $repuesto->save();
                }
            }
            
            $venta->estado = $request->estado;
            $venta->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar boleta térmica en PDF
     */
    public function generarBoleta($id)
    {
        try {
            // Cargar la venta con sus relaciones
            $venta = Venta::with(['cliente', 'detalles.repuesto'])
                ->findOrFail($id);
            
            // Configurar PDF para impresora térmica (80mm)
            $pdf = Pdf::loadView('ventas.boleta', [
                'venta' => $venta,
            ]);
            
            // Configuración del papel (80mm de ancho, altura automática)
            $pdf->setPaper([0, 0, 226.77, 841.89], 'portrait'); // 80mm x 297mm en puntos
            
            // Nombre del archivo
            $filename = 'Boleta_' . str_pad($venta->id, 8, '0', STR_PAD_LEFT) . '.pdf';
            
            // Retornar PDF para mostrar en navegador
            return $pdf->stream($filename);
            
        } catch (\Exception $e) {
            return redirect()->route('ventas.index')
                ->with('error', 'Error al generar la boleta: ' . $e->getMessage());
        }
    }
    
    /**
     * Generar ticket simplificado (aún más compacto)
     */
    public function generarTicket($id)
    {
        try {
            $venta = Venta::with(['cliente', 'detalles.repuesto'])
                ->findOrFail($id);
            
            // Vista simple para tickets
            return view('ventas.ticket', compact('venta'));
            
        } catch (\Exception $e) {
            return redirect()->route('ventas.index')
                ->with('error', 'Error al generar el ticket: ' . $e->getMessage());
        }
    }
}