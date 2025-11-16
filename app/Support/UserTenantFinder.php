<?php

namespace App\Support;

use Spatie\Multitenancy\TenantFinder\TenantFinder;
use Spatie\Multitenancy\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserTenantFinder extends TenantFinder
{
    public function findForRequest(Request $request): ?Tenant
    {
        // Si el usuario no está autenticado, retorna null
        if (!Auth::check()) {
            return null;
        }

        $user = Auth::user();
        
        // Si el usuario no tiene empresa asignada, retorna null
        if (!$user || !$user->empresa_id) {
            return null;
        }

        // Retorna la empresa (tenant) del usuario
        return \App\Models\Empresa::find($user->empresa_id);
    }
}