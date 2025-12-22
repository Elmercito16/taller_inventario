<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empresa;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Repuesto;
use App\Models\Categoria;
use App\Models\Proveedor;

class VentaPruebaSeeder extends Seeder
{
    public function run()
    {
        // 👇 CAMBIAR: En lugar de first(), buscar específicamente la empresa ID 1
    $empresa = Empresa::find(1); // Tu empresa
    
    if (!$empresa) {
        $this->command->error('❌ No existe ninguna empresa con ID 1');
        return;
    }

    $this->command->info('📋 Creando datos de prueba para: ' . $empresa->nombre);

        // 1. Crear categorías
        $categorias = $this->crearCategorias($empresa);

        // 2. Crear proveedores
        $proveedores = $this->crearProveedores($empresa);

        // 3. Crear repuestos
        $repuestos = $this->crearRepuestos($empresa, $categorias, $proveedores);

        // 4. Crear clientes
        $clientes = $this->crearClientes($empresa);

        // 5. Crear ventas
        $this->crearVentas($empresa, $clientes, $repuestos);

        $this->command->info('');
        $this->command->info('🎉 ¡Datos de prueba creados exitosamente!');
        $this->command->info('');
        $this->command->info('💡 Ahora puedes:');
        $this->command->info('   1. Iniciar sesión con: admin@mitaller.com / password');
        $this->command->info('   2. Ir a Ventas y ver las ventas de prueba');
        $this->command->info('   3. Emitir comprobantes electrónicos de prueba');
    }

    private function crearCategorias($empresa)
    {
        $categorias = [
            ['nombre' => 'Filtros', 'descripcion' => 'Filtros de aceite, aire y combustible'],
            ['nombre' => 'Frenos', 'descripcion' => 'Pastillas, discos y líquidos de freno'],
            ['nombre' => 'Motor', 'descripcion' => 'Repuestos para motor'],
            ['nombre' => 'Suspensión', 'descripcion' => 'Amortiguadores y componentes'],
            ['nombre' => 'Eléctricos', 'descripcion' => 'Componentes eléctricos'],
        ];

        $categoriasCreadas = [];

        foreach ($categorias as $cat) {
            $categoria = Categoria::firstOrCreate(
                ['empresa_id' => $empresa->id, 'nombre' => $cat['nombre']],
                ['descripcion' => $cat['descripcion']]
            );
            $categoriasCreadas[] = $categoria;
        }

        $this->command->info('✅ ' . count($categoriasCreadas) . ' categorías creadas');

        return $categoriasCreadas;
    }

    private function crearProveedores($empresa)
    {
        $proveedores = [
            ['nombre' => 'REPUESTOS LIMA SAC', 'contacto' => 'Carlos Mendoza', 'telefono' => '999111222'],
            ['nombre' => 'IMPORTADORA NORTE', 'contacto' => 'Ana García', 'telefono' => '999333444'],
            ['nombre' => 'DISTRIBUIDORA CENTRAL', 'contacto' => 'Luis Torres', 'telefono' => '999555666'],
        ];

        $proveedoresCreados = [];

        foreach ($proveedores as $prov) {
            $proveedor = Proveedor::firstOrCreate(
                ['empresa_id' => $empresa->id, 'nombre' => $prov['nombre']],
                [
                    'contacto' => $prov['contacto'],
                    'telefono' => $prov['telefono'],
                    'direccion' => 'AV. INDUSTRIAL 456',
                ]
            );
            $proveedoresCreados[] = $proveedor;
        }

        $this->command->info('✅ ' . count($proveedoresCreados) . ' proveedores creados');

        return $proveedoresCreados;
    }

    private function crearRepuestos($empresa, $categorias, $proveedores)
    {
        $repuestosData = [
            ['codigo' => 'REP-0001', 'nombre' => 'FILTRO DE ACEITE', 'marca' => 'BOSCH', 'precio' => 25.00, 'stock' => 100],
            ['codigo' => 'REP-0002', 'nombre' => 'PASTILLAS DE FRENO', 'marca' => 'BREMBO', 'precio' => 80.00, 'stock' => 50],
            ['codigo' => 'REP-0003', 'nombre' => 'AMORTIGUADOR DELANTERO', 'marca' => 'MONROE', 'precio' => 120.00, 'stock' => 30],
            ['codigo' => 'REP-0004', 'nombre' => 'BUJIAS SET 4 UNID', 'marca' => 'NGK', 'precio' => 45.00, 'stock' => 75],
            ['codigo' => 'REP-0005', 'nombre' => 'ACEITE MOTOR 5W30', 'marca' => 'MOBIL', 'precio' => 35.00, 'stock' => 200],
        ];

        $repuestosCreados = [];

        foreach ($repuestosData as $index => $rep) {
            $repuesto = Repuesto::firstOrCreate(
                ['empresa_id' => $empresa->id, 'codigo' => $rep['codigo']],
                [
                    'nombre' => $rep['nombre'],
                    'marca' => $rep['marca'],
                    'descripcion' => 'Repuesto de alta calidad',
                    'precio_unitario' => $rep['precio'],
                    'cantidad' => $rep['stock'],
                    'minimo_stock' => 10,
                    'categoria_id' => $categorias[$index % count($categorias)]->id,
                    'proveedor_id' => $proveedores[$index % count($proveedores)]->id,
                    'fecha_ingreso' => now(),
                ]
            );
            $repuestosCreados[] = $repuesto;
        }

        $this->command->info('✅ ' . count($repuestosCreados) . ' repuestos creados');

        return $repuestosCreados;
    }

