<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingresos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->decimal('monto', 10, 2);
            $table->string('descripcion');
            $table->date('fecha');
            $table->string('tipo')->nullable();
            $table->timestamps();

            $table->index(['empresa_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingresos');
    }
};
