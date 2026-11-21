<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToEmpresa;

class Categoria extends Model
{
    use HasFactory, BelongsToEmpresa;

    protected $fillable = [
        'empresa_id',
        'nombre',
        'descripcion',
    ];

    public function repuestos()
    {
        return $this->hasMany(Repuesto::class);
    }
}