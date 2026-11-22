<?php

/*
|--------------------------------------------------------------------------
| Opciones del sistema
|--------------------------------------------------------------------------
*/

return [

    /*
     * Los tickets rápidos (ventas escritas a mano, sin inventario) se suman
     * como ingresos en Finanzas. Cuando termines el inventariado y vendas
     * solo desde el Punto de venta, desactívalo con TICKETS_EN_FINANZAS=false
     * en el .env (y ejecuta "php artisan config:clear" si usas caché).
     */
    'tickets_en_finanzas' => (bool) env('TICKETS_EN_FINANZAS', true),

];
