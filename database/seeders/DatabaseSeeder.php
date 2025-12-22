<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // ConfigurarEmpresaSeeder::class, // 👈 COMENTAR esto (ya tienes tu empresa)
            VentaPruebaSeeder::class, // 👈 Solo ejecutar el de datos de prueba
        ]);
    }
}
