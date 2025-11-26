<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        // 1. Configuración de Filtros de Fecha
        $rango = $request->get('rango', 'mes'); // Por defecto: mes actual
        $fechaInicio = Carbon::now()->startOfMonth();
        $fechaFin = Carbon::now()->endOfMonth();

        if ($rango === 'hoy') {
            $fechaInicio = Carbon::today();
            $fechaFin = Carbon::today()->endOfDay();
        } elseif ($rango === 'semana') {
            $fechaInicio = Carbon::now()->startOfWeek();
            $fechaFin = Carbon::now()->endOfWeek();
        } elseif ($rango === 'anio') {
            $fechaInicio = Carbon::now()->startOfYear();
            $fechaFin = Carbon::now()->endOfYear();
        } elseif ($rango === 'personalizado' && $request->fecha_inicio && $request->fecha_fin) {
            $fechaInicio = Carbon::parse($request->fecha_inicio)->startOfDay();
            $fechaFin = Carbon::parse($request->fecha_fin)->endOfDay();
        }

        // 2. Consultas Base (El Multi-tenant filtra automáticamente)
        
        // Ventas completadas en el rango
        $ventas = Venta::where('estado', 'pagado')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->get();

        // Detalles de esas ventas (para productos y categorías)
        // Usamos whereIn para filtrar detalles que pertenezcan a las ventas filtradas
        $detalles = DetalleVenta::whereIn('venta_id', $ventas->pluck('id'))
            ->with(['repuesto.categoria']) // Cargar relaciones para evitar N+1
            ->get();

        // 3. Cálculos para el Resumen de Ventas
        $totalIngresos = $ventas->sum('total');
        $cantidadVentas = $ventas->count();
        
        // Cálculo de Ganancia Estimada
        // Nota: Si no tienes un campo 'costo' en tus repuestos, esto será igual al ingreso.
        // Si lo tienes, la fórmula sería: (precio_venta - costo) * cantidad
        // Aquí asumiremos un margen simple del 30% para el ejemplo visual, 
        // o puedes ajustar si añades el campo 'costo' a tu tabla repuestos.
        $gananciaEstimada = $totalIngresos * 0.30; 

        // 4. Método de Pago (Simulado si no tienes la columna, o real si la añades)
        // Como tu tabla 'ventas' actual no tiene 'metodo_pago', 
        // asumiremos 'Efectivo' por defecto o lo calcularemos si lo añades después.
        $metodosPago = [
            'Efectivo' => $totalIngresos, // Por ahora todo es efectivo
            'Yape/Plin' => 0,
            'Tarjeta' => 0
        ];

        // 5. Mejores Productos (Top 5)
        $topProductos = $detalles->groupBy('repuesto_id')
            ->map(function ($items) {
                $repuesto = $items->first()->repuesto;
                return [
                    'nombre' => $repuesto ? $repuesto->nombre : 'Desconocido',
                    'codigo' => $repuesto ? $repuesto->codigo : 'N/A',
                    'cantidad_vendida' => $items->sum('cantidad'),
                    'total_generado' => $items->sum('subtotal')
                ];
            })
            ->sortByDesc('total_generado') // Ordenar por dinero generado
            ->take(5);

        // 6. Mejores Categorías
        $topCategorias = $detalles->groupBy(function ($detalle) {
                return $detalle->repuesto && $detalle->repuesto->categoria 
                    ? $detalle->repuesto->categoria->nombre 
                    : 'Sin Categoría';
            })
            ->map(function ($items, $categoriaNombre) {
                return [
                    'nombre' => $categoriaNombre,
                    'cantidad_items' => $items->sum('cantidad'),
                    'total_generado' => $items->sum('subtotal')
                ];
            })
            ->sortByDesc('total_generado')
            ->take(5);

        return view('reportes.index', compact(
            'rango', 'fechaInicio', 'fechaFin',
            'totalIngresos', 'cantidadVentas', 'gananciaEstimada',
            'metodosPago', 'topProductos', 'topCategorias'
        ));
    }
}