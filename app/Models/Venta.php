<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Spatie\Multitenancy\Models\Concerns\BelongsToTenant; // 1. IMPORTAR
use Spatie\Multitenancy\Models\Concerns\UsesTenantModel;
class Venta extends Model
{
    use HasFactory, UsesTenantModel; // 2. AÑADIR TRAIT

    // (No necesitas $table, 'ventas' es el plural estándar)

    protected $fillable = [
        'cliente_id',
        'fecha',
        'total',
        'estado',
        // 'empresa_id' se añadirá automáticamente
    ];

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