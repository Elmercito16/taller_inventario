<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::withCount('repuestos')
            ->orderBy('nombre', 'asc')
            ->paginate(12);

        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->reglas());

        Categoria::create($validated);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $validated = $request->validate($this->reglas($categoria->id));

        $categoria->update($validated);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Categoria $categoria)
    {
        if ($categoria->repuestos()->exists()) {
            return redirect()->route('categorias.index')
                ->with('error', 'No se puede eliminar: la categoría tiene repuestos asignados.');
        }

        $categoria->delete();

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }

    /**
     * El nombre debe ser único solo dentro de la empresa del usuario.
     */
    private function reglas(?int $ignorarId = null): array
    {
        return [
            'nombre' => [
                'required', 'string', 'max:255',
                Rule::unique('categorias', 'nombre')
                    ->where(fn ($q) => $q->where('empresa_id', auth()->user()->empresa_id))
                    ->ignore($ignorarId),
            ],
            'descripcion' => 'nullable|string|max:500',
        ];
    }
}
