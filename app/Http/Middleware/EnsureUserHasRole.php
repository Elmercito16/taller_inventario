<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restringe una ruta a ciertos roles. Uso: ->middleware('rol:admin,encargado')
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! in_array($request->user()?->rol, $roles, true)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permiso para realizar esta acción.'], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', 'No tienes permiso para acceder a esa sección.');
        }

        return $next($request);
    }
}
