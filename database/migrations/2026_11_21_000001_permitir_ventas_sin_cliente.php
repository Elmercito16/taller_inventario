<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Permite registrar ventas a "Cliente general" (sin cliente asociado).
 * El controlador ya lo permitía, pero la columna era NOT NULL en algunas BD.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('ventas', 'cliente_id')) {
            Schema::table('ventas', function (Blueprint $table) {
                $table->unsignedBigInteger('cliente_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // No se revierte: volver a NOT NULL fallaría si ya existen ventas sin cliente.
    }
};
