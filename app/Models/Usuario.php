<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Multitenancy\Models\Concerns\UsesTenantModel; // 👈 CAMBIADO

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable, UsesTenantModel; // 👈 CAMBIADO

    /**
     * El nombre de la tabla en tu base de datos.
     */
    protected $table = 'usuarios';

    /**
     * Mapear 'correo' a 'email' para el sistema de login de Laravel.
     */
    public function getAuthIdentifierName()
    {
        return 'correo';
    }

    /**
     * Mapear 'contraseña' a 'password' para el sistema de login.
     */
    public function getAuthPassword()
    {
        return $this->{"contraseña"};
    }

    /**
     * Columnas que se pueden llenar masivamente.
     */
    protected $fillable = [
        'nombre',
        'correo',
        'contraseña',
        'rol',
        'empresa_id',
    ];

    /**
     * Atributos que deben ocultarse.
     */
    protected $hidden = [
        'contraseña',
        'remember_token',
    ];

    /**
     * Define la relación: un usuario pertenece a UNA empresa.
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
    
    /**
     * MÉTODO REQUERIDO POR SPATIE MULTITENANCY V4
     * Define qué tenant (empresa) está asociado a este usuario
     */
    public function getTenant()
    {
        return $this->empresa;
    }

    // --- Mapeadores Opcionales ---
    public function getNameAttribute()
    {
        return $this->nombre;
    }

    public function getEmailAttribute()
    {
        return $this->correo;
    }
}