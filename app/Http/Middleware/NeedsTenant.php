<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Multitenancy\Models\Tenant;

class NeedsTenant
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario NO está autenticado, continuar (auth middleware lo maneja)
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Validar que el usuario tenga empresa asignada
        if (!$user->empresa_id || !$user->empresa) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login.form')
                ->withErrors(['correo' => 'Tu usuario no tiene una empresa asignada. Contacta al administrador.']);
        }

        // Obtener la empresa (tenant)
        $empresa = $user->empresa;

        if (!$empresa) {
            abort(403, 'No se pudo cargar la empresa asociada a tu usuario.');
        }

        // 👇 ACTIVAR EL TENANT PARA ESTA REQUEST
        $empresa->makeCurrent();

        // 👇 VERIFICACIÓN (DEBUG opcional)
        $current = Tenant::current();
        if (!$current || $current->id !== $empresa->id) {
            Log::warning('Tenant no se activó correctamente', [
                'usuario_id' => $user->id,
                'empresa_esperada' => $empresa->id,
                'tenant_actual' => $current?->id,
            ]);
        }

        return $next($request);
    }
}