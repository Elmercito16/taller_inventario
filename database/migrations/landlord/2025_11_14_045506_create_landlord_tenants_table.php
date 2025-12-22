<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('empresas', function (Blueprint $table) {
            // Verificar y añadir solo las columnas que no existen
            
            // RUC ya existe, no lo añadimos
            // $table->string('ruc', 11)->nullable()->after('nombre');
            
            // Datos fiscales SUNAT (nuevos)
            if (!Schema::hasColumn('empresas', 'razon_social')) {
                $table->string('razon_social')->nullable()->after('ruc');
            }
            
            if (!Schema::hasColumn('empresas', 'nombre_comercial')) {
                $table->string('nombre_comercial')->nullable()->after('razon_social');
            }
            
            // Dirección fiscal
            if (!Schema::hasColumn('empresas', 'direccion_fiscal')) {
                $table->string('direccion_fiscal')->nullable()->after('nombre_comercial');
            }
            
            if (!Schema::hasColumn('empresas', 'ubigeo')) {
                $table->string('ubigeo', 6)->nullable()->after('direccion_fiscal')
                      ->comment('Código de ubigeo INEI');
            }
            
            if (!Schema::hasColumn('empresas', 'departamento')) {
                $table->string('departamento', 100)->nullable()->after('ubigeo');
            }
            
            if (!Schema::hasColumn('empresas', 'provincia')) {
                $table->string('provincia', 100)->nullable()->after('departamento');
            }
            
            if (!Schema::hasColumn('empresas', 'distrito')) {
                $table->string('distrito', 100)->nullable()->after('provincia');
            }
            
            if (!Schema::hasColumn('empresas', 'urbanizacion')) {
                $table->string('urbanizacion', 100)->nullable()->after('distrito');
            }
            
            if (!Schema::hasColumn('empresas', 'codigo_pais')) {
                $table->string('codigo_pais', 2)->default('PE')->after('urbanizacion');
            }
            
            // Contacto (telefono y email ya existen, los saltamos)
            
            if (!Schema::hasColumn('empresas', 'web')) {
                $table->string('web')->nullable()->after('email');
            }
            
            // Logo
            if (!Schema::hasColumn('empresas', 'logo')) {
                $table->string('logo')->nullable()->after('web');
            }
            
            // Credenciales SUNAT (Producción)
            if (!Schema::hasColumn('empresas', 'sol_user')) {
                $table->string('sol_user')->nullable()->after('logo')
                      ->comment('Usuario SOL SUNAT');
            }
            
            if (!Schema::hasColumn('empresas', 'sol_pass')) {
                $table->string('sol_pass')->nullable()->after('sol_user')
                      ->comment('Contraseña SOL SUNAT');
            }
            
            // Certificado digital
            if (!Schema::hasColumn('empresas', 'certificado_path')) {
                $table->string('certificado_path')->nullable()->after('sol_pass');
            }
            
            if (!Schema::hasColumn('empresas', 'certificado_password')) {
                $table->string('certificado_password')->nullable()->after('certificado_path');
            }
            
            // Estado de facturación
            if (!Schema::hasColumn('empresas', 'facturacion_activa')) {
                $table->boolean('facturacion_activa')->default(false)
                      ->after('certificado_password');
            }
            
            if (!Schema::hasColumn('empresas', 'ambiente_sunat')) {
                $table->enum('ambiente_sunat', ['beta', 'produccion'])
                      ->default('beta')
                      ->after('facturacion_activa');
            }
            
            // Índice en RUC ya existe según tu schema
        });
    }

    public function down()
    {
        Schema::table('empresas', function (Blueprint $table) {
            // Eliminar solo las columnas que añadimos
            $columnasAEliminar = [
                'razon_social', 'nombre_comercial',
                'direccion_fiscal', 'ubigeo', 'departamento', 'provincia', 
                'distrito', 'urbanizacion', 'codigo_pais',
                'web', 'logo',
                'sol_user', 'sol_pass',
                'certificado_path', 'certificado_password',
                'facturacion_activa', 'ambiente_sunat'
            ];
            
            foreach ($columnasAEliminar as $columna) {
                if (Schema::hasColumn('empresas', $columna)) {
                    $table->dropColumn($columna);
                }
            }
        });
    }
};
