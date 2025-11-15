<?php

use App\Multitenancy\UserTenantFinder; // <-- CAMBIO CLAVE
use Spatie\Multitenancy\Actions\MakeTenantCurrentAction;
use Spatie\Multitenancy\Actions\ForgetCurrentTenantAction;
use Spatie\Multitenancy\Actions\MigrateTenantAction;

return [
    /*
     * Este modelo de tenant será usado por el sistema.
     */
    'tenant_model' => \App\Models\Empresa::class,

    /*
     * Esta clase será la encargada de determinar el tenant actual.
     */
    'tenant_finder' => \App\Support\UserTenantFinder::class,

    /*
     * Estos modelos tendrán scope automático por tenant.
     */
    'tenant_aware_models' => [
        \App\Models\Repuesto::class,
        \App\Models\Venta::class,
        \App\Models\Cliente::class,
        \App\Models\Categoria::class,
        \App\Models\Proveedor::class,
        \App\Models\DetalleVenta::class,
    ],

    /*
     * Acciones a ejecutar cuando se hace el tenant actual.
     */
    'switch_tenant_tasks' => [
        // Puedes agregar tareas personalizadas aquí
    ],

    /*
     * Rutas que no requieren tenant.
     */
    'exempt_routes' => [
        'login',
        'register',
        'logout',
        'password.*',
    ],
];