<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Support\Facades\DB;

class Serie extends Model
{
    use HasFactory, BelongsToEmpresa;

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
    public function comprobantes()
    {
        return $this->hasMany(Comprobante::class, 'serie', 'serie')
                    ->where('tipo_comprobante', $this->tipo_comprobante);
    }

    // Obtener siguiente correlativo
    /**
     * Reserva el siguiente correlativo de forma atómica (bloqueo de fila),
     * para que dos emisiones simultáneas nunca obtengan el mismo número.
     */
    public function obtenerSiguienteCorrelativo(): int
    {
        return DB::transaction(function () {
            $serie = static::withoutGlobalScope('tenant')->lockForUpdate()->findOrFail($this->id);
            $serie->increment('correlativo_actual');
            $this->correlativo_actual = $serie->correlativo_actual;

            return (int) $serie->correlativo_actual;
        });
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
