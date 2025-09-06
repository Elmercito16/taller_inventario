<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedors', function (Blueprint $table) {
        $table->bigIncrements('id'); // BIGINT UNSIGNED
        $table->string('nombre');
        $table->string('contacto')->nullable();
        $table->string('telefono')->nullable();
        $table->string('direccion')->nullable();
        $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
