<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Repuesto;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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


    public function historialCliente($id)
{
    $cliente = Cliente::findOrFail($id);

    // Trae todas las ventas de este cliente con sus detalles y repuestos
    $ventas = Venta::with('detalles.repuesto')
                    ->where('cliente_id', $id)
                    ->get();

    return view('ventas.historial', compact('cliente', 'ventas'));
}






}
