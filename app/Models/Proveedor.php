<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // 1. AÑADIR (buena práctica)
use Illuminate\Database\Eloquent\Model;
//use Spatie\Multitenancy\Models\Concerns\BelongsToTenant; // 2. IMPORTAR
use App\Models\Repuesto; // Importar el modelo usado
use Spatie\Multitenancy\Models\Concerns\UsesTenantModel;

class Proveedor extends Model
{
    use HasFactory, UsesTenantModel; // 3. AÑADIR TRAITS

    protected $table = 'proveedor'; // 👈 ¡CORRECTO! nombre de la tabla en tu BD

    protected $fillable = [
        'nombre',
        'contacto', // antes lo llamabas "email"
        'telefono',
        'direccion',
        // 'empresa_id' se añadirá automáticamente
    ];

    /**
     * Relación: Un proveedor pertenece a UNA empresa (Tenant).
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Define la relación: Un proveedor tiene muchos repuestos
     */
    public function repuestos()
    {
        return $this->hasMany(Repuesto::class);
    }
}