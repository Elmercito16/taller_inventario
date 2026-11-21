<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use BelongsToEmpresa;

    public const TIPOS_DOCUMENTO = [
        'factura' => 'Factura',
        'boleta' => 'Boleta',
        'guia' => 'Guía de remisión',
        'nota' => 'Nota de pedido',
        'ninguno' => 'Sin documento',
    ];

    protected $fillable = [
        'empresa_id', 'proveedor_id', 'usuario_id', 'fecha', 'tipo_documento', 'numero_documento',
        'total', 'estado', 'observaciones', 'fecha_anulacion', 'motivo_anulacion',
    ];

    protected $casts = [
        'fecha' => 'date',
        'total' => 'decimal:2',
        'fecha_anulacion' => 'datetime',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class);
    }

    public function movimientos()
    {
        return $this->morphMany(MovimientoInventario::class, 'referencia');
    }

    public function scopeRegistradas($query)
    {
        return $query->where('estado', 'registrada');
    }

    public function estaAnulada(): bool
    {
        return $this->estado === 'anulada';
    }

    public function getNumeroAttribute(): string
    {
        return 'C-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    public function getDocumentoTextoAttribute(): string
    {
        $tipo = self::TIPOS_DOCUMENTO[$this->tipo_documento] ?? 'Documento';

        return $this->numero_documento ? "{$tipo} {$this->numero_documento}" : $tipo;
    }
}
