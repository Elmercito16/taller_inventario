<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Repuesto;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule; // 1. ¡IMPORTAR LA CLASE RULE!

class VentaController extends Controller
{
    /**
     * ¡MAGIA! Esta función ya es multi-tenant.
     * Venta::with() es filtrado automáticamente por el Trait.
     */
    public function index()
    {
        $ventas = Venta::with('cliente')->latest()->get();

        return view('ventas.index', compact('ventas'));
    }

    /**
     * ¡MAGIA! Esta función ya es multi-tenant.
     * Repuesto::...get() y Cliente::all() son filtrados automáticamente.
     */
    public function create()
    {
        $repuestos = Repuesto::with('categoria')->get() ?? collect([]);
        $clientes  = Cliente::all() ?? collect([]);

        return view('ventas.create', compact('repuestos', 'clientes'));
    }

    public function store(Request $request)
    {
        // 2. ¡VALIDACIÓN DE SEGURIDAD MULTI-TENANT AÑADIDA!
        // Esto se ejecuta ANTES de que comience la transacción.
        try {
            $validated = $request->validate([
                'total' => 'required|numeric|min:0',

                // Valida que el cliente_id exista Y pertenezca a la empresa del usuario
                'cliente_id' => [
                    'nullable', // 'nullable' ya que tu código lo permite
                    Rule::exists('clientes', 'id')->where(function ($query) {
                        return $query->where('empresa_id', auth()->user()->empresa_id);
                    })
                ],

                // Valida que el array de repuestos exista
                'repuestos' => 'required|array|min:1',

                // Valida CADA item dentro del array de repuestos
                'repuestos.*.id' => [
                    'required',
                    // Valida que el repuesto_id exista Y pertenezca a la empresa del usuario
                    Rule::exists('repuestos', 'id')->where(function ($query) {
                        return $query->where('empresa_id', auth()->user()->empresa_id);
                    })
                ],
                'repuestos.*.cantidad' => 'required|integer|min:1',
                'repuestos.*.precio' => 'required|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si la validación falla, regresa con el error.
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        }

        // --- TU LÓGICA DE TRANSACCIÓN (SIN CAMBIOS) ---
        try {
            DB::transaction(function () use ($request) {
                // Crear la venta
                // ¡MAGIA! 'Venta::create' añadirá 'empresa_id' automáticamente.
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
                    // Ahora esta línea es segura, porque el ID ya fue validado.
                    $repuesto = Repuesto::findOrFail($rep['id']);

                    // Validar stock
                    if ($rep['cantidad'] > $repuesto->cantidad) {
                        throw new \Exception("No hay suficiente stock de {$repuesto->nombre}");
                    }

                    // Crear detalle de venta
                    // ¡MAGIA! 'DetalleVenta::create' añadirá 'empresa_id' automáticamente.
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

    // ¡MAGIA! Esta función ya es multi-tenant.
    public function buscarRepuesto(Request $request)
    {
        $query = $request->input('query');
        
        // Repuesto::where() es filtrado automáticamente por 'empresa_id'
        $repuestos = Repuesto::where('nombre', 'like', "%$query%")
            ->orWhereHas('categoria', function($q) use ($query) {
                $q->where('nombre', 'like', "%$query%");
            })
            ->take(10)
            ->get();

        return response()->json($repuestos);
    }

    // ¡MAGIA! Esta función ya es multi-tenant.
    // Venta::...findOrFail() solo encontrará ventas de esta empresa.
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
                    // $detalle->repuesto está seguro porque la venta fue encontrada
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

    // ¡MAGIA! Esta función ya es multi-tenant.
    public function show($id)
    {
        $venta = Venta::with(['cliente', 'detalles.repuesto'])->findOrFail($id);

        return view('ventas.show', compact('venta'));
    }

    // ¡MAGIA! Esta función ya es multi-tenant.
    public function historialCliente(Request $request, $id)
    {
        // Cliente::findOrFail() solo encontrará clientes de esta empresa.
        $cliente = Cliente::findOrFail($id);

        // Venta::where() es filtrado automáticamente por 'empresa_id'
        $ventas = Venta::with('detalles.repuesto')->where('cliente_id', $id);

        // ... (El resto de tu lógica de filtros de fecha)
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
    
    // ¡MAGIA! Esta función ya es multi-tenant.
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
     * ¡MAGIA! Esta función ya es multi-tenant.
     */
    public function cambiarEstado(Request $request, $id)
    {
        try {
            $request->validate([
                'estado' => 'required|in:pendiente,pagado,anulado'
            ]);
            
            // Venta::findOrFail() solo encontrará la venta si pertenece a esta empresa
            $venta = Venta::findOrFail($id);
            
            // Si se anula, restaurar stock
            if ($request->estado === 'anulado' && $venta->estado !== 'anulado') {
                foreach ($venta->detalles as $detalle) {
                    $repuesto = $detalle->repuesto; // $detalle ya pertenece a la empresa
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
     * ¡MAGIA! Esta función ya es multi-tenant.
     */
    public function generarBoleta($id)
    {
        try {
            // Cargar la venta con sus relaciones
            // Venta::findOrFail() solo encontrará la venta si pertenece a esta empresa
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
     * ¡MAGIA! Esta función ya es multi-tenant.
     */
    public function generarTicket($id)
    {
        try {
            // Venta::findOrFail() solo encontrará la venta si pertenece a esta empresa
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