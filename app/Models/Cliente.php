<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // 1. AÑADIR (buena práctica)
use Illuminate\Database\Eloquent\Model;
//use Spatie\Multitenancy\Models\Concerns\BelongsToTenant; // 2. IMPORTAR
use Spatie\Multitenancy\Models\Concerns\UsesTenantModel;
class Cliente extends Model
{
    use HasFactory, UsesTenantModel; // 3. AÑADIR TRAITS

    /**
     * El nombre de la tabla (opcional, 'clientes' es el estándar).
     */
    protected $table = 'clientes';

    protected $fillable = [
        'dni', 
        'nombre', 
        'telefono', 
        'direccion', 
        'email'
        // 'empresa_id' se añadirá automáticamente
    ];

    /**
     * Relación: Un cliente pertenece a UNA empresa (Tenant).
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Relación existente: Un cliente tiene muchas ventas.
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}