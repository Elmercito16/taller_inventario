<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder; // 👈 ESTE ES EL CORRECTO
use Spatie\Multitenancy\Models\Tenant; // 👈 AGREGA ESTA LÍNEA

//use Spatie\Multitenancy\Models\Concerns\BelongsToTenant; // 1. IMPORTAR
class Venta extends Model
{
    use HasFactory; // 2. AÑADIR TRAIT

    // (No necesitas $table, 'ventas' es el plural estándar)

    protected $fillable = [
        'empresa_id',
        'cliente_id',
        'fecha',
        'total',
        'estado',
        // 'empresa_id' se añadirá automáticamente
    ];

     protected static function booted()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if ($tenant = Tenant::current()) {
                $builder->where('empresa_id', $tenant->id);
            }
        });

        static::creating(function ($model) {
            if ($tenant = Tenant::current()) {
                $model->empresa_id = $tenant->id;
            }
        });
    }
    

    /**
     * Relación: Una venta pertenece a UNA empresa (Tenant).
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    // --- Tus relaciones existentes ---

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }
}