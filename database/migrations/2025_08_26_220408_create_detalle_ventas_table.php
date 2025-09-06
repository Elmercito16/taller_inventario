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
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id();

            // Relaciones con ventas y repuestos
            $table->foreignId('venta_id')
                  ->constrained('ventas')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate()
                  ->comment('Referencia a la venta');

            $table->foreignId('repuesto_id')
                  ->constrained('repuestos')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate()
                  ->comment('Repuesto vendido');

            $table->unsignedInteger('cantidad')->default(1)->comment('Cantidad de repuestos vendidos');
            $table->decimal('precio_unitario', 10, 2)->comment('Precio por unidad');
            $table->decimal('subtotal', 10, 2)->comment('Subtotal calculado = cantidad * precio_unitario');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
    }
};
