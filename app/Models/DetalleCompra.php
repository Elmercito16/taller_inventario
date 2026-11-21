<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use App\Support\Catalogos;
use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    use BelongsToEmpresa;

    protected $table = 'detalle_compras';

    protected $fillable = ['empresa_id', 'compra_id', 'repuesto_id', 'cantidad', 'costo_unitario', 'subtotal'];

    protected $casts = [
        'cantidad' => 'float',
        'costo_unitario' => 'float',
        'subtotal' => 'decimal:2',
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    public function repuesto()
    {
        return $this->belongsTo(Repuesto::class);
    }

    public function getCantidadFormateadaAttribute(): string
    {
        return Catalogos::cantidad($this->cantidad);
    }
}
