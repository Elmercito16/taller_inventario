<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Spatie\Multitenancy\Models\Concerns\BelongsToTenant; // 1. IMPORTAR
use Spatie\Multitenancy\Models\Concerns\UsesTenantModel;


class Repuesto extends Model
{
    use HasFactory, UsesTenantModel; // 2. AÑADIR TRAIT

    // (No necesitas $table, 'repuestos' es el plural estándar de Laravel)

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
        // 'empresa_id' se añadirá automáticamente
    ];

    // Valores por defecto
    protected $attributes = [
        'minimo_stock' => 0,
    ];

    /**
     * Relación: Un repuesto pertenece a UNA empresa (Tenant).
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

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
        // Apunta a tu modelo Proveedor (que ya tiene $table = 'proveedor')
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

}