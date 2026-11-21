<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adapta el inventario a cualquier tipo de negocio:
 * - Productos y servicios (los servicios no controlan stock).
 * - Unidad de medida (catálogo 03 SUNAT) y cantidades con decimales (kg, litros, metros...).
 * - Código de barras.
 * - Afectación del IGV por producto (gravado / exonerado / inafecto).
 * - Marca opcional.
 *
 * Idempotente y compatible con MySQL y PostgreSQL.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repuestos', function (Blueprint $table) {
            if (! Schema::hasColumn('repuestos', 'tipo')) {
                $table->string('tipo', 20)->default('producto');
            }
            if (! Schema::hasColumn('repuestos', 'unidad_medida')) {
                $table->string('unidad_medida', 5)->default('NIU');
            }
            if (! Schema::hasColumn('repuestos', 'codigo_barras')) {
                $table->string('codigo_barras', 50)->nullable()->index();
            }
            if (! Schema::hasColumn('repuestos', 'afectacion_igv')) {
                $table->string('afectacion_igv', 2)->default('10');
            }
        });

        Schema::table('repuestos', function (Blueprint $table) {
            $table->string('marca')->nullable()->change();
            $table->decimal('cantidad', 12, 3)->default(0)->change();
            $table->decimal('minimo_stock', 12, 3)->default(0)->change();
        });

        Schema::table('detalle_ventas', function (Blueprint $table) {
            if (! Schema::hasColumn('detalle_ventas', 'unidad_medida')) {
                $table->string('unidad_medida', 5)->default('NIU');
            }
            if (! Schema::hasColumn('detalle_ventas', 'afectacion_igv')) {
                $table->string('afectacion_igv', 2)->default('10');
            }
        });

        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->decimal('cantidad', 12, 3)->change();
        });
    }

    public function down(): void
    {
        // Solo se eliminan las columnas nuevas; los tipos decimales se conservan
        // para no truncar cantidades fraccionadas ya registradas.
        foreach (['repuestos' => ['tipo', 'unidad_medida', 'codigo_barras', 'afectacion_igv'], 'detalle_ventas' => ['unidad_medida', 'afectacion_igv']] as $tabla => $cols) {
            $existentes = array_values(array_filter($cols, fn ($c) => Schema::hasColumn($tabla, $c)));
            if ($existentes) {
                Schema::table($tabla, function (Blueprint $t) use ($tabla, $existentes) {
                    if ($tabla === 'repuestos' && in_array('codigo_barras', $existentes)) {
                        $t->dropIndex(['codigo_barras']);
                    }
                    $t->dropColumn($existentes);
                });
            }
        }
    }
};
