<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario; // Modelo de usuarios
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'contraseña' => 'required|string|min:6',
        ]);

        // Buscar usuario por correo
        $usuario = Usuario::where('correo', $request->correo)->first();

        if ($usuario && Hash::check($request->contraseña, $usuario->contraseña)) {
            // Guardar sesión manual
            $request->session()->put([
                'usuario_id' => $usuario->id,
                'nombre'     => $usuario->nombre,
                'rol'        => $usuario->rol,
            ]);

            // Redirigir según el rol
            if ($usuario->rol === 'admin') {
                return redirect()->route('clientes.index')
                                 ->with('success', 'Bienvenido Administrador: ' . $usuario->nombre);
            }

            return redirect()->route('clientes.index')
                             ->with('success', 'Bienvenido, ' . $usuario->nombre);
        }

        return back()->withErrors([
            'correo' => 'Credenciales incorrectas. Intenta nuevamente.',
        ])->withInput();
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        $request->session()->flush(); // Eliminar toda la sesión
        return redirect()->route('login.form')
                         ->with('success', 'Sesión cerrada correctamente.');
    }
}
