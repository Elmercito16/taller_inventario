<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory, BelongsToEmpresa;

    public const METODOS_PAGO = [
        'efectivo'      => 'Efectivo',
        'yape'          => 'Yape / Plin',
        'tarjeta'       => 'Tarjeta',
        'transferencia' => 'Transferencia',
    ];

    protected $fillable = [
        'empresa_id',
        'cliente_id',
        'usuario_id',
        'fecha',
        'total',
        'estado',
        'metodo_pago',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'total' => 'decimal:2',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    /**
     * Último comprobante emitido para la venta (puede haber intentos rechazados previos).
     */
    public function comprobante()
    {
        return $this->hasOne(Comprobante::class)->latestOfMany();
    }

    public function comprobantes()
    {
        return $this->hasMany(Comprobante::class);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopePagadas($query)
    {
        return $query->where('estado', 'pagado');
    }

    public function scopeConComprobante($query)
    {
        return $query->has('comprobante');
    }

    public function scopeSinComprobante($query)
    {
        return $query->doesntHave('comprobante');
    }

    // ==========================================
    // COMPROBANTES ELECTRÓNICOS
    // ==========================================

    /**
     * ¿Tiene un comprobante vigente? (aceptado/observado/pendiente y no anulado).
     * Los rechazados no cuentan, así se puede volver a intentar la emisión.
     */
    public function tieneComprobante(): bool
    {
        return $this->comprobantes()
            ->whereIn('estado_sunat', ['aceptado', 'observado', 'pendiente'])
            ->where('anulado', false)
            ->exists();
    }

    public function tieneComprobanteElectronico(): bool
    {
        return $this->tieneComprobante();
    }

    public function comprobanteAceptadoPorSunat(): bool
    {
        return $this->comprobantes()
            ->whereIn('estado_sunat', ['aceptado', 'observado'])
            ->where('anulado', false)
            ->exists();
    }

    public function getTipoComprobanteSugerido(): string
    {
        return $this->cliente && $this->cliente->tieneRuc() ? '01' : '03';
    }

    public function puedeEmitirComprobante(): bool
    {
        if ($this->estado !== 'pagado' || $this->tieneComprobante()) {
            return false;
        }

        if (! $this->detalles()->exists()) {
            return false;
        }

        return $this->empresa && $this->empresa->facturacion_activa;
    }

    // ==========================================
    // ACCESORIOS
    // ==========================================

    public function getResumenAttribute(): string
    {
        $cantidadItems = $this->detalles->sum('cantidad');

        return "Venta #{$this->id} - {$cantidadItems} items - S/ {$this->total}";
    }

    public function getMetodoPagoNombreAttribute(): string
    {
        return self::METODOS_PAGO[$this->metodo_pago] ?? 'Efectivo';
    }

    public function getEstadoComprobanteAttribute(): string
    {
        $comprobante = $this->comprobante;

        if (! $comprobante) {
            return 'sin_comprobante';
        }

        return $comprobante->anulado ? 'anulado' : $comprobante->estado_sunat;
    }

    public function getNumeroComprobanteAttribute(): ?string
    {
        return $this->comprobante?->numero_completo;
    }

    public function getTipoComprobanteTextoAttribute(): ?string
    {
        return $this->comprobante?->tipo_comprobante_nombre;
    }

    // ==========================================
    // AUXILIARES (precios con IGV incluido)
    // ==========================================

    /**
     * Base imponible (operaciones gravadas, sin IGV).
     * Solo las líneas gravadas incluyen IGV; exoneradas/inafectas se suman aparte.
     */
    public function calcularBaseImponible(): float
    {
        return round($this->detalles->filter->esGravado()->sum(fn ($d) => $d->subtotal_sin_igv), 2);
    }

    public function calcularNoGravado(): float
    {
        return round($this->detalles->reject->esGravado()->sum('subtotal'), 2);
    }

    public function calcularIgv(): float
    {
        return round($this->detalles->filter->esGravado()->sum(fn ($d) => $d->igv), 2);
    }

    public function tieneItemsValidos(): bool
    {
        return $this->detalles()
            ->whereHas('repuesto')
            ->where('cantidad', '>', 0)
            ->where('precio_unitario', '>', 0)
            ->exists();
    }
}
