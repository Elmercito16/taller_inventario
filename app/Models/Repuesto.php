<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use App\Support\Catalogos;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Producto o servicio del inventario.
 * (La tabla conserva el nombre "repuestos" por compatibilidad con los datos existentes.)
 */
class Repuesto extends Model
{
    use HasFactory, BelongsToEmpresa;

    protected $fillable = [
        'empresa_id',
        'codigo',
        'nombre',
        'marca',
        'descripcion',
        'cantidad',
        'minimo_stock',
        'precio_unitario', // Precio de venta (IGV incluido)
        'precio_compra',   // Costo de adquisición
        'tipo',            // producto | servicio
        'unidad_medida',   // Catálogo 03 SUNAT (NIU, KGM, LTR...)
        'codigo_barras',
        'afectacion_igv',  // 10 gravado | 20 exonerado | 30 inafecto
        'proveedor_id',
        'categoria_id',
        'fecha_ingreso',
        'imagen',
    ];

    protected $attributes = [
        'minimo_stock' => 0,
        'tipo' => 'producto',
        'unidad_medida' => 'NIU',
        'afectacion_igv' => '10',
    ];

    protected $casts = [
        'cantidad'        => 'float',
        'minimo_stock'    => 'float',
        'precio_unitario' => 'decimal:2',
        'precio_compra'   => 'decimal:2',
        'fecha_ingreso'   => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Repuesto $model) {
            if (empty($model->codigo)) {
                $model->codigo = static::siguienteCodigo($model->empresa_id);
            }
        });

        // Los servicios no manejan inventario
        static::saving(function (Repuesto $model) {
            if ($model->esServicio()) {
                $model->cantidad = 0;
                $model->minimo_stock = 0;
            }
        });
    }

    /**
     * Genera el siguiente código REP#### de la empresa.
     * Se calcula en PHP para que funcione igual en MySQL y PostgreSQL,
     * y acepta códigos antiguos con o sin guion (REP0007 / REP-0007).
     */
    public static function siguienteCodigo(?int $empresaId = null): string
    {
        $empresaId ??= static::empresaIdActual();

        $max = static::withoutGlobalScope('tenant')
            ->when($empresaId, fn ($q) => $q->where('empresa_id', $empresaId))
            ->where('codigo', 'like', 'REP%')
            ->pluck('codigo')
            ->map(fn ($codigo) => preg_match('/(\d+)\s*$/', $codigo, $m) ? (int) $m[1] : 0)
            ->max() ?? 0;

        return 'REP' . str_pad($max + 1, 4, '0', STR_PAD_LEFT);
    }

    // ==========================================
    // RELACIONES
    // ==========================================

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function detallesVenta()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function detallesCompra()
    {
        return $this->hasMany(DetalleCompra::class);
    }

    /** Kardex del producto. */
    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /** Solo ítems que controlan stock (excluye servicios). */
    public function scopeProductos($query)
    {
        return $query->where(fn ($q) => $q->where('tipo', 'producto')->orWhereNull('tipo'));
    }

    public function scopeServicios($query)
    {
        return $query->where('tipo', 'servicio');
    }

    public function scopeConStockBajo($query)
    {
        return $query->productos()->whereColumn('cantidad', '<=', 'minimo_stock');
    }

    /** Vendibles: productos con stock o servicios. */
    public function scopeDisponibles($query)
    {
        return $query->where(fn ($q) => $q->where('cantidad', '>', 0)->orWhere('tipo', 'servicio'));
    }

    // ==========================================
    // AUXILIARES
    // ==========================================

    public function esServicio(): bool
    {
        return $this->tipo === 'servicio';
    }

    public function controlaStock(): bool
    {
        return ! $this->esServicio();
    }

    public function permiteDecimales(): bool
    {
        return Catalogos::unidadPermiteDecimales($this->unidad_medida);
    }

    public function tieneStockBajo(): bool
    {
        return $this->controlaStock() && $this->cantidad <= $this->minimo_stock;
    }

    public function tieneStock(float $cantidadRequerida = 1): bool
    {
        return $this->esServicio() || $this->cantidad >= $cantidadRequerida;
    }

    public function getUnidadNombreAttribute(): string
    {
        return Catalogos::nombreUnidad($this->unidad_medida);
    }

    public function getUnidadAbreviaturaAttribute(): string
    {
        return Catalogos::abreviaturaUnidad($this->unidad_medida);
    }

    /** Stock legible: "12 und", "2.5 kg" o "Servicio". */
    public function getStockFormateadoAttribute(): string
    {
        return $this->esServicio()
            ? 'Servicio'
            : Catalogos::cantidad($this->cantidad) . ' ' . $this->unidad_abreviatura;
    }

    public function getStockAttribute()
    {
        return $this->cantidad;
    }

    /**
     * El precio de venta YA incluye IGV (es el precio que paga el cliente).
     */
    public function getPrecioVentaConIgvAttribute(): float
    {
        return round((float) $this->precio_unitario, 2);
    }

    /**
     * Valor unitario sin IGV (el que se informa a SUNAT).
     * Solo los ítems gravados incluyen IGV en su precio.
     */
    public function getPrecioVentaSinIgvAttribute(): float
    {
        return $this->afectacion_igv === '10'
            ? round((float) $this->precio_unitario / 1.18, 2)
            : round((float) $this->precio_unitario, 2);
    }

    /**
     * Margen unitario (precio de venta - costo). Null si no hay costo registrado.
     */
    public function getMargenUnitarioAttribute(): ?float
    {
        if ($this->precio_compra === null) {
            return null;
        }

        return round((float) $this->precio_unitario - (float) $this->precio_compra, 2);
    }
}
