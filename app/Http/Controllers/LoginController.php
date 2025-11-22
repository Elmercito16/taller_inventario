<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario; // Modelo de usuarios
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; // 👈 ¡IMPORTANTE! Importar Auth

class LoginController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLoginForm()
    {
        // Si ya está logueado, mandar al dashboard
        if (Auth::check()) {
            return redirect()->route('repuestos.index');
        }
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

        // Verificar contraseña
        if ($usuario && Hash::check($request->contraseña, $usuario->contraseña)) {
            
            // ✅ ¡AQUÍ ESTÁ LA CORRECCIÓN!
            // En lugar de session()->put(), usamos Auth::login()
            // Esto registra al usuario en Laravel, activa el middleware y el multi-tenant.
            Auth::login($usuario);

            // Regenerar sesión por seguridad (evita ataques de fijación de sesión)
            $request->session()->regenerate();

            // Redirigir según el rol
            if ($usuario->rol === 'admin') {
                // Usamos intended() para redirigir a donde quería ir el usuario
                return redirect()->intended(route('clientes.index'))
                                 ->with('success', 'Bienvenido Administrador: ' . $usuario->nombre);
            }

            return redirect()->intended(route('clientes.index'))
                             ->with('success', 'Bienvenido, ' . $usuario->nombre);
        }

        // Si falla
        return back()->withErrors([
            'correo' => 'Credenciales incorrectas. Intenta nuevamente.',
        ])->withInput();
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        // ✅ Cerrar sesión correctamente en Laravel
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form')
                         ->with('success', 'Sesión cerrada correctamente.');
    }
}