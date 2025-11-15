<?php

namespace App\Multitenancy;

use Illuminate\Http\Request;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\TenantFinder\TenantFinder;

/*
 * Esta clase es el "cerebro".
 * Su trabajo es decirle a Spatie qué empresa está activa,
 * basándose en el usuario que ha iniciado sesión.
 */
class UserTenantFinder extends TenantFinder
{
    public function findForRequest(Request $request): ?Tenant
    {
        // Si hay un usuario con sesión iniciada...
        if (auth()->check()) {
            // ...devuelve la empresa de ese usuario.
            // Esto activa el filtro global para todas las consultas.
            // Nota: auth()->user()->empresa() viene de la relación que definiremos.
            return auth()->user()->empresa;
        }

        // Si no hay sesión (ej. en la página de login), no devuelve nada
        // y permite el acceso.
        return null;
    }
}