<?php

use App\Support\UserTenantFinder;

return [
    /*
     * El modelo que se usará para el "tenant" (tu cliente).
     */
    'tenant_model' => \App\Models\Empresa::class,

    /*
     * Esta clase es la que "encuentra" al tenant actual.
     */
    'tenant_finder' => UserTenantFinder::class,

    /*
     * Tareas que se ejecutarán cuando un tenant se active.
     */
    'switch_tenant_tasks' => [
        // Puedes agregar tareas aquí si necesitas
    ],

    /*
     * Tareas que se ejecutarán cuando un tenant se olvide.
     */
    'forget_tenant_tasks' => [
        // Puedes agregar tareas aquí si necesitas
    ],

    /*
     * Modelos que NO deben ser filtrados por tenant (modelos globales).
     */
    'landlord_models' => [
        \Spatie\Multitenancy\Models\Tenant::class,
        \App\Models\Empresa::class,
        \App\Models\Usuario::class, // IMPORTANTE
    ],

    /*
     * Por defecto, todos los modelos son "específicos del tenant".
     */
    'models_are_tenant_specific_by_default' => true,

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