<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Gasto;
use App\Models\Ticket;
use App\Models\Ingreso;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanzasController extends Controller
{
    public function index(Request $request)
    {
        // 1. Rango de fechas
        $filtro = $request->get('filtro', 'mes_actual');
        [$fechaInicio, $fechaFin] = $this->rangoFechas($filtro, $request);

        // 2. Totales (agregados en BD; el scope de empresa se aplica solo)
        $ventasQuery = Venta::where('estado', 'pagado')->whereBetween('fecha', [$fechaInicio, $fechaFin]);
        $ingresosQuery = Ingreso::whereBetween('fecha', [$fechaInicio->toDateString(), $fechaFin->toDateString()]);
        $gastosQuery = Gasto::whereBetween('fecha', [$fechaInicio->toDateString(), $fechaFin->toDateString()]);
        // Las compras de mercadería registradas también son egresos
        $comprasQuery = Compra::registradas()->whereBetween('fecha', [$fechaInicio->toDateString(), $fechaFin->toDateString()]);

        // Tickets rápidos (provisional, ver config/sistema.php)
        $incluirTickets = config('sistema.tickets_en_finanzas');
        $ticketsQuery = Ticket::where('estado', 'emitido')->whereBetween('fecha', [$fechaInicio, $fechaFin]);

        $totalIngresos = (float) (clone $ventasQuery)->sum('total') + (float) (clone $ingresosQuery)->sum('monto')
            + ($incluirTickets ? (float) (clone $ticketsQuery)->sum('total') : 0);
        $totalGastos = (float) (clone $gastosQuery)->sum('monto') + (float) (clone $comprasQuery)->sum('total');
        $balance = $totalIngresos - $totalGastos;

        // 3. Flujo de caja unificado, paginado directamente en la BD (UNION ALL)
        $tipoMovimiento = $request->get('tipo_movimiento', 'todos');

        $partes = [];
        if ($tipoMovimiento !== 'gastos') {
            $partes[] = (clone $ventasQuery)->toBase()
                ->select('id', DB::raw("'Venta' as modelo"), DB::raw("'ingreso' as tipo"), 'total as monto', 'fecha', DB::raw('NULL as descripcion'), DB::raw('NULL as detalle'), 'cliente_id');
            $partes[] = (clone $ingresosQuery)->toBase()
                ->select('id', DB::raw("'Ingreso' as modelo"), DB::raw("'ingreso' as tipo"), 'monto', 'fecha', 'descripcion', 'tipo as detalle', DB::raw('NULL as cliente_id'));
            if ($incluirTickets) {
                // En tickets, "descripcion" lleva el cliente y "detalle" el número
                $partes[] = (clone $ticketsQuery)->toBase()
                    ->select('id', DB::raw("'Ticket' as modelo"), DB::raw("'ingreso' as tipo"), 'total as monto', 'fecha', 'cliente_nombre as descripcion', DB::raw('CAST(numero AS CHAR(10)) as detalle'), DB::raw('NULL as cliente_id'));
            }
        }
        if ($tipoMovimiento !== 'ingresos') {
            $partes[] = (clone $gastosQuery)->toBase()
                ->select('id', DB::raw("'Gasto' as modelo"), DB::raw("'gasto' as tipo"), 'monto', 'fecha', 'descripcion', 'categoria as detalle', DB::raw('NULL as cliente_id'));
            // En compras, la columna "cliente_id" del UNION lleva el proveedor
            $partes[] = (clone $comprasQuery)->toBase()
                ->select('id', DB::raw("'Compra' as modelo"), DB::raw("'gasto' as tipo"), 'total as monto', 'fecha', DB::raw('NULL as descripcion'), 'numero_documento as detalle', 'proveedor_id as cliente_id');
        }

        $union = array_shift($partes);
        foreach ($partes as $parte) {
            $union->unionAll($parte);
        }

        $cajaFlujo = DB::query()
            ->fromSub($union, 'movimientos')
            ->orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Nombres de clientes solo para la página actual
        $clientes = \App\Models\Cliente::whereIn('id', collect($cajaFlujo->items())->where('modelo', 'Venta')->pluck('cliente_id')->filter()->unique())
            ->pluck('nombre', 'id');

        $proveedores = \App\Models\Proveedor::whereIn('id', collect($cajaFlujo->items())->where('modelo', 'Compra')->pluck('cliente_id')->filter()->unique())
            ->pluck('nombre', 'id');

        $cajaFlujo->through(function ($m) use ($clientes, $proveedores) {
            $descripcion = match ($m->modelo) {
                'Compra' => 'Compra C-' . str_pad($m->id, 6, '0', STR_PAD_LEFT)
                    . ($m->cliente_id && isset($proveedores[$m->cliente_id]) ? ' - ' . $proveedores[$m->cliente_id] : '')
                    . ($m->detalle ? ' (' . $m->detalle . ')' : ''),
                'Ticket' => 'Ticket T-' . str_pad(trim((string) $m->detalle), 6, '0', STR_PAD_LEFT)
                    . ' - ' . ($m->descripcion ?: 'Cliente general'),
                'Venta' => 'Venta #' . str_pad($m->id, 6, '0', STR_PAD_LEFT)
                    . ($m->cliente_id && isset($clientes[$m->cliente_id]) ? ' - ' . $clientes[$m->cliente_id] : ''),
                default => $m->descripcion . ($m->detalle ? ' (' . $m->detalle . ')' : ''),
            };

            return [
                'id' => $m->id,
                'tipo' => $m->tipo,
                'descripcion' => $descripcion,
                'monto' => (float) $m->monto,
                'fecha' => $m->fecha,
                'modelo' => $m->modelo,
            ];
        });

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
        $validated = $request->validate([
            'descripcion' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0.01',
            'fecha' => 'required|date',
            'categoria' => 'nullable|string|max:100',
        ]);

        Gasto::create($validated);

        return redirect()->back()->with('success', 'Gasto registrado correctamente.');
    }

    public function storeIngreso(Request $request)
    {
        $validated = $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'descripcion' => 'required|string|max:255',
            'fecha' => 'required|date',
            'tipo_ingreso' => 'required|string|max:100',
        ]);

        Ingreso::create([
            'monto' => $validated['monto'],
            'descripcion' => $validated['descripcion'],
            'fecha' => $validated['fecha'],
            'tipo' => $validated['tipo_ingreso'],
        ]);

        return redirect()->back()->with('success', 'Ingreso registrado correctamente.');
    }

    /**
     * Anular = borrado lógico (soft delete): el registro se conserva para auditoría.
     */
    public function anularIngreso($id)
    {
        Ingreso::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Ingreso anulado correctamente.');
    }

    public function anularGasto($id)
    {
        Gasto::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Gasto anulado correctamente.');
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function rangoFechas(string $filtro, Request $request): array
    {
        return match ($filtro) {
            'hoy' => [Carbon::today(), Carbon::today()->endOfDay()],
            'semana' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'anio_actual' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            'personalizado' => $request->filled(['fecha_inicio', 'fecha_fin'])
                ? [Carbon::parse($request->fecha_inicio)->startOfDay(), Carbon::parse($request->fecha_fin)->endOfDay()]
                : [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }
}
