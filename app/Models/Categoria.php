<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Repuesto; // Importamos el modelo relacionado

class Categoria extends Model
{
    use HasFactory;

    // Nombre de la tabla (opcional si sigue convención plural)
    protected $table = 'categorias';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    /**
     * Relación con repuestos
     * Una categoría puede tener muchos repuestos
     */
    public function repuestos()
    {
        return $this->hasMany(Repuesto::class, 'categoria_id');
    }
}
