<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Gasto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
            $fechaFin = Carbon::today()->endOfDay(); // Hasta el final del día
        } elseif ($filtro === 'semana') {
            $fechaInicio = Carbon::now()->startOfWeek();
            $fechaFin = Carbon::now()->endOfWeek();
        } elseif ($filtro === 'personalizado' && $request->fecha_inicio && $request->fecha_fin) {
            $fechaInicio = Carbon::parse($request->fecha_inicio)->startOfDay();
            $fechaFin = Carbon::parse($request->fecha_fin)->endOfDay();
        }

        // 2. Obtener Datos (El Multi-tenant filtra automáticamente por empresa)
        
        // Ingresos (Ventas Pagadas)
        $ventas = Venta::where('estado', 'pagado')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->with('cliente') // Cargar relación cliente para mostrar nombres
            ->orderBy('fecha', 'desc')
            ->get();

        // Gastos
        $gastos = Gasto::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->orderBy('fecha', 'desc')
            ->get();

        // 3. Calcular Totales
        $totalIngresos = $ventas->sum('total');
        $totalGastos = $gastos->sum('monto');
        $balance = $totalIngresos - $totalGastos;

        // 4. Crear Caja de Flujo Unificada (Merge)
        // Transformamos ambos para que tengan estructura similar
        $flujoVentas = $ventas->map(function ($v) {
            return [
                'id' => $v->id,
                'tipo' => 'ingreso', // Para pintar verde
                'descripcion' => 'Venta #' . str_pad($v->id, 6, '0', STR_PAD_LEFT) . ($v->cliente ? ' - ' . $v->cliente->nombre : ''),
                'monto' => $v->total,
                'fecha' => $v->fecha,
                'modelo' => 'Venta'
            ];
        });

        $flujoGastos = $gastos->map(function ($g) {
            return [
                'id' => $g->id,
                'tipo' => 'gasto', // Para pintar rojo
                'descripcion' => $g->descripcion . ($g->categoria ? ' (' . $g->categoria . ')' : ''),
                'monto' => $g->monto,
                'fecha' => $g->fecha, // Ojo: en BD es date, en venta es timestamp
                'modelo' => 'Gasto'
            ];
        });

        // Unimos y ordenamos por fecha descendente
        $cajaFlujo = $flujoVentas->concat($flujoGastos)->sortByDesc('fecha');

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
}