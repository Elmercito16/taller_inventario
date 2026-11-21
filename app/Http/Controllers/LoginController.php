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
            return redirect()->route('dashboard');
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
        $usuario = Usuario::where('correo', trim($request->correo))->first();

        // Verificar contraseña
        if ($usuario && Hash::check($request->contraseña, $usuario->contraseña)) {

            // ✅ Verificamos que el usuario tenga empresa ANTES de continuar
            if (!$usuario->empresa_id) {
                return back()->withErrors([
                    'correo' => 'Tu usuario no tiene una empresa asignada. Contacta al administrador.',
                ])->withInput();
            }

            // Registramos al usuario en Laravel (activa sesión y middlewares)
            Auth::login($usuario);

            // Regenerar sesión por seguridad (evita ataques de fijación de sesión)
            $request->session()->regenerate();

            // 👇 ¡LA CORRECCIÓN CLAVE!
            // Activamos manualmente el tenant (empresa) justo después del login,
            // sin depender del orden de ejecución de los middlewares globales.
            $empresa = $usuario->empresa; // Carga la relación empresa()
            if ($empresa) {
                $empresa->makeCurrent();
            }

            return redirect()->intended(route('dashboard'))
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
        // Olvidamos el tenant activo antes de cerrar sesión
        if (\Spatie\Multitenancy\Models\Tenant::checkCurrent()) {
            \Spatie\Multitenancy\Models\Tenant::current()->forgetCurrent();
        }

        // ✅ Cerrar sesión correctamente en Laravel
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form')
                         ->with('success', 'Sesión cerrada correctamente.');
    }
}