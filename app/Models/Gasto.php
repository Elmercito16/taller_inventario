<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder; // 👈 Importante para el scope
use Spatie\Multitenancy\Models\Tenant;   // 👈 Importante para obtener el tenant actual

class Gasto extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'descripcion',
        'monto',
        'fecha',
        'categoria'
    ];

    /**
     * Lógica manual de Multi-Tenancy.
     * Esto reemplaza al Trait 'UsesTenantModel' si te daba errores.
     */
    protected static function booted()
    {
        // 1. Al leer (SELECT): Filtrar siempre por la empresa actual
        static::addGlobalScope('tenant', function (Builder $builder) {
            if ($tenant = Tenant::current()) {
                $builder->where('empresa_id', $tenant->id);
            }
        });

        // 2. Al crear (INSERT): Asignar automáticamente el ID de la empresa
        static::creating(function ($model) {
            if ($tenant = Tenant::current()) {
                $model->empresa_id = $tenant->id;
            }
        });
    }

    /**
     * Relación: Un gasto pertenece a UNA empresa.
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
    
    /**
     * Requerido si en algún momento usas métodos del paquete Spatie explícitamente
     */
    public function getTenantKeyName(): string
    {
        return 'empresa_id';
    }
}