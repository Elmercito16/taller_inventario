<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    use HasFactory;

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
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

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
        return $query->where('estado', 'aceptado');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }
    // ==========================================
// MÉTODOS DE NEGOCIO
// ==========================================

/**
 * Verifica si el comprobante puede ser anulado
 */
public function puedeSerAnulado()
{
    // No se puede anular si ya está anulado
    if ($this->anulado) {
        return false;
    }

    // No se puede anular si no fue aceptado por SUNAT
    if ($this->estado_sunat !== 'aceptado') {
        return false;
    }

    // Solo se pueden anular comprobantes del mismo día
    if (!$this->fecha_emision->isToday()) {
        return false;
    }

    // No se puede anular si tiene notas de crédito/débito asociadas
    if ($this->notasCredito()->exists()) {
        return false;
    }

    return true;
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
 * Descarga el XML
 */
public function descargarXml()
{
    if (!$this->xml_path || !\Storage::exists($this->xml_path)) {
        return null;
    }

    return response()->download(
        storage_path('app/' . $this->xml_path),
        $this->numero_completo . '.xml'
    );
}

/**
 * Descarga el PDF
 */
public function descargarPdf()
{
    if (!$this->pdf_path || !\Storage::exists($this->pdf_path)) {
        return null;
    }

    return response()->download(
        storage_path('app/' . $this->pdf_path),
        $this->numero_completo . '.pdf'
    );
}

/**
 * Descarga el CDR (Constancia de Recepción)
 */
public function descargarCdr()
{
    if (!$this->cdr_path || !\Storage::exists($this->cdr_path)) {
        return null;
    }

    return response()->download(
        storage_path('app/' . $this->cdr_path),
        'R-' . $this->numero_completo . '.zip'
    );
}

}
