<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Multitenancy\Models\Tenant;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'cliente_id',
        'usuario_id',
        'fecha',
        'total',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'total' => 'decimal:2',
    ];

    // ==========================================
    // MULTI-TENANCY (Spatie)
    // ==========================================
    
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

    // ==========================================
    // RELACIONES
    // ==========================================

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

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

    public function comprobante()
    {
        return $this->hasOne(Comprobante::class);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

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
    // MÉTODOS PARA COMPROBANTES ELECTRÓNICOS
    // ==========================================

    /**
     * Verifica si la venta ya tiene un comprobante electrónico
     */
    public function tieneComprobante()
    {
        return $this->comprobante()->exists();
    }

    /**
     * Alias de tieneComprobante() para mantener compatibilidad
     */
    // ✅ DESPUÉS (correcto)
public function tieneComprobanteElectronico()
{
    return $this->comprobante()->exists();
}


    /**
     * Verifica si el comprobante fue aceptado por SUNAT
     */
    public function comprobanteAceptadoPorSunat()
    {
        return $this->comprobante()
                    ->where('estado', 'aceptado')
                    ->exists();
    }

    /**
     * Sugiere el tipo de comprobante según el cliente
     * 01 = Factura (si tiene RUC)
     * 03 = Boleta (si solo tiene DNI)
     */
    public function getTipoComprobanteSugerido()
    {
        if ($this->cliente && !empty($this->cliente->ruc)) {
            return '01'; // Factura
        }
        return '03'; // Boleta
    }

    /**
     * Verifica si se puede emitir un comprobante electrónico
     */
    public function puedeEmitirComprobante()
    {
        // No tiene comprobante
        if ($this->tieneComprobante()) {
            return false;
        }

        // Estado válido (pagado o completada)
        $estadosValidos = ['pagado', 'completada'];
        if (!in_array($this->estado, $estadosValidos)) {
            return false;
        }

        // Tiene detalles
        if (!$this->detalles()->exists()) {
            return false;
        }

        // La empresa tiene facturación activa
        if (!$this->empresa || !$this->empresa->facturacion_activa) {
            return false;
        }

        return true;
    }

    // ==========================================
    // ACCESORIOS (GETTERS)
    // ==========================================

    /**
     * Resumen de la venta
     */
    public function getResumenAttribute()
    {
        $cantidadItems = $this->detalles->sum('cantidad');
        return "Venta #{$this->id} - {$cantidadItems} items - S/ {$this->total}";
    }

    /**
     * Estado del comprobante electrónico
     */
    public function getEstadoComprobanteAttribute()
    {
        if (!$this->tieneComprobante()) {
            return 'sin_comprobante';
        }

        $comprobante = $this->comprobante;

        // Si está anulado
        if ($comprobante->estado === 'anulado') {
            return 'anulado';
        }

        // Retornar estado actual
        return $comprobante->estado;
    }

    /**
     * Número de comprobante (si existe)
     */
    public function getNumeroComprobanteAttribute()
    {
        if (!$this->tieneComprobante()) {
            return null;
        }

        return $this->comprobante->numero_comprobante;
    }

    /**
     * Tipo de comprobante en texto (si existe)
     */
    public function getTipoComprobanteTextoAttribute()
    {
        if (!$this->tieneComprobante()) {
            return null;
        }

        return $this->comprobante->tipo_comprobante_nombre;
    }

    // ==========================================
    // MÉTODOS AUXILIARES
    // ==========================================

    /**
     * Calcular base imponible (sin IGV)
     */
    public function calcularBaseImponible()
    {
        return round($this->total / 1.18, 2);
    }

    /**
     * Calcular IGV (18%)
     */
    public function calcularIgv()
    {
        return round($this->total - $this->calcularBaseImponible(), 2);
    }

    /**
     * Verificar si tiene items válidos para facturar
     */
    public function tieneItemsValidos()
    {
        return $this->detalles()
                    ->whereHas('repuesto')
                    ->where('cantidad', '>', 0)
                    ->where('precio_unitario', '>', 0)
                    ->exists();
    }
}
