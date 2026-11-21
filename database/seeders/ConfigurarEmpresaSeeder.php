<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empresa;
use App\Models\Usuario;
use App\Models\Serie;
use Illuminate\Support\Facades\Hash;

class ConfigurarEmpresaSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🚀 Iniciando configuración de empresa...');

        // 1. Crear o actualizar empresa
        $empresa = Empresa::updateOrCreate(
            ['ruc' => '20000000001'], // Buscar por RUC
            [
                'nombre' => 'MI NEGOCIO',
                'razon_social' => 'EMPRESA DE PRUEBAS S.A.C.',
                'nombre_comercial' => 'MI NEGOCIO',
                'direccion' => 'AV. PRINCIPAL 123',
                'telefono' => '999888777',
                'email' => 'pruebas@minegocio.com',
                'direccion_fiscal' => 'AV. PRINCIPAL 123',
                'ubigeo' => '150101',
                'departamento' => 'LIMA',
                'provincia' => 'LIMA',
                'distrito' => 'LIMA',
                'urbanizacion' => '-',
                'codigo_pais' => 'PE',
                'web' => 'www.minegocio.com',
                'sol_user' => null,
                'sol_pass' => null,
                'certificado_path' => 'certificado-beta.pem',
                'certificado_password' => null,
                'facturacion_activa' => true,
                'ambiente_sunat' => 'beta',
            ]
        );

        $this->command->info('✅ Empresa configurada para facturación electrónica (Beta)');
        $this->command->info('   RUC: ' . $empresa->ruc);
        $this->command->info('   Razón Social: ' . $empresa->razon_social);

        // 2. Crear usuario administrador
        $this->crearUsuarioAdmin($empresa);

        // 3. Crear series para comprobantes
        $this->crearSeries($empresa);

        $this->command->info('');
        $this->command->info('🎉 Configuración completada exitosamente');
    }

    private function crearUsuarioAdmin($empresa)
    {
        $usuario = Usuario::updateOrCreate(
            ['correo' => 'admin@minegocio.com'],
            [
                'empresa_id' => $empresa->id,
                'nombre' => 'Administrador',
                'contraseña' => Hash::make('password'),
                'rol' => 'admin',
            ]
        );

        $this->command->info('✅ Usuario administrador creado/actualizado');
        $this->command->info('   Email: ' . $usuario->correo);
        $this->command->info('   Contraseña: password');
        $this->command->info('   Rol: ' . $usuario->rol);
    }

    private function crearSeries($empresa)
    {
        $series = [
            ['tipo_comprobante' => '01', 'serie' => 'F001', 'descripcion' => 'Facturas'],
            ['tipo_comprobante' => '03', 'serie' => 'B001', 'descripcion' => 'Boletas'],
            ['tipo_comprobante' => '07', 'serie' => 'FC01', 'descripcion' => 'Notas de Crédito - Facturas'],
            ['tipo_comprobante' => '07', 'serie' => 'BC01', 'descripcion' => 'Notas de Crédito - Boletas'],
        ];

        foreach ($series as $serieData) {
            Serie::updateOrCreate(
                [
                    'empresa_id' => $empresa->id,
                    'tipo_comprobante' => $serieData['tipo_comprobante'],
                    'serie' => $serieData['serie'],
                ],
                [
                    'correlativo_actual' => 0,
                    'activo' => true,
                    'descripcion' => $serieData['descripcion'],
                ]
            );

            $this->command->info('   ✅ Serie creada: ' . $serieData['serie'] . ' - ' . $serieData['descripcion']);
        }
    }
}
