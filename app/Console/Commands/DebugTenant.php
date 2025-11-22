<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use Spatie\Multitenancy\Models\Tenant;

class DebugTenant extends Command
{
    protected $signature = 'debug:tenant {email}';
    protected $description = 'Debug del tenant para un usuario específico';

    public function handle()
    {
        $email = $this->argument('email');
        
        $user = Usuario::where('correo', $email)->first();
        
        if (!$user) {
            $this->error("❌ Usuario no encontrado: {$email}");
            return;
        }

        $this->info("=== DEBUG TENANT ===");
        $this->info("Usuario: {$user->nombre}");
        $this->info("Email: {$user->correo}");
        $this->info("Empresa ID en BD: {$user->empresa_id}");
        $this->newLine();

        // Obtener tenant
        $tenant = $user->getTenant();
        $this->info("✅ Tenant del usuario: {$tenant?->nombre} (ID: {$tenant?->id})");
        
        // Activar
        if ($tenant) {
            $tenant->makeCurrent();
            $this->newLine();
            
            $current = Tenant::current();
            if ($current) {
                $this->info("✅ Tenant actual después de makeCurrent(): {$current->nombre} (ID: {$current->id})");
            } else {
                $this->error("❌ No hay tenant actual después de makeCurrent()");
            }
        }

        // Contar datos
        $this->newLine();
        $this->info("=== CONTEOS ===");
        $this->info("Repuestos visibles: " . \App\Models\Repuesto::count());
        $this->info("Clientes visibles: " . \App\Models\Cliente::count());
        $this->info("Categorías visibles: " . \App\Models\Categoria::count());
        $this->info("Proveedores visibles: " . \App\Models\Proveedor::count());
        $this->info("Ventas visibles: " . \App\Models\Venta::count());
        
        // Sin scope
        $this->newLine();
        $this->info("=== SIN SCOPE (TODOS) ===");
        $this->info("Repuestos totales: " . \App\Models\Repuesto::withoutGlobalScope('tenant')->count());
        $this->info("Clientes totales: " . \App\Models\Cliente::withoutGlobalScope('tenant')->count());
    }
}