<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Ambiente SUNAT
    |--------------------------------------------------------------------------
    | false = Beta (Pruebas)
    | true = Producción (Real)
    */
    'production' => env('SUNAT_PRODUCTION', false),
    
    /*
    |--------------------------------------------------------------------------
    | URLs de SUNAT
    |--------------------------------------------------------------------------
    */
    'urls' => [
        'beta' => 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService',
        'production' => 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService',
        'guia_beta' => 'https://e-beta.sunat.gob.pe/ol-ti-itemision-guia-gem-beta/billService',
        'guia_production' => 'https://e-guiaremision.sunat.gob.pe/ol-ti-itemision-guia-gem/billService',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Credenciales Beta (Pruebas)
    |--------------------------------------------------------------------------
    */
    'beta' => [
        'ruc' => '20000000001',
        'sol_user' => 'MODDATOS',
        'sol_pass' => 'moddatos',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Rutas de Almacenamiento
    |--------------------------------------------------------------------------
    */
    'paths' => [
        'certificados' => storage_path('app/sunat/certificados'),
        'xml' => storage_path('app/sunat/xml'),
        'cdr' => storage_path('app/sunat/cdr'),
        'pdf' => storage_path('app/sunat/pdf'),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Configuración de Comprobantes
    |--------------------------------------------------------------------------
    */
    'series' => [
        'factura' => 'F001',
        'boleta' => 'B001',
        'nota_credito_factura' => 'FC01',
        'nota_credito_boleta' => 'BC01',
        'nota_debito_factura' => 'FD01',
        'nota_debito_boleta' => 'BD01',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | IGV (Impuesto General a las Ventas)
    |--------------------------------------------------------------------------
    */
    'igv' => 18, // 18%
    
];
