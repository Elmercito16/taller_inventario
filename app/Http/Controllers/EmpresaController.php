<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // 👈 AÑADIDO

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

        // 👇 VALIDACIÓN ACTUALIZADA (añadiendo campos SUNAT)
        $validated = $request->validate([
            // Campos existentes
            'nombre'    => 'required|string|max:255',
            'ruc'       => 'nullable|string|max:20|unique:empresas,ruc,' . $empresa->id,
            'direccion' => 'nullable|string|max:255',
            'telefono'  => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:255',
            
            // 👇 CAMPOS NUEVOS PARA SUNAT
            'razon_social' => 'nullable|string|max:255',
            'nombre_comercial' => 'nullable|string|max:255',
            'direccion_fiscal' => 'nullable|string|max:255',
            'ubigeo' => 'nullable|digits:6',
            'departamento' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:100',
            'distrito' => 'nullable|string|max:100',
            'urbanizacion' => 'nullable|string|max:100',
            'web' => 'nullable|string|max:255',
            'ambiente_sunat' => 'nullable|in:beta,produccion',
            'certificado' => 'nullable|file|mimes:pem|max:2048',
        ]);

        // 👇 MANEJAR SUBIDA DE CERTIFICADO (NUEVO)
        if ($request->hasFile('certificado')) {
            $archivo = $request->file('certificado');
            $nombreArchivo = 'certificado-' . $empresa->id . '.pem';
            
            // Guardar en storage/app/sunat/certificados
            $archivo->storeAs('sunat/certificados', $nombreArchivo);
            
            $validated['certificado_path'] = $nombreArchivo;
        }

        // 👇 AÑADIR ESTADO DE FACTURACIÓN (NUEVO)
        $validated['facturacion_activa'] = $request->has('facturacion_activa');

        // 👇 CONVERTIR A MAYÚSCULAS LOS DATOS SUNAT (NUEVO)
        if (isset($validated['razon_social'])) {
            $validated['razon_social'] = strtoupper($validated['razon_social']);
        }
        if (isset($validated['nombre_comercial'])) {
            $validated['nombre_comercial'] = strtoupper($validated['nombre_comercial']);
        }
        if (isset($validated['direccion_fiscal'])) {
            $validated['direccion_fiscal'] = strtoupper($validated['direccion_fiscal']);
        }
        if (isset($validated['departamento'])) {
            $validated['departamento'] = strtoupper($validated['departamento']);
        }
        if (isset($validated['provincia'])) {
            $validated['provincia'] = strtoupper($validated['provincia']);
        }
        if (isset($validated['distrito'])) {
            $validated['distrito'] = strtoupper($validated['distrito']);
        }
        if (isset($validated['urbanizacion'])) {
            $validated['urbanizacion'] = strtoupper($validated['urbanizacion']);
        }

        // Actualizar empresa
        $empresa->update($validated);

        return back()->with('success', '✅ Información de la empresa actualizada correctamente.');
    
    }
}