<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Empresa;
use App\Models\Usuario; // Tu modelo de usuario corregido
use Illuminate\Support\Facades\Hash;

class CreateSaasClient extends Command
{
    /**
     * El nombre y la firma del comando en la consola.
     *
     * @var string
     */
    protected $signature = 'saas:nuevo-cliente';

    /**
     * La descripción del comando.
     *
     * @var string
     */
    protected $description = 'Crea una nueva Empresa y su Usuario Administrador automáticamente';

    /**
     * Ejecutar el comando.
     */
    public function handle()
    {
        $this->info('🚀 Iniciando proceso de alta de nuevo cliente...');
        $this->newLine();

        // --- 1. Pedir datos de la Empresa ---
        $this->info('🏢 Datos de la Empresa');
        $nombreEmpresa = $this->ask('Nombre de la Empresa (Ej: Taller Juan)');
        $ruc = $this->ask('RUC (Opcional)');
        $direccion = $this->ask('Dirección (Opcional)');
        $telefono = $this->ask('Teléfono de la Empresa (Opcional)');
        $emailEmpresa = $this->ask('Email de contacto de la Empresa (Opcional)');
        
        $this->newLine();

        // --- 2. Pedir datos del Usuario Admin ---
        $this->info('👤 Datos del Usuario Administrador');
        $nombreUser = $this->ask('Nombre del Encargado');
        $emailUser = $this->ask('Correo electrónico para Login');
        $password = $this->secret('Contraseña (no se verá al escribir)');

        // Confirmación final antes de guardar
        $this->newLine();
        if (!$this->confirm("¿Estás seguro de crear la empresa '$nombreEmpresa' con el usuario '$emailUser'?")) {
            $this->info('Operación cancelada.');
            return;
        }

        $this->info('⏳ Creando registros en la base de datos...');

        try {
            // 3. Crear la Empresa
            $empresa = Empresa::create([
                'nombre' => $nombreEmpresa,
                'ruc' => $ruc,
                'direccion' => $direccion,
                'telefono' => $telefono,
                'email' => $emailEmpresa,
            ]);

            // 4. Crear el Usuario vinculado
            // (Aquí tomamos el ID generado de la empresa y se lo ponemos al usuario)
            $usuario = Usuario::create([
                'nombre' => $nombreUser,
                'correo' => $emailUser,
                'contraseña' => Hash::make($password),
                'rol' => 'admin', // Siempre admin
                'empresa_id' => $empresa->id, // ¡AQUÍ SE HACE LA ASOCIACIÓN AUTOMÁTICA!
            ]);

            $this->newLine();
            $this->info('✅ ¡Cliente creado con éxito!');
            
            // Mostramos la tabla resumen con los datos clave
            $this->table(
                ['ID', 'Empresa', 'RUC', 'Usuario Admin', 'Login Correo'],
                [[
                    $empresa->id,
                    $empresa->nombre, 
                    $empresa->ruc ?? 'N/A',
                    $usuario->nombre, 
                    $usuario->correo
                ]]
            );
            
            $this->info('Ahora puedes entregar estas credenciales al cliente.');

        } catch (\Exception $e) {
            $this->error('❌ Ocurrió un error al crear el cliente:');
            $this->error($e->getMessage());
        }
    }
}