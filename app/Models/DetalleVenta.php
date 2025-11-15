<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Spatie\Multitenancy\Models\Concerns\BelongsToTenant; // 1. IMPORTAR
use Spatie\Multitenancy\Models\Concerns\UsesTenantModel;

class DetalleVenta extends Model
{
    use HasFactory, UsesTenantModel; // 2. AÑADIR TRAIT

    protected $table = 'detalle_ventas'; // Nombre de la tabla

    protected $fillable = [
        'venta_id',
        'repuesto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        // 'empresa_id' se añadirá automáticamente
    ];

    /**
     * Relación: Un detalle de venta pertenece a UNA empresa (Tenant).
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    // Relación con la venta
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    // Relación con el repuesto
    public function repuesto()
    {
        return $this->belongsTo(Repuesto::class);
    }
}