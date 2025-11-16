<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Multitenancy\Models\Tenant;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedor';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'contacto',
        'telefono',
        'direccion',
    ];

    protected static function booted()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if ($tenant = Tenant::current()) {
                $builder->where('empresa_id', $tenant->id);
            }
        });

        static::creating(function ($model) {
            if ($tenant = Tenant::current()) {
                $model->empresa_id = $tenant->id;
            }
        });
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function repuestos()
    {
        return $this->hasMany(Repuesto::class);
    }
}