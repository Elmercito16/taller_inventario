<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Multitenancy\Models\Tenant;

class Repuesto extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', // 👈 IMPORTANTE: debe estar aquí
        'codigo',
        'nombre',
        'marca',
        'descripcion',
        'cantidad',
        'minimo_stock',
        'precio_unitario',
        'proveedor_id',
        'categoria_id',
        'fecha_ingreso',
        'imagen',
    ];

    protected $attributes = [
        'minimo_stock' => 0,
    ];

    /**
     * Boot del modelo - aplica scope automático
     */
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

        // Genera código secuencial si no se proporciona
        if (empty($model->codigo)) {
            $lastRepuesto = static::withoutGlobalScope('tenant')
                ->where('empresa_id', $model->empresa_id)
                ->orderBy('id', 'desc')
                ->first();
            
            $nextNumber = $lastRepuesto ? (int)substr($lastRepuesto->codigo, 4) + 1 : 1;
            $model->codigo = 'REP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }
    });
}

    /**
     * Relación: Un repuesto pertenece a UNA empresa (Tenant).
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Relación con categoría
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /**
     * Relación con proveedor
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }
}