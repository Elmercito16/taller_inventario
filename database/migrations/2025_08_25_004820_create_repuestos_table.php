<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('repuestos', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('imagen')->nullable();
        $table->string('codigo')->unique();
        $table->string('nombre');
        $table->string('marca')->nullable();
        $table->text('descripcion')->nullable();
        $table->integer('cantidad');
        $table->integer('minimo_stock');
        $table->decimal('precio_unitario', 10, 2);
        


        // Claves foráneas
        $table->unsignedBigInteger('proveedor_id')->nullable();
        $table->unsignedBigInteger('categoria_id')->nullable();
        
        $table->date('fecha_ingreso')->nullable();
        $table->timestamps();

        $table->foreign('proveedor_id')
              ->references('id')
              ->on('proveedors')
              ->onDelete('set null');

        $table->foreign('categoria_id')
              ->references('id')
              ->on('categorias')
              ->onDelete('set null');
    });
}

    public function down(): void
    {
        Schema::dropIfExists('repuestos');
    }
};
