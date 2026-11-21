<?php

namespace App\Models\Concerns;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\Multitenancy\Models\Tenant;

/**
 * Aísla los registros por empresa (multi-tenant).
 *
 * - Al leer: filtra por la empresa activa (Tenant::current()) o, si todavía no
 *   se activó (p. ej. durante el route-model-binding), por la empresa del
 *   usuario autenticado. Si hay un usuario logueado SIN empresa, no devuelve nada
 *   (falla "cerrado" en lugar de mostrar datos de todas las empresas).
 * - Al crear: asigna automáticamente la empresa.
 *
 * En consola (artisan, seeders) sin usuario ni tenant no se filtra.
 */
trait BelongsToEmpresa
{
    public static function bootBelongsToEmpresa(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            $empresaId = static::empresaIdActual();

            if ($empresaId !== null) {
                $builder->where($builder->getModel()->getTable() . '.empresa_id', $empresaId);
            } elseif (Auth::hasUser()) {
                // Usuario autenticado sin empresa: no mostrar nada.
                $builder->whereRaw('1 = 0');
            }
        });

        static::creating(function ($model) {
            $empresaId = static::empresaIdActual();
            if ($empresaId !== null) {
                $model->empresa_id = $empresaId;
            }
        });
    }

    /**
     * Empresa activa: tenant actual o empresa del usuario autenticado.
     */
    public static function empresaIdActual(): ?int
    {
        if ($tenant = Tenant::current()) {
            return (int) $tenant->getKey();
        }

        // Evita disparar la autenticación desde consola.
        if (app()->runningInConsole() && ! Auth::hasUser()) {
            return null;
        }

        $user = Auth::user();

        return $user && $user->empresa_id ? (int) $user->empresa_id : null;
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}
