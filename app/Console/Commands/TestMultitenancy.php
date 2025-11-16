<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Empresa;
use App\Models\Repuesto;

class TestMultitenancy extends Command
{
    protected $signature = 'test:multitenancy';
    protected $description = 'Prueba la configuración de multitenancy';

    public function handle()
    {
        $this->info('=== Probando Multitenancy ===');
        $this->newLine();

        // 1. Verificar usuarios
        $userCount = Usuario::count();
        $this->info("✅ Total de usuarios: {$userCount}");

        // 2. Obtener primer usuario
        $user = Usuario::first();
        $this->info("✅ Usuario: {$user->nombre} ({$user->correo})");
        $this->info("   Empresa ID: {$user->empresa_id}");
        $this->newLine();

        // 3. Obtener tenant
        $tenant = $user->getTenant();
        $this->info("✅ Tenant obtenido: {$tenant->nombre} (ID: {$tenant->id})");
        $this->newLine();

        // 4. Activar tenant
        $tenant->makeCurrent();
        $currentTenant = \Spatie\Multitenancy\Models\Tenant::current();
        
        if ($currentTenant) {
            $this->info("✅ Tenant actual establecido: {$currentTenant->nombre}");
        } else {
            $this->error("❌ No se pudo establecer el tenant actual");
            return;
        }
        $this->newLine();

        // 5. Probar scope automático
        $this->info("=== Probando Scope Automático ===");
        
        // Contar todos los repuestos (debería estar filtrado por empresa)
        $repuestosCount = Repuesto::count();
        $this->info("Repuestos visibles con scope: {$repuestosCount}");
        
        // Contar sin scope (todos los repuestos de todas las empresas)
        $repuestosTotales = Repuesto::withoutGlobalScope('tenant')->count();
        $this->info("Total de repuestos (sin scope): {$repuestosTotales}");
        $this->newLine();

        if ($repuestosCount < $repuestosTotales) {
            $this->info("✅ El scope automático está funcionando correctamente");
        } else {
            $this->warn("⚠️  El scope no está filtrando (puede que todas las empresas compartan los mismos datos)");
        }
        $this->newLine();

        // 6. Verificar que solo vemos datos de nuestra empresa
        if ($repuestosCount > 0) {
            $repuesto = Repuesto::first();
            $this->info("Ejemplo de repuesto:");
            $this->info("  - Nombre: {$repuesto->nombre}");
            $this->info("  - Empresa ID: {$repuesto->empresa_id}");
            
            if ($repuesto->empresa_id == $tenant->id) {
                $this->info("✅ El repuesto pertenece al tenant correcto");
            } else {
                $this->error("❌ ERROR: El repuesto NO pertenece al tenant actual");
            }
        }
        
        $this->newLine();
        $this->info('=== Prueba completada ===');
    }
}