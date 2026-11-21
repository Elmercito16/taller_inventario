<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToEmpresa;

class Proveedor extends Model
{
    use HasFactory, BelongsToEmpresa;

    protected $table = 'proveedors';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'contacto',
        'telefono',
        'direccion',
    ];

    public function repuestos()
    {
        return $this->hasMany(Repuesto::class);
    }
}