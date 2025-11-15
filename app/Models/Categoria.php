<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Repuesto; // Importamos el modelo relacionado
//use Spatie\Multitenancy\Models\Concerns\BelongsToTenant; // 1. IMPORTAR
use Spatie\Multitenancy\Models\Concerns\UsesTenantModel;

class Categoria extends Model
{
    use HasFactory, UsesTenantModel ; // 2. AÑADIR TRAIT

    // Nombre de la tabla (opcional si sigue convención plural)
    protected $table = 'categorias';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'nombre',
        'descripcion',
        // 'empresa_id' se añadirá automáticamente por el paquete
    ];

    /**
     * Relación: Una categoría pertenece a UNA empresa (Tenant).
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Relación con repuestos
     * Una categoría puede tener muchos repuestos
     */
    public function repuestos()
    {
        return $this->hasMany(Repuesto::class, 'categoria_id');
    }
}