<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Repuesto;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        'fecha' => \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i'),
        'total' => $venta->total,
        'estado' => ucfirst($venta->estado),
        'cliente' => $venta->cliente,
        'detalles' => $venta->detalles
    ]);
}



}
