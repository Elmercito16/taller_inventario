<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repuesto extends Model
{
    use HasFactory;

    // Campos que se pueden asignar masivamente
    protected $fillable = [
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
        'imagen' // nuevo

    ];

    // Valores por defecto
    protected $attributes = [
        'minimo_stock' => 0,
    ];

    /**
     * Relación con categoría
     * Un repuesto pertenece a una categoría
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /**
     * Relación con proveedor
     * Un repuesto pertenece a un proveedor
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }
}
