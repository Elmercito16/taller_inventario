<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToEmpresa;

class Comprobante extends Model
{
    use HasFactory, BelongsToEmpresa;

    protected $fillable = [
    'empresa_id',
    'venta_id',
    'cliente_id',
    'tipo_comprobante',
    'serie',
    'correlativo',
    'numero_completo',
    
    // Datos del cliente
    'cliente_tipo_doc',
    'cliente_num_doc',
    'cliente_nombre',
    
    // Importes
    'subtotal',
    'igv',
    'total',
    'total_letras',
    'moneda',
    'total_gravadas',
    'total_igv',
    'total_exoneradas',
    'total_inafectas',
    'total_gratuitas',
    'total_descuentos',
    
    // Fechas
    'fecha_emision',
    'fecha_envio_sunat',
    'fecha_vencimiento',
    'hora_emision',
    
    // SUNAT
    'estado_sunat',
    'codigo_sunat',
    'mensaje_sunat',
    'observaciones_sunat',
    'hash',
    'hash_cpe',
    'hash_cdr',
    
    // Archivos
    'xml',
    'xml_path',
    'cdr',
    'cdr_path',
    'pdf_path',
    
    // Notas
    'comprobante_afectado_id',
    'motivo_nota',
    'codigo_motivo_nota',
    
    // Anulación
    'anulado',
    'fecha_anulacion',
    'motivo_anulacion',
    'usuario_anulacion_id',
    
    // Otros
    'ambiente',
    'usuario_emision_id',
    'estado',
    'numero_comprobante',
    'ticket_baja',
];


    protected $casts = [
    'fecha_emision' => 'datetime',
    'fecha_envio_sunat' => 'datetime',
    'fecha_anulacion' => 'datetime',
    'fecha_vencimiento' => 'date',
    
    'total_gravadas' => 'decimal:2',
    'total_igv' => 'decimal:2',
    'total_exoneradas' => 'decimal:2',
    'total_inafectas' => 'decimal:2',
    'total_gratuitas' => 'decimal:2',
    'total_descuentos' => 'decimal:2',
    'subtotal' => 'decimal:2',
    'igv' => 'decimal:2',
    'total' => 'decimal:2',
    
    'anulado' => 'boolean',
];


    // Relaciones

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    public function usuarioEmision()
{
    return $this->belongsTo(Usuario::class, 'usuario_emision_id');
}

public function usuarioAnulacion()
{
    return $this->belongsTo(Usuario::class, 'usuario_anulacion_id');
}


    

    public function serie_modelo()
    {
        return $this->belongsTo(Serie::class, 'serie', 'serie')
                    ->where('empresa_id', $this->empresa_id);
    }

    public function comprobanteAfectado()
    {
        return $this->belongsTo(Comprobante::class, 'comprobante_afectado_id');
    }

    public function notasCredito()
    {
        return $this->hasMany(Comprobante::class, 'comprobante_afectado_id');
    }

    // Accesorios
    public function getTipoComprobanteNombreAttribute()
    {
        return match($this->tipo_comprobante) {
            '01' => 'FACTURA',
            '03' => 'BOLETA DE VENTA',
            '07' => 'NOTA DE CRÉDITO',
            '08' => 'NOTA DE DÉBITO',
            default => 'COMPROBANTE'
        };
    }

   public function getEstadoBadgeAttribute()
{
    return match($this->estado_sunat) {  // ✅ Cambiado de estado a estado_sunat
        'pendiente' => '<span class="badge bg-warning">Pendiente</span>',
        'aceptado' => '<span class="badge bg-success">Aceptado</span>',
        'rechazado' => '<span class="badge bg-danger">Rechazado</span>',
        'anulado' => '<span class="badge bg-secondary">Anulado</span>',
        'baja_pendiente' => '<span class="badge bg-warning">Baja en proceso</span>',
        'observado' => '<span class="badge bg-info">Observado</span>',
        default => '<span class="badge bg-light">Desconocido</span>',
    };
}


    // Scopes
    public function scopeEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeAceptados($query)
    {
        return $query->whereIn('estado_sunat', ['aceptado', 'observado']);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado_sunat', 'pendiente');
    }
    // ==========================================
// MÉTODOS DE NEGOCIO
// ==========================================

/**
 * Verifica si el comprobante puede ser anulado
 */
public function puedeSerAnulado(): bool
{
    if ($this->anulado || $this->ticket_baja) {
        return false;
    }

    // Solo comprobantes aceptados (o con observaciones) por SUNAT
    if (! in_array($this->estado_sunat, ['aceptado', 'observado'])) {
        return false;
    }

    // SUNAT admite la baja dentro del plazo configurado (días calendario)
    $dias = (int) config('sunat.dias_comunicacion_baja', 7);
    if (! $this->fecha_emision || $this->fecha_emision->copy()->startOfDay()->diffInDays(now()->startOfDay()) > $dias) {
        return false;
    }

    // No se puede anular si tiene notas de crédito/débito asociadas
    return ! $this->notasCredito()->exists();
}

/**
 * Verifica si el comprobante puede generar nota de crédito
 */
public function puedeGenerarNotaCredito()
{
    return $this->estado_sunat === 'aceptado' 
        && !$this->anulado
        && in_array($this->tipo_comprobante, ['01', '03']); // Solo facturas y boletas
}

/**
 * Verifica si el comprobante puede generar nota de débito
 */
public function puedeGenerarNotaDebito()
{
    return $this->estado_sunat === 'aceptado' 
        && !$this->anulado
        && $this->tipo_comprobante === '01'; // Solo facturas
}

/**
 * Ruta absoluta de un archivo del comprobante (xml_path, cdr_path, pdf_path)
 * o null si no existe en disco.
 */
public function rutaArchivo(string $campo): ?string
{
    if (empty($this->{$campo})) {
        return null;
    }

    $ruta = storage_path('app/' . ltrim($this->{$campo}, '/'));

    return file_exists($ruta) ? $ruta : null;
}

}
