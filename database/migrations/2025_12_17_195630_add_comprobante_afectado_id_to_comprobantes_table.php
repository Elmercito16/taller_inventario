<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Si la tabla aún no existe, la crea completa la migración de sincronización.
        if (! Schema::hasTable('comprobantes') || Schema::hasColumn('comprobantes', 'comprobante_afectado_id')) {
            return;
        }

        Schema::table('comprobantes', function (Blueprint $table) {
            $table->unsignedBigInteger('comprobante_afectado_id')->nullable()->after('venta_id');

            if (! Schema::hasColumn('comprobantes', 'motivo_nota')) {
                $table->string('motivo_nota', 500)->nullable()->after('comprobante_afectado_id');
            }

            $table->foreign('comprobante_afectado_id')
                  ->references('id')
                  ->on('comprobantes')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        if (! Schema::hasColumn('comprobantes', 'comprobante_afectado_id')) {
            return;
        }

        Schema::table('comprobantes', function (Blueprint $table) {
            $table->dropForeign(['comprobante_afectado_id']);
            $table->dropColumn(['comprobante_afectado_id', 'motivo_nota']);
        });
    }
};
