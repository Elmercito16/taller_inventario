<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $table = 'detalle_ventas'; // Nombre de la tabla

    protected $fillable = [
        'venta_id',
        'repuesto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

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
