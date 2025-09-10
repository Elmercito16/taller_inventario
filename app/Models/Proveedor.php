<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedors'; // 👈 nombre de la tabla en tu BD

    protected $fillable = [
        'nombre',
        'contacto', // antes lo llamabas "email"
        'telefono',
        'direccion',
    ];
    public function getRouteKeyName()
{
    return 'id';
}
}