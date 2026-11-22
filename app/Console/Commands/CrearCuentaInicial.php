<?php

namespace App\Console\Commands;

use App\Models\Empresa;
use App\Models\Usuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Crea la primera empresa y su administrador en una base de datos vacía,
 * leyendo variables de entorno (útil en Render, donde el plan gratis no tiene consola):
 *
 *   INICIAL_EMPRESA, INICIAL_ADMIN_NOMBRE, INICIAL_ADMIN_CORREO, INICIAL_ADMIN_CLAVE
 *
 * No hace nada si ya existe algún usuario o si faltan variables.
 * Después del primer arranque, elimina INICIAL_ADMIN_CLAVE de Render.
 */
class CrearCuentaInicial extends Command
{
    protected $signature = 'sistema:cuenta-inicial';

    protected $description = 'Crea la empresa y el administrador iniciales desde variables de entorno (solo si no hay usuarios)';

    public function handle(): int
    {
        $empresa = env('INICIAL_EMPRESA');
        $nombre = env('INICIAL_ADMIN_NOMBRE');
        $correo = env('INICIAL_ADMIN_CORREO');
        $clave = env('INICIAL_ADMIN_CLAVE');

        if (! $empresa || ! $nombre || ! $correo || ! $clave) {
            $this->line('Cuenta inicial: variables INICIAL_* no definidas, se omite.');

            return self::SUCCESS;
        }

        if (Usuario::query()->exists()) {
            $this->line('Cuenta inicial: ya existen usuarios, se omite.');

            return self::SUCCESS;
        }

        if (strlen($clave) < 8) {
            $this->error('Cuenta inicial: INICIAL_ADMIN_CLAVE debe tener al menos 8 caracteres.');

            return self::FAILURE;
        }

        DB::transaction(function () use ($empresa, $nombre, $correo, $clave) {
            $e = Empresa::create([
                'nombre' => $empresa,
                'nombre_comercial' => mb_strtoupper($empresa),
                'ambiente_sunat' => 'beta',
                'facturacion_activa' => false,
            ]);

            Usuario::create([
                'nombre' => $nombre,
                'correo' => mb_strtolower(trim($correo)),
                'contraseña' => Hash::make($clave),
                'rol' => 'admin',
                'empresa_id' => $e->id,
            ]);
        });

        $this->info("Cuenta inicial creada: {$empresa} / {$correo}");

        return self::SUCCESS;
    }
}
