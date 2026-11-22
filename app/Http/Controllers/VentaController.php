<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\Repuesto;
use App\Models\Venta;
use App\Services\InventarioService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $query = Venta::query();

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($w) use ($q) {
                $w->whereHas('cliente', fn ($c) => $c->where('nombre', 'like', "%{$q}%")->orWhere('dni', 'like', "%{$q}%")->orWhere('ruc', 'like', "%{$q}%"));
                if (ctype_digit(ltrim($q, '#0')) && ltrim($q, '#0') !== '') {
                    $w->orWhere('id', (int) ltrim($q, '#0'));
                }
            });
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->metodo_pago);
        }
        if ($request->filled('desde')) {
            $query->where('fecha', '>=', Carbon::parse($request->desde)->startOfDay());
        }
        if ($request->filled('hasta')) {
            $query->where('fecha', '<=', Carbon::parse($request->hasta)->endOfDay());
        }

        $resumen = [
            'cantidad' => (clone $query)->count(),
            'total' => (float) (clone $query)->where('estado', 'pagado')->sum('total'),
            'anuladas' => (clone $query)->where('estado', 'anulado')->count(),
            'hoy' => (float) Venta::where('estado', 'pagado')->whereDate('fecha', today())->sum('total'),
        ];

        $ventas = $query->with(['cliente', 'comprobante', 'usuario'])->withCount('detalles')
            ->orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('ventas.index', compact('ventas', 'resumen'));
    }

    public function create()
    {
        // Productos con stock + servicios (que no manejan stock)
        $repuestos = Repuesto::with('categoria')
            ->disponibles()
            ->orderBy('nombre')
            ->get();

        $categorias = Categoria::orderBy('nombre')->get();

        return view('ventas.create', compact('repuestos', 'categorias'));
    }

    public function store(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;

        $request->validate([
            'cliente_id' => [
                'nullable',
                Rule::exists('clientes', 'id')->where(fn ($q) => $q->where('empresa_id', $empresaId)),
            ],
            'metodo_pago' => ['nullable', Rule::in(array_keys(Venta::METODOS_PAGO))],
            'repuestos' => 'required|array|min:1',
            'repuestos.*.id' => [
                'required',
                Rule::exists('repuestos', 'id')->where(fn ($q) => $q->where('empresa_id', $empresaId)),
            ],
            'repuestos.*.cantidad' => 'required|numeric|gt:0',
            'repuestos.*.precio' => 'required|numeric|min:0',
        ], [
            'repuestos.required' => 'Agrega al menos un producto a la venta.',
        ]);

        // Agrupar líneas repetidas del mismo repuesto
        $items = collect($request->repuestos)
            ->groupBy('id')
            ->map(fn ($lineas, $id) => [
                'id' => (int) $id,
                'cantidad' => round((float) $lineas->sum('cantidad'), 3),
                'precio' => round((float) $lineas->first()['precio'], 2),
            ])
            ->values();

        try {
            $venta = DB::transaction(function () use ($request, $items) {
                // El total SIEMPRE se calcula en el servidor
                $total = $items->sum(fn ($i) => round($i['cantidad'] * $i['precio'], 2));

                $venta = Venta::create([
                    'cliente_id'  => $request->cliente_id ?: null,
                    'usuario_id'  => auth()->id(),
                    'fecha'       => now(),
                    'total'       => $total,
                    'estado'      => 'pagado',
                    'metodo_pago' => $request->metodo_pago ?: 'efectivo',
                ]);

                foreach ($items as $item) {
                    // Bloqueo de fila: evita vender el mismo stock dos veces
                    $repuesto = Repuesto::whereKey($item['id'])->lockForUpdate()->firstOrFail();

                    if (! $repuesto->permiteDecimales() && floor($item['cantidad']) != $item['cantidad']) {
                        throw new \RuntimeException("{$repuesto->nombre} se vende por {$repuesto->unidad_nombre}: la cantidad debe ser entera.");
                    }

                    if ($repuesto->controlaStock() && $item['cantidad'] > $repuesto->cantidad) {
                        throw new \RuntimeException("No hay suficiente stock de {$repuesto->nombre} (disponible: {$repuesto->stock_formateado}).");
                    }

                    DetalleVenta::create([
                        'venta_id'        => $venta->id,
                        'repuesto_id'     => $repuesto->id,
                        'cantidad'        => $item['cantidad'],
                        'precio_unitario' => $item['precio'],
                        'subtotal'        => round($item['cantidad'] * $item['precio'], 2),
                        'costo_unitario'  => $repuesto->precio_compra,
                        'unidad_medida'   => $repuesto->unidad_medida ?: 'NIU',
                        'afectacion_igv'  => $repuesto->afectacion_igv ?: '10',
                    ]);

                    // Descuenta stock y registra la salida en el kardex
                    app(InventarioService::class)->salida($repuesto, $item['cantidad'], 'venta', [
                        'referencia' => $venta,
                        'costo' => $repuesto->precio_compra,
                        'descripcion' => 'Venta #' . str_pad($venta->id, 6, '0', STR_PAD_LEFT),
                    ]);
                }

                return $venta;
            });

            return redirect()->route('ventas.show', $venta)
                ->with('success', 'Venta #' . str_pad($venta->id, 6, '0', STR_PAD_LEFT) . ' registrada correctamente.');
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return redirect()->back()->withInput()->with('error', 'No se pudo registrar la venta. Intenta nuevamente.');
        }
    }

    public function show(Venta $venta)
    {
        // El binding ya filtra por empresa; doble verificación por seguridad.
        abort_unless((int) $venta->empresa_id === (int) auth()->user()->empresa_id, 403);

        $venta->load(['cliente', 'detalles.repuesto', 'comprobante', 'usuario']);

        return view('ventas.show', compact('venta'));
    }

    /**
     * Anular venta (AJAX): devuelve el stock.
     */
    public function anular($id)
    {
        try {
            $venta = Venta::findOrFail($id);
            $this->aplicarCambioEstado($venta, 'anulado');

            return response()->json([
                'success' => true,
                'message' => 'Venta anulada y stock devuelto correctamente.',
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['success' => false, 'message' => 'No se pudo anular la venta.'], 500);
        }
    }

    /**
     * Cambiar estado (AJAX). Mantiene el stock consistente en ambas direcciones.
     */
    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,pagado,anulado',
        ]);

        try {
            $venta = Venta::findOrFail($id);
            $this->aplicarCambioEstado($venta, $request->estado);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado correctamente',
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['success' => false, 'message' => 'No se pudo actualizar el estado.'], 500);
        }
    }

    /**
     * Lógica central de cambio de estado:
     * - activo -> anulado: devuelve stock.
     * - anulado -> activo: vuelve a descontar stock (si hay disponible).
     * - No permite anular si tiene comprobante electrónico vigente (requiere nota de crédito / baja).
     */
    private function aplicarCambioEstado(Venta $venta, string $nuevoEstado): void
    {
        if ($venta->estado === $nuevoEstado) {
            throw new \RuntimeException($nuevoEstado === 'anulado' ? 'La venta ya está anulada.' : 'La venta ya tiene ese estado.');
        }

        if ($nuevoEstado === 'anulado' && $venta->comprobanteAceptadoPorSunat()) {
            throw new \RuntimeException('Esta venta tiene un comprobante electrónico vigente. Primero anúlalo desde el módulo de Comprobantes.');
        }

        DB::transaction(function () use ($venta, $nuevoEstado) {
            $venta->load('detalles');

            $inventario = app(InventarioService::class);
            $numero = 'Venta #' . str_pad($venta->id, 6, '0', STR_PAD_LEFT);

            if ($nuevoEstado === 'anulado') {
                foreach ($venta->detalles as $detalle) {
                    if ($repuesto = Repuesto::find($detalle->repuesto_id)) {
                        $inventario->entrada($repuesto, $detalle->cantidad, 'anulacion_venta', [
                            'referencia' => $venta,
                            'costo' => $detalle->costo_unitario,
                            'descripcion' => "Anulación de {$numero}",
                        ]);
                    }
                }
            } elseif ($venta->estado === 'anulado') {
                foreach ($venta->detalles as $detalle) {
                    $repuesto = Repuesto::find($detalle->repuesto_id);

                    if (! $repuesto) {
                        throw new \RuntimeException('Un producto de la venta fue eliminado; no se puede reactivar.');
                    }

                    // Lanza excepción si no hay stock suficiente
                    $inventario->salida($repuesto, $detalle->cantidad, 'reactivacion_venta', [
                        'referencia' => $venta,
                        'costo' => $detalle->costo_unitario,
                        'descripcion' => "Reactivación de {$numero}",
                    ]);
                }
            }

            $venta->update(['estado' => $nuevoEstado]);
        });
    }

    public function historialCliente(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        $ventas = Venta::with('detalles.repuesto')->where('cliente_id', $cliente->id);

        $hoy = Carbon::now();
        $rango = match ($request->get('filter', 'all')) {
            'today'        => [$hoy->copy()->startOfDay(), $hoy->copy()->endOfDay()],
            'yesterday'    => [$hoy->copy()->subDay()->startOfDay(), $hoy->copy()->subDay()->endOfDay()],
            'last_7_days'  => [$hoy->copy()->subDays(7)->startOfDay(), $hoy->copy()->endOfDay()],
            'last_30_days' => [$hoy->copy()->subDays(30)->startOfDay(), $hoy->copy()->endOfDay()],
            'this_week'    => [$hoy->copy()->startOfWeek(), $hoy->copy()->endOfWeek()],
            'last_week'    => [$hoy->copy()->subWeek()->startOfWeek(), $hoy->copy()->subWeek()->endOfWeek()],
            'this_month'   => [$hoy->copy()->startOfMonth(), $hoy->copy()->endOfMonth()],
            'last_month'   => [$hoy->copy()->subMonthNoOverflow()->startOfMonth(), $hoy->copy()->subMonthNoOverflow()->endOfMonth()],
            'this_year'    => [$hoy->copy()->startOfYear(), $hoy->copy()->endOfYear()],
            'last_year'    => [$hoy->copy()->subYear()->startOfYear(), $hoy->copy()->subYear()->endOfYear()],
            'custom'       => $request->filled(['start_date', 'end_date'])
                ? [Carbon::parse($request->start_date)->startOfDay(), Carbon::parse($request->end_date)->endOfDay()]
                : null,
            default        => null,
        };

        if ($rango) {
            $ventas->whereBetween('fecha', $rango);
        }

        $ventas = $ventas->orderBy('fecha', 'desc')->paginate(10)->appends($request->query());

        return view('ventas.historial', compact('cliente', 'ventas'));
    }

    /**
     * Detalle de venta en JSON (modal).
     */
    public function detalles($id)
    {
        $venta = Venta::with(['cliente', 'detalles.repuesto'])->findOrFail($id);

        return response()->json([
            'id' => $venta->id,
            'fecha' => $venta->fecha?->format('d/m/Y H:i'),
            'total' => number_format($venta->total, 2),
            'estado' => ucfirst($venta->estado),
            'metodo_pago' => $venta->metodo_pago_nombre,
            'cliente' => [
                'nombre' => $venta->cliente->nombre ?? 'Cliente General',
                'dni' => $venta->cliente->dni ?? 'No especificado',
                'telefono' => $venta->cliente->telefono ?? 'No especificado',
            ],
            'detalles' => $venta->detalles->map(fn ($detalle) => [
                'repuesto' => [
                    'nombre' => $detalle->repuesto->nombre ?? 'Producto eliminado',
                    'codigo' => $detalle->repuesto->codigo ?? 'N/A',
                ],
                'cantidad' => $detalle->cantidad,
                'precio_unitario' => number_format($detalle->precio_unitario, 2),
                'subtotal' => number_format($detalle->subtotal, 2),
            ]),
        ]);
    }

    /**
     * Boleta térmica (80mm) en PDF.
     */
    public function generarBoleta($id)
    {
        try {
            $venta = Venta::with(['cliente', 'detalles.repuesto', 'empresa'])->findOrFail($id);

            $pdf = Pdf::loadView('ventas.boleta', ['venta' => $venta]);
            $pdf->setPaper([0, 0, 226.77, 841.89], 'portrait');

            return $pdf->stream('Boleta_' . str_pad($venta->id, 8, '0', STR_PAD_LEFT) . '.pdf');
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('ventas.index')->with('error', 'Error al generar la boleta.');
        }
    }

    /**
     * Ticket simple imprimible desde el navegador.
     */
    public function generarTicket($id)
    {
        $venta = Venta::with(['cliente', 'detalles.repuesto', 'empresa', 'usuario'])->findOrFail($id);

        return view('ventas.ticket', compact('venta'));
    }
}
