<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /**
     * Mostrar formulario de registro
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Guardar un nuevo usuario
     */
    public function store(Request $request)
    {
        // Validar datos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|unique:usuarios,correo',
            'contraseña' => 'required|string|min:6',
            'rol' => 'required|in:admin,usuario',
        ]);

        // Crear usuario
        Usuario::create([
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'contraseña' => Hash::make($request->contraseña), // 🔐 Encripta la contraseña
            'rol' => $request->rol,
        ]);

        // Redirigir al login
        return redirect()->route('login')->with('success', 'Usuario registrado correctamente. Ahora inicia sesión.');
    }
}
