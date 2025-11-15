<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Esta tabla guardará a tus clientes (Taller A, Taller B, etc.)
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // El nombre del taller
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};