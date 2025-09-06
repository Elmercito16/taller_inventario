<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            // Actualizamos el campo "rol" para aceptar usuario también
            $table->enum('rol', ['admin','usuario'])
                  ->default('usuario')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            // Revertimos al estado anterior (solo admin y encargado)
            $table->enum('rol', ['admin', 'encargado'])
                  ->default('encargado')
                  ->change();
        });
    }
};
