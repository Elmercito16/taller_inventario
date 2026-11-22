<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Proveedor;
use App\Models\Repuesto;
use App\Services\InventarioService;
use App\Support\Catalogos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CompraController extends Controller
{
    public function __construct(protected InventarioService $inventario)
    {
    }

    public function index(Request $request)
    {
        $query = Compra::with(['proveedor', 'usuario'])->withCount('detalles')
            ->orderByDesc('fecha')
            ->orderByDesc('id');

        if ($request->filled('proveedor_id')) {
            $query->where('proveedor_id', $request->proveedor_id);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('desde')) {
            $query->whereDate('fecha', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $request->hasta);
        }
        if ($request->filled('q')) {
            $query->whereLike('numero_documento', '%' . $request->q . '%');
        }

        $totalPeriodo = (float) (clone $query)->reorder()->where('estado', 'registrada')->sum('total');
        $compras = $query->paginate(15)->withQueryString();
        $proveedores = Proveedor::orderBy('nombre')->get(['id', 'nombre']);

        return view('compras.index', compact('compras', 'proveedores', 'totalPeriodo'));
    }

    public function create()
    {
        $proveedores = Proveedor::orderBy('nombre')->get(['id', 'nombre']);

        $productos = Repuesto::productos()->orderBy('nombre')->get()->map(fn (Repuesto $r) => [
            'id' => $r->id,
            'nombre' => $r->nombre,
            'codigo' => (string) $r->codigo,
            'barras' => (string) ($r->codigo_barras ?? ''),
            'stock' => (float) $r->cantidad,
            'unidad' => $r->unidad_abreviatura,
            'decimales' => $r->permiteDecimales(),
            'costo' => $r->precio_compra !== null ? (float) $r->precio_compra : null,
            'precio' => (float) $r->precio_unitario,
        ])->values();

        return view('compras.create', [
            'proveedores' => $proveedores,
            'productos' => $productos,
            'tiposDocumento' => Compra::TIPOS_DOCUMENTO,
        ]);
    }

    public function store(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;
        $porEmpresa = fn ($q) => $q->where('empresa_id', $empresaId);

        $request->validate([
            'proveedor_id' => ['nullable', Rule::exists('proveedors', 'id')->where($porEmpresa)],
            'fecha' => 'required|date|before_or_equal:today',
            'tipo_documento' => ['required', Rule::in(array_keys(Compra::TIPOS_DOCUMENTO))],
            'numero_documento' => [
                'nullable', 'string', 'max:30',
                // Evita registrar dos veces el mismo documento del mismo proveedor
                Rule::unique('compras', 'numero_documento')->where(fn ($q) => $q
                    ->where('empresa_id', $empresaId)
                    ->where('proveedor_id', $request->proveedor_id)
                    ->where('tipo_documento', $request->tipo_documento)
                    ->where('estado', 'registrada')),
            ],
            'observaciones' => 'nullable|string|max:1000',
            'actualizar_costo' => 'nullable|boolean',
            'items' => 'required|array|min:1',
            'items.*.id' => ['required', Rule::exists('repuestos', 'id')->where($porEmpresa)],
            'items.*.cantidad' => 'required|numeric|gt:0|max:1000000',
            'items.*.costo' => 'required|numeric|min:0',
        ], [
            'items.required' => 'Agrega al menos un producto a la compra.',
            'numero_documento.unique' => 'Ese documento ya fue registrado para este proveedor.',
            'fecha.before_or_equal' => 'La fecha no puede ser futura.',
        ]);

        try {
            $compra = DB::transaction(function () use ($request) {
                $items = collect($request->items)->map(fn ($i) => [
                    'id' => (int) $i['id'],
                    'cantidad' => round((float) $i['cantidad'], 3),
                    'costo' => round((float) $i['costo'], 4),
                ]);

                $compra = Compra::create([
                    'proveedor_id' => $request->proveedor_id ?: null,
                    'usuario_id' => auth()->id(),
                    'fecha' => $request->fecha,
                    'tipo_documento' => $request->tipo_documento,
                    'numero_documento' => $request->numero_documento ? mb_strtoupper(trim($request->numero_documento)) : null,
                    'observaciones' => $request->observaciones,
                    'total' => 0,
                    'estado' => 'registrada',
                ]);

                $total = 0;
                $actualizarCosto = $request->boolean('actualizar_costo', true);

                foreach ($items as $item) {
                    $repuesto = Repuesto::findOrFail($item['id']);

                    if ($repuesto->esServicio()) {
                        throw new \RuntimeException("{$repuesto->nombre} es un servicio y no se puede comprar para stock.");
                    }

                    if (! $repuesto->permiteDecimales() && floor($item['cantidad']) != $item['cantidad']) {
                        throw new \RuntimeException("{$repuesto->nombre} se maneja por {$repuesto->unidad_nombre}: la cantidad debe ser entera.");
                    }

                    $subtotal = round($item['cantidad'] * $item['costo'], 2);
                    $total += $subtotal;

                    DetalleCompra::create([
                        'compra_id' => $compra->id,
                        'repuesto_id' => $repuesto->id,
                        'cantidad' => $item['cantidad'],
                        'costo_unitario' => $item['costo'],
                        'subtotal' => $subtotal,
                    ]);

                    $this->inventario->entrada($repuesto, $item['cantidad'], 'compra', [
                        'referencia' => $compra,
                        'costo' => $item['costo'],
                        'descripcion' => 'Compra ' . $compra->numero . ' · ' . $compra->documento_texto,
                    ]);

                    if ($actualizarCosto) {
                        $repuesto->update(['precio_compra' => round($item['costo'], 2)]);
                    }
                }

                $compra->update(['total' => round($total, 2)]);

                return $compra;
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('compras.show', $compra)
            ->with('success', "Compra {$compra->numero} registrada. El stock fue actualizado.");
    }

    public function show(Compra $compra)
    {
        $compra->load(['proveedor', 'usuario', 'detalles.repuesto', 'movimientos.repuesto']);

        return view('compras.show', compact('compra'));
    }

    /**
     * Anula la compra y retira del stock lo que había ingresado.
     */
    public function anular(Request $request, Compra $compra)
    {
        $request->validate(['motivo' => 'required|string|min:5|max:255']);

        if ($compra->estaAnulada()) {
            return back()->with('error', 'La compra ya está anulada.');
        }

        try {
            DB::transaction(function () use ($compra, $request) {
                foreach ($compra->detalles()->with('repuesto')->get() as $detalle) {
                    if (! $detalle->repuesto) {
                        continue;
                    }

                    $this->inventario->salida($detalle->repuesto, $detalle->cantidad, 'anulacion_compra', [
                        'referencia' => $compra,
                        'costo' => $detalle->costo_unitario,
                        'descripcion' => 'Anulación de compra ' . $compra->numero . ': ' . $request->motivo,
                    ]);
                }

                $compra->update([
                    'estado' => 'anulada',
                    'fecha_anulacion' => now(),
                    'motivo_anulacion' => $request->motivo,
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', 'No se puede anular: ' . $e->getMessage() . ' (parte de la mercadería ya se vendió).');
        }

        return back()->with('success', "Compra {$compra->numero} anulada. Se retiró la mercadería del stock.");
    }
}
