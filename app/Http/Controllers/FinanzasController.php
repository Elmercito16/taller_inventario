<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Gasto;
use App\Models\Ingreso; // 👈 Añadir esta línea
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator; // 👈 Añadir esta línea


class FinanzasController extends Controller
{
    public function index(Request $request)
    {
        // 1. Filtros de Fecha
$filtro = $request->get('filtro', 'mes_actual');

// Por defecto: mes actual
$fechaInicio = Carbon::now()->startOfMonth();
$fechaFin = Carbon::now()->endOfMonth();

if ($filtro === 'hoy') {
    $fechaInicio = Carbon::today();
    $fechaFin = Carbon::today()->endOfDay();
} elseif ($filtro === 'semana') {
    $fechaInicio = Carbon::now()->startOfWeek();
    $fechaFin = Carbon::now()->endOfWeek();
} elseif ($filtro === 'anio_actual') {
    $fechaInicio = Carbon::now()->startOfYear();
    $fechaFin = Carbon::now()->endOfYear();
} elseif ($filtro === 'personalizado' && $request->fecha_inicio && $request->fecha_fin) {
    $fechaInicio = Carbon::parse($request->fecha_inicio)->startOfDay();
    $fechaFin = Carbon::parse($request->fecha_fin)->endOfDay();
}


        // 2. Obtener Datos (El Multi-tenant filtra automáticamente por empresa)
        
        // Ingresos por Ventas (Ventas Pagadas)
        $ventas = Venta::where('estado', 'pagado')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->with('cliente')
            ->orderBy('fecha', 'desc')
            ->get();

        // Otros Ingresos (no por ventas)
        $ingresos = Ingreso::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->orderBy('fecha', 'desc')
            ->get();

        // Gastos
        $gastos = Gasto::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->orderBy('fecha', 'desc')
            ->get();

        // 3. Calcular Totales
        $totalIngresos = $ventas->sum('total') + $ingresos->sum('monto');
        $totalGastos = $gastos->sum('monto');
        $balance = $totalIngresos - $totalGastos;

        // 4. Crear Caja de Flujo Unificada (Merge)
        $flujoVentas = $ventas->map(function ($v) {
            return [
                'id' => $v->id,
                'tipo' => 'ingreso',
                'descripcion' => 'Venta #' . str_pad($v->id, 6, '0', STR_PAD_LEFT) . ($v->cliente ? ' - ' . $v->cliente->nombre : ''),
                'monto' => $v->total,
                'fecha' => $v->fecha,
                'modelo' => 'Venta'
            ];
        });

        $flujoIngresos = $ingresos->map(function ($i) {
            return [
                'id' => $i->id,
                'tipo' => 'ingreso',
                'descripcion' => $i->descripcion . ($i->tipo ? ' (' . $i->tipo . ')' : ''),
                'monto' => $i->monto,
                'fecha' => $i->fecha,
                'modelo' => 'Ingreso'
            ];
        });

        $flujoGastos = $gastos->map(function ($g) {
            return [
                'id' => $g->id,
                'tipo' => 'gasto',
                'descripcion' => $g->descripcion . ($g->categoria ? ' (' . $g->categoria . ')' : ''),
                'monto' => $g->monto,
                'fecha' => $g->fecha,
                'modelo' => 'Gasto'
            ];
        });

        // Unimos todos los flujos y ordenamos por fecha descendente
        $cajaFlujo = $flujoVentas->concat($flujoIngresos)->concat($flujoGastos)->sortByDesc('fecha');

        // Unimos y ordenamos por fecha descendente
$cajaFlujo = $flujoVentas->concat($flujoIngresos)->concat($flujoGastos)->sortByDesc('fecha');

// 👇 AÑADE ESTAS LÍNEAS AQUÍ
              $tipoMovimiento = $request->get('tipo_movimiento', 'todos');

                if ($tipoMovimiento === 'ingresos') {
                   $cajaFlujo = $cajaFlujo->filter(function ($item) {
                    return $item['tipo'] === 'ingreso';
                 });    
                 } elseif ($tipoMovimiento === 'gastos') {
                   $cajaFlujo = $cajaFlujo->filter(function ($item) {
                    return $item['tipo'] === 'gasto';
                    });
            }
// 👆 HASTA AQUÍ
// 👇 PAGINACIÓN AÑADIDA AQUÍ
        // Configuración de paginación
        $perPage = 5; // Registros por página
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $cajaFlujo->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $cajaFlujo = new LengthAwarePaginator(
            $currentItems,
            $cajaFlujo->count(),
            $perPage,
            $currentPage,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );
        // 👆 HASTA AQUÍ

        return view('finanzas.index', compact(
            'totalIngresos', 
            'totalGastos', 
            'balance', 
            'cajaFlujo',
            'filtro',
            'fechaInicio',  
            'fechaFin'
        ));
    }

    public function storeGasto(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
            'fecha' => 'required|date',
            'categoria' => 'nullable|string'
        ]);

        // UsesTenantModel llena empresa_id automático
        Gasto::create($request->all());

        return redirect()->back()->with('success', 'Gasto registrado correctamente.');
    }

    public function storeIngreso(Request $request)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'descripcion' => 'required|string|max:255',
            'fecha' => 'required|date',
            'tipo_ingreso' => 'required|string'
        ]);

        // El scope global se encarga de asignar empresa_id automáticamente
        Ingreso::create([
            'monto' => $request->monto,
            'descripcion' => $request->descripcion,
            'fecha' => $request->fecha,
            'tipo' => $request->tipo_ingreso,
        ]);

        return redirect()->back()->with('success', 'Ingreso registrado correctamente.');
    }
}
