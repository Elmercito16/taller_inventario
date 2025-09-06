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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->string('dni', 8)->unique()->comment('Documento Nacional de Identidad');
            $table->string('nombre', 100)->comment('Nombre completo del cliente');
            $table->string('telefono', 15)->nullable()->comment('Número de teléfono del cliente');
            $table->string('direccion', 150)->nullable()->comment('Dirección del cliente');
            $table->string('email', 100)->nullable()->unique()->comment('Correo electrónico del cliente');
            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
