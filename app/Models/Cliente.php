<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Multitenancy\Models\Tenant;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'nombre',
        'dni',
        'ruc',
        'telefono',
        'email',
        'direccion',
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

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    public function comprobantes()
    {
        return $this->hasMany(Comprobante::class);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeConRuc($query)
    {
        return $query->whereNotNull('ruc')->where('ruc', '!=', '');
    }

    public function scopeSoloConDni($query)
    {
        return $query->where(function($q) {
            $q->whereNull('ruc')->orWhere('ruc', '');
        });
    }

    // ==========================================
    // MÉTODOS PARA GREENTER / SUNAT
    // ==========================================

    /**
     * Verifica si el cliente tiene RUC válido (11 dígitos)
     */
    public function tieneRuc()
    {
        return !empty($this->ruc) && strlen($this->ruc) === 11;
    }

    /**
     * Código de tipo de documento según catálogo SUNAT
     * 1 = DNI
     * 6 = RUC
     * 4 = Carnet de extranjería
     * 7 = Pasaporte
     * 0 = Otros
     */
    public function getTipoDocumentoSunat()
    {
        if ($this->tieneRuc()) {
            return '6'; // RUC
        }
        
        // Si tiene DNI (8 dígitos)
        if (!empty($this->dni) && strlen($this->dni) === 8) {
            return '1'; // DNI
        }
        
        return '0'; // Otros (sin documento)
    }

    /**
     * Obtiene el número de documento principal
     */
    public function getNumeroDocumento()
    {
        return $this->tieneRuc() ? $this->ruc : ($this->dni ?? '-');
    }

    /**
     * Verifica si puede emitir factura (requiere RUC)
     */
    public function puedeEmitirFactura()
    {
        return $this->tieneRuc();
    }

    /**
     * Tipo de comprobante recomendado
     * 01 = Factura (si tiene RUC)
     * 03 = Boleta (si no tiene RUC)
     */
    public function getTipoComprobanteRecomendado()
    {
        return $this->tieneRuc() ? '01' : '03';
    }

    // ==========================================
    // ACCESORIOS (GETTERS)
    // ==========================================

    /**
     * Nombre del tipo de documento (para mostrar)
     */
    public function getTipoDocumentoAttribute()
    {
        return $this->tieneRuc() ? 'RUC' : 'DNI';
    }

    /**
     * Número de documento principal (accessor)
     */
    public function getNumeroDocumentoAttribute()
    {
        return $this->getNumeroDocumento();
    }

    /**
     * Tipo de comprobante recomendado (accessor)
     */
    public function getTipoComprobanteRecomendadoAttribute()
    {
        return $this->getTipoComprobanteRecomendado();
    }

    /**
     * Documento formateado para mostrar
     */
    public function getDocumentoFormateadoAttribute()
    {
        if ($this->tieneRuc()) {
            return "RUC: {$this->ruc}";
        }
        
        if (!empty($this->dni)) {
            return "DNI: {$this->dni}";
        }
        
        return "Sin documento";
    }

    /**
     * Nombre completo con documento
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} ({$this->documento_formateado})";
    }

    // ==========================================
    // VALIDACIONES PARA GREENTER
    // ==========================================

    /**
     * Verifica si el cliente tiene datos mínimos para facturación
     */
    public function tieneDatosCompletosParaFacturacion()
    {
        // Tiene nombre
        if (empty($this->nombre)) {
            return false;
        }

        // Tiene documento (RUC o DNI)
        if (empty($this->ruc) && empty($this->dni)) {
            return false;
        }

        // Si va a emitir factura, necesita dirección
        if ($this->tieneRuc() && empty($this->direccion)) {
            return false;
        }

        return true;
    }

    /**
     * Obtiene los errores de validación para facturación
     */
    public function getErroresFacturacion()
    {
        $errores = [];

        if (empty($this->nombre)) {
            $errores[] = 'El cliente debe tener un nombre';
        }

        if (empty($this->ruc) && empty($this->dni)) {
            $errores[] = 'El cliente debe tener RUC o DNI';
        }

        if ($this->tieneRuc() && empty($this->direccion)) {
            $errores[] = 'El cliente con RUC debe tener dirección';
        }

        return $errores;
    }

    /**
     * Dirección para SUNAT (formato requerido)
     */
    public function getDireccionSunat()
    {
        if ($this->tieneRuc() && !empty($this->direccion)) {
            return strtoupper($this->direccion);
        }
        
        return '-'; // Dirección por defecto para boletas sin RUC
    }
}
