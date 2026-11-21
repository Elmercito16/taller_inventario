<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToEmpresa;

class DetalleVenta extends Model
{
    use HasFactory, BelongsToEmpresa;

    protected $table = 'detalle_ventas';

    protected $fillable = [
        'empresa_id',
        'venta_id',
        'repuesto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'costo_unitario',
        'unidad_medida',
        'afectacion_igv',
    ];
     protected $casts = [
        'cantidad' => 'float',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'costo_unitario' => 'decimal:2',
    ];

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

    /** ¿La línea está gravada con IGV? (exonerado/inafecto no lo incluyen) */
    public function esGravado(): bool
    {
        return ($this->afectacion_igv ?? '10') === '10';
    }

    // Precio unitario sin IGV (para comprobantes SUNAT)
    public function getPrecioUnitarioSinIgvAttribute()
    {
        return $this->esGravado() ? round($this->precio_unitario / 1.18, 2) : round((float) $this->precio_unitario, 2);
    }

    // Subtotal sin IGV
    public function getSubtotalSinIgvAttribute()
    {
        return $this->esGravado() ? round($this->subtotal / 1.18, 2) : round((float) $this->subtotal, 2);
    }

    // IGV de la línea
    public function getIgvAttribute()
    {
        return round($this->subtotal - $this->subtotal_sin_igv, 2);
    }

    // Cantidad legible (sin ceros sobrantes)
    public function getCantidadFormateadaAttribute(): string
    {
        return \App\Support\Catalogos::cantidad($this->cantidad);
    }
}
