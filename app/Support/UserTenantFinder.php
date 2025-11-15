<?php

namespace App\Support;

use Spatie\Multitenancy\Concerns\FindsTenants;
use Spatie\Multitenancy\Models\Tenant;

class UserTenantFinder implements FindsTenants
{
    /**
     * Encuentra el tenant actual basándose en el usuario autenticado.
     */
    public function find(): ?Tenant
    {
        // Obtener el usuario autenticado
        $user = auth()->user();

        // Si no hay usuario, retornar null
        if (!$user) {
            return null;
        }

        // Retornar la empresa del usuario
        return $user->empresa;
    }
}