<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Permite el acceso solo a usuarios con rol "admin".
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->esAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Solo un administrador puede realizar esta acción.'], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', 'Solo un administrador puede acceder a esa sección.');
        }

        return $next($request);
    }
}
