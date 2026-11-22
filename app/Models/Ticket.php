<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use App\Support\Catalogos;
use App\Support\NumeroALetras;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Ticket rápido: venta escrita a mano, sin control de inventario.
 * items: [{descripcion, cantidad, precio, importe}]
 */
class Ticket extends Model
{
    use BelongsToEmpresa;

    protected $fillable = [
        'empresa_id', 'numero', 'cliente_documento', 'cliente_nombre',
        'items', 'total', 'estado', 'usuario_id', 'fecha',
    ];

    protected $casts = [
        'items' => 'array',
        'total' => 'decimal:2',
        'fecha' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    /** Siguiente correlativo de la empresa (se llama dentro de una transacción). */
    public static function siguienteNumero(int $empresaId): int
    {
        // Se bloquea la última fila (PostgreSQL no admite FOR UPDATE con MAX()).
        $ultimo = DB::table('tickets')
            ->where('empresa_id', $empresaId)
            ->orderByDesc('numero')
            ->lockForUpdate()
            ->value('numero');

        return ((int) $ultimo) + 1;
    }

    public function getNumeroFormateadoAttribute(): string
    {
        return 'T-' . str_pad($this->numero, 6, '0', STR_PAD_LEFT);
    }

    public function getTipoDocumentoAttribute(): ?string
    {
        return match (strlen((string) $this->cliente_documento)) {
            8 => 'DNI',
            11 => 'RUC',
            default => null,
        };
    }

    public function getTotalLetrasAttribute(): string
    {
        return NumeroALetras::monto((float) $this->total);
    }

    public function estaAnulado(): bool
    {
        return $this->estado === 'anulado';
    }

    public function cantidadTexto($valor): string
    {
        return Catalogos::cantidad($valor);
    }
}
