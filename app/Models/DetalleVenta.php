<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Multitenancy\Models\Tenant;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $table = 'detalle_ventas';

    protected $fillable = [
        'empresa_id',
        'venta_id',
        'repuesto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];
     protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
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

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function repuesto()
    {
        return $this->belongsTo(Repuesto::class);
    }
     // ==========================================
    // 👇 MÉTODOS NUEVOS PARA GREENTER
    // ==========================================

    // Accesor para total del detalle
    public function getTotalAttribute()
    {
        return $this->subtotal;
    }

    // Accesor para precio sin IGV (para comprobantes SUNAT)
    public function getPrecioUnitarioSinIgvAttribute()
    {
        return round($this->precio_unitario / 1.18, 2);
    }

    // Accesor para subtotal sin IGV
    public function getSubtotalSinIgvAttribute()
    {
        return round($this->subtotal / 1.18, 2);
    }

    // Calcular IGV del detalle
    public function getIgvAttribute()
    {
        return round($this->subtotal - $this->subtotal_sin_igv, 2);
    }
}