    private function crearClientes($empresa)
    {
        $clientesData = [
            [
                'dni' => '12345678',
                'nombre' => 'JUAN PEREZ GARCIA',
                'telefono' => '999888777',
                'email' => 'juan@example.com',
                'ruc' => null,
            ],
            [
                'dni' => '87654321',
                'nombre' => 'EMPRESA PRUEBA SAC',
                'telefono' => '999777666',
                'email' => 'empresa@example.com',
                'ruc' => '20123456789',
            ],
            [
                'dni' => '11223344',
                'nombre' => 'MARIA LOPEZ DIAZ',
                'telefono' => '999666555',
                'email' => 'maria@example.com',
                'ruc' => null,
            ],
        ];

        $clientesCreados = [];

        foreach ($clientesData as $cli) {
            // Verificar si ya existe
            $existe = Cliente::where('empresa_id', $empresa->id)
                ->where('dni', $cli['dni'])
                ->exists();

            if (!$existe) {
                $cliente = Cliente::create([
                    'empresa_id' => $empresa->id,
                    'nombre' => $cli['nombre'],
                    'dni' => $cli['dni'],
                    'ruc' => $cli['ruc'],
                    'telefono' => $cli['telefono'],
                    'email' => $cli['email'],
                    'direccion' => 'AV. TEST 123',
                ]);
                $clientesCreados[] = $cliente;
            } else {
                $clientesCreados[] = Cliente::where('empresa_id', $empresa->id)
                    ->where('dni', $cli['dni'])
                    ->first();
            }
        }

        $this->command->info('✅ ' . count($clientesCreados) . ' clientes creados');

        return $clientesCreados;
    }

    private function crearVentas($empresa, $clientes, $repuestos)
    {
        // 👇 CONVERTIR ARRAY A COLECCIÓN
        $repuestos = collect($repuestos);
        
        $ventasCreadas = 0;

        foreach ($clientes as $cliente) {
            // Crear 1-2 ventas por cliente
            $numVentas = rand(1, 2);

            for ($i = 0; $i < $numVentas; $i++) {
                // Seleccionar 2-3 repuestos aleatorios
                $cantidadRepuestos = min(rand(2, 3), $repuestos->count());
                $repuestosVenta = $repuestos->random($cantidadRepuestos);

                $total = 0;
                $detalles = [];

                foreach ($repuestosVenta as $repuesto) {
                    $cantidad = rand(1, 3);
                    $subtotal = $cantidad * $repuesto->precio_unitario;
                    $total += $subtotal;

                    $detalles[] = [
                        'repuesto_id' => $repuesto->id,
                        'cantidad' => $cantidad,
                        'precio_unitario' => $repuesto->precio_unitario,
                        'subtotal' => $subtotal,
                    ];
                }

                // Crear venta
                $venta = Venta::create([
                    'empresa_id' => $empresa->id,
                    'cliente_id' => $cliente->id,
                    'usuario_id' => 1, // Usuario admin
                    'fecha' => now()->subDays(rand(0, 30)),
                    'total' => $total,
                    'estado' => 'pagado',
                ]);

                // Crear detalles
                foreach ($detalles as $detalle) {
                    DetalleVenta::create([
                        'empresa_id' => $empresa->id,
                        'venta_id' => $venta->id,
                        'repuesto_id' => $detalle['repuesto_id'],
                        'cantidad' => $detalle['cantidad'],
                        'precio_unitario' => $detalle['precio_unitario'],
                        'subtotal' => $detalle['subtotal'],
                    ]);
                }

                $ventasCreadas++;
            }
        }

        $this->command->info('✅ ' . $ventasCreadas . ' ventas creadas');
    }
}
