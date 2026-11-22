<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;

class ProveedorController extends Controller
{
    public function index()
    {
        // Traer todos los proveedores
         // Por esto:
    $proveedores = Proveedor::withCount(['repuestos', 'compras'])
        ->withSum(['compras as total_comprado' => fn ($q) => $q->where('estado', 'registrada')], 'total')
        ->orderBy('nombre', 'asc')
        ->paginate(10); // 👈 10 proveedores por página
    
    return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        // Validar los datos antes de guardar
        $validated = $request->validate([
            'nombre'    => 'required|string|max:255',
            'contacto'  => 'nullable|string|max:255',
            'telefono'  => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
        ]);

        // Guardar proveedor
        Proveedor::create($validated);

        // Redirigir con mensaje de éxito
        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor creado correctamente ✅');
    }

    public function edit($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre'    => 'required|string|max:255',
            'contacto'  => 'nullable|string|max:255',
            'telefono'  => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
        ]);

        $proveedor = Proveedor::findOrFail($id);
        $proveedor->update($validated);

        // Redirigir con mensaje de éxito
        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente ✏️');
    }

    public function destroy($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->delete();

        // Redirigir con mensaje de éxito
        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor eliminado correctamente 🗑️');
    }
}
