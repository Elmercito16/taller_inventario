<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        // 1. Rango de fechas
        $rango = $request->get('rango', 'mes');
        [$fechaInicio, $fechaFin] = match ($rango) {
            'hoy' => [Carbon::today(), Carbon::today()->endOfDay()],
            'semana' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'anio' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            'personalizado' => $request->filled(['fecha_inicio', 'fecha_fin'])
                ? [Carbon::parse($request->fecha_inicio)->startOfDay(), Carbon::parse($request->fecha_fin)->endOfDay()]
                : [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };

        // 2. Ventas pagadas del rango (filtradas por empresa automáticamente)
        $ventas = Venta::where('estado', 'pagado')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->get(['id', 'cliente_id', 'total', 'metodo_pago']);

        $detalles = DetalleVenta::whereIn('venta_id', $ventas->pluck('id'))
            ->with(['repuesto.categoria'])
            ->get();

        // 3. Resumen
        $totalIngresos = (float) $ventas->sum('total');
        $cantidadVentas = $ventas->count();

        // 4. Ganancia REAL: (precio de venta - costo) * cantidad.
        //    Usa el costo guardado en la venta o, si no existe, el precio de compra actual.
        $itemsSinCosto = 0;
        $gananciaEstimada = $detalles->sum(function ($detalle) use (&$itemsSinCosto) {
            $costo = $detalle->costo_unitario ?? $detalle->repuesto?->precio_compra;

            if ($costo === null) {
                $itemsSinCosto++;

                return 0;
            }

            return ((float) $detalle->precio_unitario - (float) $costo) * $detalle->cantidad;
        });

        $ventasConCosto = $detalles
            ->filter(fn ($d) => ($d->costo_unitario ?? $d->repuesto?->precio_compra) !== null)
            ->sum('subtotal');
        $margenPorcentaje = $ventasConCosto > 0 ? ($gananciaEstimada / $ventasConCosto) * 100 : 0;

        // 5. Métodos de pago (reales)
        $metodosPago = collect(Venta::METODOS_PAGO)
            ->mapWithKeys(fn ($nombre, $clave) => [
                $nombre => (float) $ventas->where('metodo_pago', $clave)->sum('total')
                    + ($clave === 'efectivo' ? (float) $ventas->whereNull('metodo_pago')->sum('total') : 0),
            ])
            ->all();

        // 6. Top productos
        $topProductos = $detalles->groupBy('repuesto_id')
            ->map(function ($items) {
                $repuesto = $items->first()->repuesto;

                return [
                    'nombre' => $repuesto->nombre ?? 'Desconocido',
                    'codigo' => $repuesto->codigo ?? 'N/A',
                    'cantidad_vendida' => $items->sum('cantidad'),
                    'total_generado' => $items->sum('subtotal'),
                ];
            })
            ->sortByDesc('total_generado')
            ->take(5);

        // 7. Top clientes
        $topClientes = Venta::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', 'pagado')
            ->whereNotNull('cliente_id')
            ->select('cliente_id', DB::raw('SUM(total) as total_gastado'), DB::raw('COUNT(*) as cantidad_compras'))
            ->with('cliente:id,nombre')
            ->groupBy('cliente_id')
            ->orderBy('total_gastado', 'desc')
            ->limit(5)
            ->get()
            ->map(fn ($venta) => [
                'nombre' => $venta->cliente->nombre ?? 'Cliente sin nombre',
                'total_gastado' => $venta->total_gastado,
                'cantidad_compras' => $venta->cantidad_compras,
            ]);

        // 8. Top categorías
        $topCategorias = $detalles->groupBy(fn ($d) => $d->repuesto?->categoria?->nombre ?? 'Sin Categoría')
            ->map(fn ($items, $categoriaNombre) => [
                'nombre' => $categoriaNombre,
                'cantidad_items' => $items->sum('cantidad'),
                'total_generado' => $items->sum('subtotal'),
            ])
            ->sortByDesc('total_generado')
            ->take(5);

        return view('reportes.index', compact(
            'rango', 'topClientes', 'fechaInicio', 'fechaFin',
            'totalIngresos', 'cantidadVentas', 'gananciaEstimada',
            'margenPorcentaje', 'itemsSinCosto',
            'metodosPago', 'topProductos', 'topCategorias',
        ));
    }
}
