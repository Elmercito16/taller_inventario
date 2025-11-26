<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmpresaController extends Controller
{
    /**
     * Muestra la página de configuración de la empresa.
     */
    public function index()
    {
        // Obtener la empresa del usuario actual
        $empresa = Auth::user()->empresa;
        
        return view('empresa.index', compact('empresa'));
    }

    /**
     * Actualiza los datos de la empresa.
     */
    public function update(Request $request)
    {
        $empresa = Auth::user()->empresa;

        // Validación
        $validated = $request->validate([
            'nombre'    => 'required|string|max:255',
            // Validar RUC único, pero ignorando el RUC actual de esta empresa
            'ruc'       => 'nullable|string|max:20|unique:empresas,ruc,' . $empresa->id,
            'direccion' => 'nullable|string|max:255',
            'telefono'  => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:255',
        ]);

        $empresa->update($validated);

        return back()->with('success', 'Información de la empresa actualizada correctamente.');
    }
}