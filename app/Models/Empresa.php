<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Tenant;

class Empresa extends Tenant
{
    use HasFactory;

    /**
     * Nombre de la tabla
     */
    protected $table = 'empresas';

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'nombre',
        'ruc',           // 👈 RECOMENDADO: Añadir RUC
        'direccion',     // 👈 RECOMENDADO
        'telefono',      // 👈 RECOMENDADO
        'email',         // 👈 RECOMENDADO
    ];

    /**
     * MÉTODO REQUERIDO POR SPATIE MULTITENANCY V4
     * Retorna el nombre de la columna que identifica al tenant
     */
    public function getTenantKeyName(): string
    {
        return 'empresa_id';
    }

    /**
     * Define la relación: una empresa tiene muchos usuarios.
     */
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'empresa_id');
    }

    /**
     * Una empresa tiene muchos repuestos
     */
    public function repuestos()
    {
        return $this->hasMany(Repuesto::class, 'empresa_id');
    }

    /**
     * Una empresa tiene muchas ventas
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'empresa_id');
    }

    /**
     * Una empresa tiene muchos clientes
     */
    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'empresa_id');
    }

    /**
     * Una empresa tiene muchas categorías
     */
    public function categorias()
    {
        return $this->hasMany(Categoria::class, 'empresa_id');
    }

    /**
     * Una empresa tiene muchos proveedores
     */
    public function proveedores()
    {
        return $this->hasMany(Proveedor::class, 'empresa_id');
    }
}