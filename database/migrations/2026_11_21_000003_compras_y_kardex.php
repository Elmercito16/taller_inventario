<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Módulo de compras y kardex (movimientos de inventario).
 * Compatible con MySQL y PostgreSQL.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('compras')) {
            Schema::create('compras', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('empresa_id')->index();
                $table->unsignedBigInteger('proveedor_id')->nullable()->index();
                $table->unsignedBigInteger('usuario_id')->nullable();
                $table->date('fecha');
                $table->string('tipo_documento', 20)->default('ninguno'); // factura, boleta, guia, ninguno
                $table->string('numero_documento', 30)->nullable();
                $table->decimal('total', 12, 2)->default(0);
                $table->string('estado', 20)->default('registrada'); // registrada, anulada
                $table->text('observaciones')->nullable();
                $table->dateTime('fecha_anulacion')->nullable();
                $table->string('motivo_anulacion')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('detalle_compras')) {
            Schema::create('detalle_compras', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('empresa_id')->index();
                $table->unsignedBigInteger('compra_id')->index();
                $table->unsignedBigInteger('repuesto_id')->index();
                $table->decimal('cantidad', 12, 3);
                $table->decimal('costo_unitario', 12, 4);
                $table->decimal('subtotal', 12, 2);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('movimientos_inventario')) {
            Schema::create('movimientos_inventario', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('empresa_id')->index();
                $table->unsignedBigInteger('repuesto_id');
                $table->string('tipo', 10); // entrada | salida
                $table->string('motivo', 30);
                $table->decimal('cantidad', 12, 3); // siempre positiva
                $table->decimal('stock_anterior', 12, 3);
                $table->decimal('stock_nuevo', 12, 3);
                $table->decimal('costo_unitario', 12, 4)->nullable();
                $table->string('referencia_type', 50)->nullable();
                $table->unsignedBigInteger('referencia_id')->nullable();
                $table->string('descripcion')->nullable();
                $table->unsignedBigInteger('usuario_id')->nullable();
                $table->dateTime('fecha');
                $table->timestamps();

                $table->index(['repuesto_id', 'fecha']);
                $table->index(['referencia_type', 'referencia_id']);
            });

            // Saldo inicial: el stock actual de cada producto se registra como
            // primer movimiento para que el kardex arranque cuadrado.
            $ahora = now();
            DB::table('repuestos')
                ->where(fn ($q) => $q->where('tipo', '<>', 'servicio')->orWhereNull('tipo'))
                ->where('cantidad', '<>', 0)
                ->orderBy('id')
                ->chunk(500, function ($repuestos) use ($ahora) {
                    DB::table('movimientos_inventario')->insert($repuestos->map(fn ($r) => [
                        'empresa_id' => $r->empresa_id,
                        'repuesto_id' => $r->id,
                        'tipo' => $r->cantidad > 0 ? 'entrada' : 'salida',
                        'motivo' => 'saldo_inicial',
                        'cantidad' => abs($r->cantidad),
                        'stock_anterior' => 0,
                        'stock_nuevo' => $r->cantidad,
                        'costo_unitario' => $r->precio_compra ?? null,
                        'descripcion' => 'Saldo inicial al activar el kardex',
                        'fecha' => $ahora,
                        'created_at' => $ahora,
                        'updated_at' => $ahora,
                    ])->all());
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
        Schema::dropIfExists('detalle_compras');
        Schema::dropIfExists('compras');
    }
};
