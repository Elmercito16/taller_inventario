<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Repuesto;
use App\Models\Proveedor;
use App\Models\Categoria;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // Asegúrate de tener esto

class RepuestoController extends Controller
{
    /**
     * Mostrar el listado de repuestos con buscador y paginación
     */
    public function index(Request $request)
    {
        // Esta función ya la habíamos corregido y está bien
        $categorias = Categoria::orderBy('nombre')->get();

        $query = Repuesto::with(['proveedor', 'categoria'])
            ->orderBy('id', 'desc');

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('marca', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%");
            });
        }
        
        // Clonamos la consulta ANTES de paginar para obtener los totales
        $statsQuery = clone $query;
        $valorTotal = (clone $statsQuery)->sum(DB::raw('cantidad * precio_unitario'));
        $stockBajoCount = (clone $statsQuery)->whereRaw('cantidad <= minimo_stock')->count();
        $categoriasCount = (clone $statsQuery)->distinct()->count('categoria_id');
        $repuestosStockBajo = (clone $statsQuery)
            ->whereRaw('cantidad <= minimo_stock AND cantidad > 0')
            ->get();
        $repuestosAgotados = (clone $statsQuery)
            ->where('cantidad', '<=', 0)
            ->get();
        
        $repuestos = $query->paginate(12)->appends($request->query());

        return view('repuestos.index', compact(
            'repuestos', 
            'categorias',
            'valorTotal',
            'stockBajoCount',
            'categoriasCount',
            'repuestosStockBajo',
            'repuestosAgotados'
        ));
    }

    /**
     * Mostrar el formulario para crear un nuevo repuesto
     */
    public function create()
    {
        $proveedores = Proveedor::all();
        $categorias  = Categoria::all();

        // Generar código automático
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
        $validated = $request->validate([
            'nombre'          => 'required|string|max:255',
            'marca'           => 'required|string|max:255',
            'cantidad'        => 'required|integer|min:0',
            'precio_unitario' => 'required|numeric|min:0',
            
            // --- ¡AQUÍ ESTÁ LA CORRECCIÓN! ---
            // Cambiamos 'proveedores' por 'proveedor' (o el nombre real de tu tabla)
            'proveedor_id'    => 'nullable|exists:proveedor,id', 
            
            'categoria_id'    => 'required|exists:categorias,id', // Asumo que esta sí se llama 'categorias'
            'descripcion'     => 'nullable|string',
            'fecha_ingreso'   => 'nullable|date',
            'codigo'          => 'required|string|unique:repuestos,codigo',
            'minimo_stock'    => 'nullable|integer|min:0',
            'imagen'          => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('repuestos', 'public');
        }

        Repuesto::create($validated);

        return redirect()->route('repuestos.index')
            ->with('success', '✅ Repuesto agregado correctamente.');
    }

    /**
     * Mostrar formulario de edición
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
        $repuesto = Repuesto::findOrFail($id);

        $validated = $request->validate([
            'nombre'          => 'required|string|max:255',
            'marca'           => 'required|string|max:255',
            'cantidad'        => 'required|integer|min:0',
            'precio_unitario' => 'required|numeric|min:0',
            
            // --- ¡AQUÍ ESTÁ LA CORRECCIÓN! ---
            // Cambiamos 'proveedores' por 'proveedor' (o el nombre real de tu tabla)
            'proveedor_id'    => 'nullable|exists:proveedor,id', 
            
            'categoria_id'    => 'required|exists:categorias,id', // Asumo que esta sí se llama 'categorias'
            'descripcion'     => 'nullable|string',
            'fecha_ingreso'   => 'nullable|date',
            'minimo_stock'    => 'nullable|integer|min:0',
            'imagen'          => 'nullable|image|max:2048',
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
// ... (código existente) ...
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
// ... (código existente) ...
        $request->validate([
            'cantidad' => 'required|integer|min:1',
        ]);

        $repuesto = Repuesto::findOrFail($id);
        $repuesto->increment('cantidad', $request->cantidad);

        return redirect()->route('repuestos.index')
            ->with('success', '📦 Cantidad agregada correctamente al inventario.');
    }
}