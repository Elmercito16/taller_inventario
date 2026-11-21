<?php

namespace App\Services;

use App\Models\MovimientoInventario;
use App\Models\Repuesto;
use App\Support\Catalogos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * ÚNICO punto de entrada para modificar el stock.
 *
 * Cada cambio bloquea la fila del producto, actualiza la cantidad y registra
 * el movimiento en el kardex con el saldo anterior y el nuevo. Así el kardex
 * siempre cuadra con el stock real.
 *
 * Los servicios (tipo = servicio) no controlan stock: se ignoran.
 */
class InventarioService
{
    public function entrada(Repuesto $repuesto, float $cantidad, string $motivo, array $opciones = []): ?MovimientoInventario
    {
        return $this->mover($repuesto, abs($cantidad), $motivo, $opciones);
    }

    public function salida(Repuesto $repuesto, float $cantidad, string $motivo, array $opciones = []): ?MovimientoInventario
    {
        return $this->mover($repuesto, -abs($cantidad), $motivo, $opciones);
    }

    /**
     * Fija el stock en un valor exacto (conteo físico / edición) registrando la diferencia.
     */
    public function fijarStock(Repuesto $repuesto, float $nuevoStock, string $motivo, array $opciones = []): ?MovimientoInventario
    {
        return DB::transaction(function () use ($repuesto, $nuevoStock, $motivo, $opciones) {
            $actual = $this->bloquear($repuesto);
            $diferencia = round($nuevoStock - (float) $actual->cantidad, 3);

            if ($diferencia == 0.0) {
                return null;
            }

            return $this->mover($actual, $diferencia, $motivo, $opciones);
        });
    }

    /**
     * @param array{referencia?: Model, costo?: float|null, descripcion?: string|null, permitir_negativo?: bool, fecha?: \DateTimeInterface} $opciones
     */
    protected function mover(Repuesto $repuesto, float $cantidadConSigno, string $motivo, array $opciones): ?MovimientoInventario
    {
        if ($repuesto->esServicio() || $cantidadConSigno == 0.0) {
            return null;
        }

        return DB::transaction(function () use ($repuesto, $cantidadConSigno, $motivo, $opciones) {
            $actual = $this->bloquear($repuesto);

            $anterior = round((float) $actual->cantidad, 3);
            $nuevo = round($anterior + $cantidadConSigno, 3);

            if ($nuevo < 0 && empty($opciones['permitir_negativo'])) {
                throw new \RuntimeException(sprintf(
                    'Stock insuficiente de %s: disponible %s %s, se requiere %s.',
                    $actual->nombre,
                    Catalogos::cantidad($anterior),
                    $actual->unidad_abreviatura,
                    Catalogos::cantidad(abs($cantidadConSigno))
                ));
            }

            // Actualización directa (sin eventos del modelo) del stock
            Repuesto::withoutGlobalScope('tenant')->whereKey($actual->id)->update(['cantidad' => $nuevo]);
            $repuesto->cantidad = $nuevo;

            $referencia = $opciones['referencia'] ?? null;

            return MovimientoInventario::create([
                'empresa_id' => $actual->empresa_id,
                'repuesto_id' => $actual->id,
                'tipo' => $cantidadConSigno > 0 ? 'entrada' : 'salida',
                'motivo' => $motivo,
                'cantidad' => abs($cantidadConSigno),
                'stock_anterior' => $anterior,
                'stock_nuevo' => $nuevo,
                'costo_unitario' => array_key_exists('costo', $opciones) ? $opciones['costo'] : $actual->precio_compra,
                'referencia_type' => $referencia ? get_class($referencia) : null,
                'referencia_id' => $referencia?->getKey(),
                'descripcion' => $opciones['descripcion'] ?? null,
                'usuario_id' => auth()->id(),
                'fecha' => $opciones['fecha'] ?? now(),
            ]);
        });
    }

    /** Relee el producto con bloqueo de fila (evita carreras entre ventas/compras simultáneas). */
    protected function bloquear(Repuesto $repuesto): Repuesto
    {
        return Repuesto::withoutGlobalScope('tenant')->whereKey($repuesto->getKey())->lockForUpdate()->firstOrFail();
    }
}
