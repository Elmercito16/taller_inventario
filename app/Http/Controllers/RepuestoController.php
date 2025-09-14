<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Repuesto;
use App\Models\Proveedor;
use App\Models\Categoria;
use Illuminate\Support\Facades\Storage;

class RepuestoController extends Controller
{
    /**
     * Mostrar el listado de repuestos con buscador y paginación
     */
    public function index(Request $request)
    {
        $query = Repuesto::with(['proveedor', 'categoria'])
            ->orderBy('id', 'desc');

        // ✅ Si hay búsqueda, mostramos solo los que coincidan
        if ($request->filled('q')) {
            $search = $request->q;

            $repuestos = $query->where('nombre', 'like', "%{$search}%")
                               ->orWhere('marca', 'like', "%{$search}%")
                               ->get(); // 🔍 sin paginación en búsqueda
        } else {
            // ✅ Si no hay búsqueda, paginamos
            $repuestos = $query->paginate(12);
        }

        return view('repuestos.index', compact('repuestos'));
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
            'proveedor_id'    => 'nullable|exists:proveedores,id',
            'categoria_id'    => 'required|exists:categorias,id',
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
            'proveedor_id'    => 'nullable|exists:proveedores,id',
            'categoria_id'    => 'required|exists:categorias,id',
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

        $repuesto = Repuesto::findOrFail($id);
        $repuesto->increment('cantidad', $request->cantidad);

        return redirect()->route('repuestos.index')
            ->with('success', '📦 Cantidad agregada correctamente al inventario.');
    }
}
