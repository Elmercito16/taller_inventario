<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY

            // Relación con clientes
            $table->foreignId('cliente_id')
                  ->constrained('clientes')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate()
                  ->comment('Cliente que realizó la compra');

            $table->dateTime('fecha')->default(now())->comment('Fecha y hora de la venta');
            $table->decimal('total', 10, 2)->default(0)->comment('Monto total de la venta');
            $table->enum('estado', ['pendiente', 'pagado', 'anulado'])
                  ->default('pendiente')
                  ->comment('Estado actual de la venta');

            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
