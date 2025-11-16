<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Empresa;

class NeedsTenant
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario está autenticado
        if ($user = auth()->user()) {
            // Establecer la empresa actual
            $empresa = $user->empresa;
            
            if ($empresa) {
                // Hacer que la empresa sea el tenant actual
                $empresa->makeCurrent();
            }
        }

        return $next($request);
    }
}