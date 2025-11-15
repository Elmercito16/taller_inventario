<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Repuesto;
use App\Models\Proveedor;
use App\Models\Categoria;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule; // 1. ¡IMPORTAR LA CLASE RULE!
use Illuminate\Support\Facades\DB; // (Asegúrate de importar DB)

class RepuestoController extends Controller
{
    /**
     * Mostrar el listado de repuestos
     * * ¡MAGIA! No necesitas cambiar nada aquí.
     * Gracias al Trait 'BelongsToTenant' en el modelo Repuesto,
     * esta consulta YA está filtrada por la 'empresa_id' del usuario.
     */
    public function index(Request $request)
    {
        // Obtenemos todas las categorías (¡solo las de esta empresa!)
        $categorias = Categoria::all();

        // Construimos la consulta base (¡solo de esta empresa!)
        $query = Repuesto::with(['proveedor', 'categoria'])
            ->orderBy('id', 'desc');

        // Aplicar filtro de categoría (si existe)
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        // Aplicar filtro de búsqueda (si existe)
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('marca', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%");
            });
        }

        // ¡Clonamos la consulta ANTES de paginar!
        $queryStats = clone $query;

        // PAGINAMOS (appends() se encarga de mantener los filtros en la URL)
        $repuestos = $query->paginate(12)->appends($request->query());

        // --- CALCULAR ESTADÍSTICAS GLOBALES (BASADO EN FILTROS) ---
        // (Usamos la consulta clonada, que no está paginada)
        $itemsGlobales = $queryStats->get();
        
        $valorTotal = $itemsGlobales->sum(function($item) { 
            return $item->cantidad * $item->precio_unitario; 
        });

        // Contar repuestos con stock bajo (incluyendo agotados)
        $stockBajoCount = $itemsGlobales->filter(function($item) { 
            return $item->cantidad <= $item->minimo_stock; 
        })->count();

        // Colecciones separadas para las alertas
        $stockBajo = $itemsGlobales->filter(function($item) { 
            return $item->cantidad <= $item->minimo_stock && $item->cantidad > 0; 
        });
        
        $agotados = $itemsGlobales->filter(function($item) { 
            return $item->cantidad <= 0; 
        });
        
        $categoriasCount = $itemsGlobales->pluck('categoria_id')->unique()->count();

        // Enviamos todo a la vista
        return view('repuestos.index', compact(
            'repuestos', 
            'categorias', 
            'valorTotal',
            'agotados',
            'stockBajo',
            'stockBajoCount', // Enviamos el conteo
            'categoriasCount'
        ));
    }

    /**
     * Mostrar el formulario para crear un nuevo repuesto
     * * ¡MAGIA! Proveedor::all() y Categoria::all()
     * solo traerán los datos de la empresa actual.
     */
    public function create()
    {
        $proveedores = Proveedor::all();
        $categorias  = Categoria::all();

        // Generar código automático
        // (Esto es seguro, 'Repuesto::orderBy' también está filtrado por empresa)
        $ultimoRepuesto = Repuesto::orderBy('id', 'desc')->first();
        if ($ultimoRepuesto) {
            $ultimoId    = intval(str_replace('REP', '', $ultimoRepuesto->codigo));
            $nuevoCodigo = 'REP' . str_pad($ultimoId + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nuevoCodigo = 'REP0001';
        }

        return view('repuestos.create', compact('proveedores', 'categorias', 'nuevoCodigo'));
    }

    /**
     * Guardar un nuevo repuesto
     */
    public function store(Request $request)
    {
        // 2. ¡VALIDACIÓN CORREGIDA Y SEGURA!
        $validated = $request->validate([
            'nombre'          => 'required|string|max:255',
            'marca'           => 'required|string|max:255',
            'cantidad'        => 'required|integer|min:0',
            'precio_unitario' => 'required|numeric|min:0',
            'descripcion'     => 'nullable|string',
            'fecha_ingreso'   => 'nullable|date',
            // ¡VALIDACIÓN DE CÓDIGO ÚNICO POR EMPRESA!
            'codigo'          => [
                'required', 'string',
                Rule::unique('repuestos', 'codigo')->where(function ($query) {
                    return $query->where('empresa_id', auth()->user()->empresa_id);
                })
            ],
            'minimo_stock'    => 'nullable|integer|min:0',
            'imagen'          => 'nullable|image|max:2048',

            // ¡REGLA DE SEGURIDAD!
            // 1. Valida que el proveedor_id exista en la tabla 'proveedor' (singular).
            // 2. Y que, además, ese proveedor pertenezca a la empresa del usuario actual.
            'proveedor_id'    => [
                'nullable',
                Rule::exists('proveedor', 'id')->where(function ($query) {
                    return $query->where('empresa_id', auth()->user()->empresa_id);
                })
            ],
            
            // ¡REGLA DE SEGURIDAD!
            // 1. Valida que el categoria_id exista en la tabla 'categorias'.
            // 2. Y que, además, esa categoría pertenezca a la empresa del usuario actual.
            'categoria_id'    => [
                'required',
                Rule::exists('categorias', 'id')->where(function ($query) {
                    return $query->where('empresa_id', auth()->user()->empresa_id);
                })
            ],
        ]);

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('repuestos', 'public');
        }

        // ¡MAGIA! Repuesto::create() añadirá automáticamente
        // el 'empresa_id' del usuario actual.
        Repuesto::create($validated);

        return redirect()->route('repuestos.index')
            ->with('success', '✅ Repuesto agregado correctamente.');
    }

    /**
     * Mostrar formulario de edición
     * * ¡MAGIA! FindOrFail() fallará si el 'id' del repuesto
     * no pertenece a la empresa del usuario.
     */
    public function edit($id)
    {
        $repuesto    = Repuesto::findOrFail($id);
        $proveedores = Proveedor::all();
        $categorias  = Categoria::all();

        return view('repuestos.edit', compact('repuesto', 'proveedores', 'categorias'));
    }

    /**
     * Actualizar repuesto
     */
    public function update(Request $request, $id)
    {
        // ¡MAGIA! FindOrFail() protege contra editar datos de otra empresa.
        $repuesto = Repuesto::findOrFail($id);

        // 3. ¡MISMA VALIDACIÓN SEGURA QUE EN STORE!
        $validated = $request->validate([
            'nombre'          => 'required|string|max:255',
            'marca'           => 'required|string|max:255',
            'cantidad'        => 'required|integer|min:0',
            'precio_unitario' => 'required|numeric|min:0',
            'descripcion'     => 'nullable|string',
            'fecha_ingreso'   => 'nullable|date',
            'minimo_stock'    => 'nullable|integer|min:0',
            'imagen'          => 'nullable|image|max:2048',
            
            // ¡VALIDACIÓN DE CÓDIGO ÚNICO POR EMPRESA!
            'codigo'          => [
                'required', 'string',
                Rule::unique('repuestos', 'codigo')->where(function ($query) {
                    return $query->where('empresa_id', auth()->user()->empresa_id);
                })->ignore($repuesto->id) // Ignora el repuesto actual al editar
            ],
            
            'proveedor_id'    => [
                'nullable',
                Rule::exists('proveedor', 'id')->where(function ($query) {
                    return $query->where('empresa_id', auth()->user()->empresa_id);
                })
            ],
            'categoria_id'    => [
                'required',
                Rule::exists('categorias', 'id')->where(function ($query) {
                    return $query->where('empresa_id', auth()->user()->empresa_id);
                })
            ],
        ]);

        if ($request->hasFile('imagen')) {
            if ($repuesto->imagen && Storage::disk('public')->exists($repuesto->imagen)) {
                Storage::disk('public')->delete($repuesto->imagen);
            }
            $validated['imagen'] = $request->file('imagen')->store('repuestos', 'public');
        }

        $repuesto->update($validated);

        return redirect()->route('repuestos.index')
            ->with('success', '✏️ Repuesto actualizado correctamente.');
    }

    /**
     * Eliminar repuesto
     */
    public function destroy($id)
    {
        // ¡MAGIA! FindOrFail() protege contra borrar datos de otra empresa.
        $repuesto = Repuesto::findOrFail($id);

        if ($repuesto->imagen && Storage::disk('public')->exists($repuesto->imagen)) {
            Storage::disk('public')->delete($repuesto->imagen);
        }

        $repuesto->delete();

        return redirect()->route('repuestos.index')
            ->with('success', '🗑️ Repuesto eliminado correctamente.');
    }

    /**
     * Actualizar cantidad (sumar stock)
     */
    public function updateCantidad(Request $request, $id)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1',
        ]);

        // ¡MAGIA! FindOrFail() protege contra sumar stock a repuestos de otra empresa.
        $repuesto = Repuesto::findOrFail($id);
        $repuesto->increment('cantidad', $request->cantidad);

        return redirect()->route('repuestos.index')
            ->with('success', '📦 Cantidad agregada correctamente al inventario.');
    }
}