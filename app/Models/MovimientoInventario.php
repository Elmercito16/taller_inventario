<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use App\Support\Catalogos;
use Illuminate\Database\Eloquent\Model;

/**
 * Movimiento del kardex. Se crea SIEMPRE a través de App\Services\InventarioService.
 */
class MovimientoInventario extends Model
{
    use BelongsToEmpresa;

    protected $table = 'movimientos_inventario';

    public const MOTIVOS = [
        'saldo_inicial'       => 'Saldo inicial',
        'stock_inicial'       => 'Stock inicial',
        'compra'              => 'Compra',
        'anulacion_compra'    => 'Anulación de compra',
        'venta'               => 'Venta',
        'anulacion_venta'     => 'Anulación de venta',
        'reactivacion_venta'  => 'Reactivación de venta',
        'ingreso_manual'      => 'Ingreso manual',
        'ajuste_entrada'      => 'Ajuste (entrada)',
        'ajuste_salida'       => 'Ajuste (salida)',
        'conteo_fisico'       => 'Conteo físico',
        'edicion_producto'    => 'Edición de producto',
    ];

    protected $fillable = [
        'empresa_id', 'repuesto_id', 'tipo', 'motivo', 'cantidad', 'stock_anterior', 'stock_nuevo',
        'costo_unitario', 'referencia_type', 'referencia_id', 'descripcion', 'usuario_id', 'fecha',
    ];

    protected $casts = [
        'cantidad' => 'float',
        'stock_anterior' => 'float',
        'stock_nuevo' => 'float',
        'costo_unitario' => 'float',
        'fecha' => 'datetime',
    ];

    public function repuesto()
    {
        return $this->belongsTo(Repuesto::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function referencia()
    {
        return $this->morphTo();
    }

    public function getMotivoNombreAttribute(): string
    {
        return self::MOTIVOS[$this->motivo] ?? ucfirst(str_replace('_', ' ', $this->motivo));
    }

    public function esEntrada(): bool
    {
        return $this->tipo === 'entrada';
    }

    /** Cantidad con signo: +entrada / -salida. */
    public function getCantidadConSignoAttribute(): float
    {
        return $this->esEntrada() ? $this->cantidad : -$this->cantidad;
    }

    public function getValorAttribute(): ?float
    {
        return $this->costo_unitario !== null ? round($this->cantidad * $this->costo_unitario, 2) : null;
    }

    /** Enlace al documento que originó el movimiento (venta / compra). */
    public function getUrlReferenciaAttribute(): ?string
    {
        return match ($this->referencia_type) {
            Venta::class => route('ventas.show', $this->referencia_id),
            Compra::class => route('compras.show', $this->referencia_id),
            default => null,
        };
    }

    public function getReferenciaTextoAttribute(): ?string
    {
        return match ($this->referencia_type) {
            Venta::class => 'Venta #' . str_pad($this->referencia_id, 6, '0', STR_PAD_LEFT),
            Compra::class => 'Compra #' . str_pad($this->referencia_id, 6, '0', STR_PAD_LEFT),
            default => null,
        };
    }

    public function cantidadTexto(float $valor): string
    {
        return Catalogos::cantidad($valor);
    }
}
