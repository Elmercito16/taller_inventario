<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\Repuesto;
use App\Services\InventarioService;
use App\Support\Catalogos;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RepuestoController extends Controller
{
    /**
     * Listado de repuestos (filtrado automáticamente por empresa).
     */
    public function index(Request $request)
    {
        $categorias = Categoria::orderBy('nombre')->get();

        $query = Repuesto::with(['proveedor', 'categoria'])->orderBy('id', 'desc');

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('tipo')) {
            $request->tipo === 'servicio' ? $query->servicios() : $query->productos();
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('marca', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%")
                  ->orWhere('codigo_barras', $search);
            });
        }

        // Estadísticas con agregados en BD (no carga todos los registros)
        $stats = (clone $query)->reorder()->setEagerLoads([])
            ->selectRaw("COALESCE(SUM(CASE WHEN tipo = 'servicio' THEN 0 ELSE cantidad * precio_unitario END), 0) as valor_total")
            ->selectRaw('COUNT(DISTINCT categoria_id) as categorias_count')
            ->selectRaw("SUM(CASE WHEN tipo <> 'servicio' AND cantidad <= minimo_stock THEN 1 ELSE 0 END) as stock_bajo_count")
            ->first();

        $valorTotal = (float) ($stats->valor_total ?? 0);
        $categoriasCount = (int) ($stats->categorias_count ?? 0);
        $stockBajoCount = (int) ($stats->stock_bajo_count ?? 0);

        $stockBajo = (clone $query)->productos()->whereColumn('cantidad', '<=', 'minimo_stock')->where('cantidad', '>', 0)->get();
        $agotados = (clone $query)->productos()->where('cantidad', '<=', 0)->get();

        $repuestos = $query->paginate(12)->appends($request->query());

        return view('repuestos.index', compact(
            'repuestos',
            'categorias',
            'valorTotal',
            'agotados',
            'stockBajo',
            'stockBajoCount',
            'categoriasCount'
        ));
    }

    public function create()
    {
        $proveedores = Proveedor::orderBy('nombre')->get();
        $categorias = Categoria::orderBy('nombre')->get();
        $nuevoCodigo = Repuesto::siguienteCodigo();

        return view('repuestos.create', compact('proveedores', 'categorias', 'nuevoCodigo') + $this->catalogos());
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->reglas());

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('repuestos', 'public');
        }

        $this->crearConStockInicial($validated);

        return redirect()->route('repuestos.index')
            ->with('success', 'Producto agregado correctamente.');
    }

    public function edit($id)
    {
        $repuesto = Repuesto::findOrFail($id);
        $categorias = Categoria::orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('nombre')->get();

        return view('repuestos.edit', compact('repuesto', 'categorias', 'proveedores') + $this->catalogos());
    }

    public function update(Request $request, $id)
    {
        $repuesto = Repuesto::findOrFail($id);

        $validated = $request->validate($this->reglas($repuesto->id));

        if ($request->hasFile('imagen')) {
            if ($repuesto->imagen && Storage::disk('public')->exists($repuesto->imagen)) {
                Storage::disk('public')->delete($repuesto->imagen);
            }
            $validated['imagen'] = $request->file('imagen')->store('repuestos', 'public');
        }

        DB::transaction(function () use ($repuesto, $validated) {
            $nuevoStock = (float) ($validated['cantidad'] ?? $repuesto->cantidad);
            unset($validated['cantidad']);

            $repuesto->update($validated);

            if ($repuesto->controlaStock()) {
                app(InventarioService::class)->fijarStock($repuesto, $nuevoStock, 'edicion_producto', [
                    'descripcion' => 'Stock modificado al editar el producto',
                ]);
            }
        });

        return redirect()->route('repuestos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy($id)
    {
        $repuesto = Repuesto::findOrFail($id);

        if ($repuesto->detallesVenta()->exists() || $repuesto->detallesCompra()->exists()) {
            return redirect()->route('repuestos.index')
                ->with('error', 'No se puede eliminar: el producto tiene ventas o compras registradas. Puedes dejar su stock en 0.');
        }

        if ($repuesto->imagen && Storage::disk('public')->exists($repuesto->imagen)) {
            Storage::disk('public')->delete($repuesto->imagen);
        }

        $repuesto->movimientos()->delete();
        $repuesto->delete();

        return redirect()->route('repuestos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }

    /**
     * Formulario para sumar stock.
     */
    public function cantidad($id)
    {
        $repuesto = Repuesto::findOrFail($id);

        if ($repuesto->esServicio()) {
            return redirect()->route('repuestos.index')->with('warning', 'Los servicios no manejan stock.');
        }

        return view('repuestos.cantidad', compact('repuesto'));
    }

    public function updateCantidad(Request $request, $id)
    {
        $repuesto = Repuesto::findOrFail($id);

        if ($repuesto->esServicio()) {
            return redirect()->route('repuestos.index')->with('warning', 'Los servicios no manejan stock.');
        }

        $request->validate([
            'cantidad' => ['required', 'numeric', 'gt:0', 'max:1000000', $this->reglaDecimales($repuesto->unidad_medida)],
        ]);

        app(InventarioService::class)->entrada($repuesto, (float) $request->cantidad, 'ingreso_manual', [
            'descripcion' => 'Ingreso manual de stock',
        ]);

        return redirect()->route('repuestos.index')
            ->with('success', 'Se agregaron ' . Catalogos::cantidad($request->cantidad) . ' ' . $repuesto->unidad_abreviatura . ' a ' . $repuesto->nombre . '.');
    }

    /**
     * Crear repuesto rápido desde el punto de venta (AJAX).
     */
    public function storeQuick(Request $request)
    {
        try {
            $empresaId = auth()->user()->empresa_id;

            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'codigo' => [
                    'nullable', 'string', 'max:50',
                    Rule::unique('repuestos')->where(fn ($q) => $q->where('empresa_id', $empresaId)),
                ],
                'precio_unitario' => 'required|numeric|min:0',
                'cantidad' => 'required|numeric|min:0',
                'tipo' => ['nullable', Rule::in(array_keys(Catalogos::TIPOS_ITEM))],
                'unidad_medida' => ['nullable', Rule::in(array_keys(Catalogos::UNIDADES))],
                'categoria_id' => [
                    'required',
                    Rule::exists('categorias', 'id')->where(fn ($q) => $q->where('empresa_id', $empresaId)),
                ],
            ]);

            $repuesto = $this->crearConStockInicial($validated)->load('categoria');

            return response()->json([
                'success' => true,
                'repuesto' => [
                    'id' => (int) $repuesto->id,
                    'nombre' => (string) $repuesto->nombre,
                    'codigo' => (string) $repuesto->codigo,
                    'categoria' => $repuesto->categoria->nombre ?? 'Sin Categoría',
                    'precio_unitario' => (float) $repuesto->precio_unitario,
                    'stock' => (float) $repuesto->cantidad,
                    'servicio' => $repuesto->esServicio(),
                    'unidad' => $repuesto->unidad_abreviatura,
                    'decimales' => $repuesto->permiteDecimales(),
                ],
                'message' => 'Producto creado correctamente.',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['success' => false, 'message' => 'No se pudo crear el producto.'], 500);
        }
    }

    /**
     * Reglas de validación comunes (store/update), con unicidad y
     * pertenencia por empresa.
     */
    /**
     * Crea el producto con stock 0 y registra el stock inicial como movimiento del kardex.
     */
    private function crearConStockInicial(array $datos): Repuesto
    {
        return DB::transaction(function () use ($datos) {
            $stockInicial = (float) ($datos['cantidad'] ?? 0);
            $datos['cantidad'] = 0;

            $repuesto = Repuesto::create($datos);

            if ($stockInicial > 0) {
                app(InventarioService::class)->entrada($repuesto, $stockInicial, 'stock_inicial', [
                    'costo' => $repuesto->precio_compra,
                    'descripcion' => 'Stock inicial al registrar el producto',
                ]);
            }

            return $repuesto->refresh();
        });
    }

    private function reglas(?int $ignorarId = null): array
    {
        $empresaId = auth()->user()->empresa_id;
        $esServicio = request('tipo') === 'servicio';

        return [
            'tipo'            => ['required', Rule::in(array_keys(Catalogos::TIPOS_ITEM))],
            'nombre'          => 'required|string|max:255',
            'marca'           => 'nullable|string|max:255',
            'unidad_medida'   => ['required', Rule::in(array_keys(Catalogos::UNIDADES))],
            'afectacion_igv'  => ['required', Rule::in(array_keys(Catalogos::AFECTACIONES_IGV))],
            'cantidad'        => $esServicio
                ? 'nullable|numeric|min:0'
                : ['required', 'numeric', 'min:0', $this->reglaDecimales(request('unidad_medida'))],
            'precio_unitario' => 'required|numeric|min:0',
            'precio_compra'   => 'nullable|numeric|min:0',
            'descripcion'     => 'nullable|string',
            'fecha_ingreso'   => 'nullable|date',
            'minimo_stock'    => 'nullable|numeric|min:0',
            'imagen'          => 'nullable|image|max:2048',
            'codigo'          => [
                'required', 'string', 'max:50',
                Rule::unique('repuestos', 'codigo')
                    ->where(fn ($q) => $q->where('empresa_id', $empresaId))
                    ->ignore($ignorarId),
            ],
            'codigo_barras'   => [
                'nullable', 'string', 'max:50',
                Rule::unique('repuestos', 'codigo_barras')
                    ->where(fn ($q) => $q->where('empresa_id', $empresaId))
                    ->ignore($ignorarId),
            ],
            'proveedor_id'    => [
                'nullable',
                Rule::exists('proveedors', 'id')->where(fn ($q) => $q->where('empresa_id', $empresaId)),
            ],
            'categoria_id'    => [
                'required',
                Rule::exists('categorias', 'id')->where(fn ($q) => $q->where('empresa_id', $empresaId)),
            ],
        ];
    }

    /**
     * Las unidades enteras (unidad, caja, paquete...) no aceptan decimales.
     */
    private function reglaDecimales(?string $unidad): \Closure
    {
        return function ($atributo, $valor, $fallar) use ($unidad) {
            if (! Catalogos::unidadPermiteDecimales($unidad) && floor((float) $valor) != (float) $valor) {
                $fallar('La unidad "' . Catalogos::nombreUnidad($unidad) . '" solo admite cantidades enteras.');
            }
        };
    }

    private function catalogos(): array
    {
        return [
            'unidades' => Catalogos::UNIDADES,
            'afectaciones' => Catalogos::AFECTACIONES_IGV,
            'tiposItem' => Catalogos::TIPOS_ITEM,
        ];
    }
}
