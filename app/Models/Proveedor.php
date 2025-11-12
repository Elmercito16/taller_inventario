<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedor'; // 👈 nombre de la tabla en tu BD

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
/**
     * Define la relación: Un proveedor tiene muchos repuestos
     */
    public function repuestos()
    {
        return $this->hasMany(Repuesto::class);
    }







}