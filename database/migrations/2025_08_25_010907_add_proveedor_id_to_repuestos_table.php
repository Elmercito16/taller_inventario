<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('repuestos', function (Blueprint $table) {
        if (!Schema::hasColumn('repuestos', 'proveedor_id')) {
            $table->unsignedBigInteger('proveedor_id')->nullable();
            $table->foreign('proveedor_id')->references('id')->on('proveedors')->onDelete('set null');
        }
    });
}

public function down()
{
    Schema::table('repuestos', function (Blueprint $table) {
        $table->dropForeign(['proveedor_id']);
        $table->dropColumn('proveedor_id');
    });
}
};
