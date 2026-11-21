<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gasto extends Model
{
    use HasFactory, BelongsToEmpresa, SoftDeletes;

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
    ];

    protected $fillable = [
        'empresa_id',
        'descripcion',
        'monto',
        'fecha',
        'categoria'
    ];
}