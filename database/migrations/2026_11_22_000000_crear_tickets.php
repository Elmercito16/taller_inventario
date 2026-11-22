<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tickets rápidos: ventas escritas a mano (sin control de inventario).
 * Generan boleta PDF y ticket con el logo y los datos de la empresa.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tickets')) {
            return;
        }

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedInteger('numero');
            $table->string('cliente_documento', 15)->nullable()->index();
            $table->string('cliente_nombre', 150)->nullable();
            $table->json('items');
            $table->decimal('total', 12, 2)->default(0);
            $table->string('estado', 15)->default('emitido'); // emitido | anulado
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->dateTime('fecha');
            $table->timestamps();

            $table->unique(['empresa_id', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
