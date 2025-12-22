<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'tipo_comprobante',
        'serie',
        'correlativo_actual',
        'activo',
        'descripcion',
    ];

    protected $casts = [
        'correlativo_actual' => 'integer',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function comprobantes()
    {
        return $this->hasMany(Comprobante::class, 'serie', 'serie')
                    ->where('tipo_comprobante', $this->tipo_comprobante);
    }

    // Obtener siguiente correlativo
    public function obtenerSiguienteCorrelativo()
    {
        $this->increment('correlativo_actual');
        return $this->correlativo_actual;
    }

    // Scope para series activas
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    // Obtener nombre del tipo de comprobante
    public function getTipoComprobanteNombreAttribute()
    {
        return match($this->tipo_comprobante) {
            '01' => 'Factura',
            '03' => 'Boleta',
            '07' => 'Nota de Crédito',
            '08' => 'Nota de Débito',
            default => 'Desconocido',
        };
    }
}
