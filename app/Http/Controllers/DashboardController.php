<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use App\Models\Repuesto;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();
        $inicioMesAnterior = Carbon::now()->subMonthNoOverflow()->startOfMonth();
        $finMesAnterior = Carbon::now()->subMonthNoOverflow()->endOfMonth();

        $pagadas = fn () => Venta::where('estado', 'pagado');

        $ventasHoy = (float) $pagadas()->whereBetween('fecha', [$hoy, $hoy->copy()->endOfDay()])->sum('total');
        $cantidadHoy = $pagadas()->whereBetween('fecha', [$hoy, $hoy->copy()->endOfDay()])->count();
        $ventasMes = (float) $pagadas()->where('fecha', '>=', $inicioMes)->sum('total');
        $cantidadMes = $pagadas()->where('fecha', '>=', $inicioMes)->count();
        $ventasMesAnterior = (float) $pagadas()->whereBetween('fecha', [$inicioMesAnterior, $finMesAnterior])->sum('total');

        $variacionMes = $ventasMesAnterior > 0
            ? (($ventasMes - $ventasMesAnterior) / $ventasMesAnterior) * 100
            : null;

        $ticketPromedio = $cantidadMes > 0 ? $ventasMes / $cantidadMes : 0;

        // Inventario
        $inventario = Repuesto::productos()
            ->selectRaw('COUNT(*) as total_items')
            ->selectRaw('COALESCE(SUM(cantidad * precio_unitario), 0) as valor')
            ->selectRaw('SUM(CASE WHEN cantidad <= 0 THEN 1 ELSE 0 END) as agotados')
            ->selectRaw('SUM(CASE WHEN cantidad > 0 AND cantidad <= minimo_stock THEN 1 ELSE 0 END) as stock_bajo')
            ->first();

        $alertasStock = Repuesto::with('categoria')
            ->conStockBajo()
            ->orderBy('cantidad')
            ->limit(6)
            ->get();

        // Ventas de los últimos 7 días (una sola consulta)
        $desde = $hoy->copy()->subDays(6);
        $porDia = $pagadas()
            ->where('fecha', '>=', $desde)
            ->get(['fecha', 'total'])
            ->groupBy(fn ($v) => $v->fecha->toDateString())
            ->map(fn ($grupo) => (float) $grupo->sum('total'));

        $ultimos7Dias = collect(range(0, 6))->map(function ($i) use ($desde, $porDia) {
            $dia = $desde->copy()->addDays($i);

            return [
                'etiqueta' => ucfirst($dia->locale('es')->isoFormat('ddd')),
                'fecha' => $dia->format('d/m'),
                'total' => $porDia[$dia->toDateString()] ?? 0,
            ];
        });
        $maxDia = max(1, $ultimos7Dias->max('total'));

        $ultimasVentas = Venta::with('cliente')
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $comprobantesConProblemas = Comprobante::whereIn('estado_sunat', ['pendiente', 'rechazado', 'baja_pendiente'])->count();

        $topProductos = DB::table('detalle_ventas')
            ->join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->join('repuestos', 'repuestos.id', '=', 'detalle_ventas.repuesto_id')
            ->where('ventas.empresa_id', auth()->user()->empresa_id)
            ->where('ventas.estado', 'pagado')
            ->where('ventas.fecha', '>=', $inicioMes)
            ->groupBy('repuestos.id', 'repuestos.nombre')
            ->select('repuestos.nombre', DB::raw('SUM(detalle_ventas.cantidad) as unidades'), DB::raw('SUM(detalle_ventas.subtotal) as total'))
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'ventasHoy', 'cantidadHoy', 'ventasMes', 'cantidadMes', 'variacionMes', 'ticketPromedio',
            'inventario', 'alertasStock', 'ultimos7Dias', 'maxDia', 'ultimasVentas',
            'comprobantesConProblemas', 'topProductos'
        ));
    }
}
