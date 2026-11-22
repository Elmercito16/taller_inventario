<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\Empresa;
use App\Models\Gasto;
use App\Models\Repuesto;
use App\Models\Usuario;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Multitenancy\Models\Tenant;
use Tests\TestCase;

class SistemaTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empresaA;
    private Empresa $empresaB;
    private Usuario $adminA;
    private Usuario $vendedorA;
    private Usuario $adminB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->empresaA = Empresa::create(['nombre' => 'Taller A']);
        $this->empresaB = Empresa::create(['nombre' => 'Taller B']);

        $this->adminA = $this->crearUsuario($this->empresaA, 'admin', 'a@test.com');
        $this->vendedorA = $this->crearUsuario($this->empresaA, 'usuario', 'v@test.com');
        $this->adminB = $this->crearUsuario($this->empresaB, 'admin', 'b@test.com');
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    private function crearUsuario(Empresa $empresa, string $rol, string $correo): Usuario
    {
        return Usuario::create([
            'nombre' => ucfirst($rol) . ' ' . $empresa->nombre,
            'correo' => $correo,
            'contraseña' => Hash::make('secreto123'),
            'rol' => $rol,
            'empresa_id' => $empresa->id,
        ]);
    }

    /** Crea datos "como" la empresa indicada. */
    private function comoEmpresa(Empresa $empresa, callable $callback)
    {
        $empresa->makeCurrent();
        $resultado = $callback();
        Tenant::forgetCurrent();

        return $resultado;
    }

    private function datosBase(Empresa $empresa): array
    {
        return $this->comoEmpresa($empresa, function () {
            $categoria = Categoria::create(['nombre' => 'Frenos']);
            $repuesto = Repuesto::create([
                'nombre' => 'Pastilla de freno', 'marca' => 'Bosch', 'cantidad' => 10,
                'precio_unitario' => 59.00, 'precio_compra' => 40.00, 'categoria_id' => $categoria->id,
            ]);
            $cliente = Cliente::create(['nombre' => 'Juan Pérez', 'dni' => '12345678', 'telefono' => '987654321']);

            return compact('categoria', 'repuesto', 'cliente');
        });
    }

    public function test_paginas_principales_cargan(): void
    {
        $this->datosBase($this->empresaA);

        $rutas = [
            'dashboard', 'repuestos.index', 'repuestos.create', 'categorias.index', 'proveedores.index',
            'clientes.index', 'ventas.index', 'ventas.create', 'comprobantes.index', 'finanzas.index',
            'reportes.index', 'empresa.index', 'usuarios.index',
        ];

        foreach ($rutas as $ruta) {
            $this->actingAs($this->adminA)->get(route($ruta))->assertOk();
        }
    }

    public function test_rutas_de_depuracion_y_registro_no_existen(): void
    {
        $this->get('/debug-log')->assertNotFound();
        $this->get('/register')->assertNotFound();
    }

    public function test_no_se_puede_ver_ni_editar_datos_de_otra_empresa(): void
    {
        $datosB = $this->datosBase($this->empresaB);

        $this->actingAs($this->adminA)->get(route('categorias.edit', $datosB['categoria']))->assertNotFound();
        $this->actingAs($this->adminA)->delete(route('clientes.destroy', $datosB['cliente']))->assertNotFound();
        $this->actingAs($this->adminA)->get(route('repuestos.edit', $datosB['repuesto']->id))->assertNotFound();

        $this->assertDatabaseHas('clientes', ['id' => $datosB['cliente']->id]);

        // Comprobante de la empresa B: la descarga del XML está prohibida para A
        $comprobante = $this->comoEmpresa($this->empresaB, fn () => Comprobante::create([
            'tipo_comprobante' => '03', 'serie' => 'B001', 'correlativo' => 1,
            'numero_completo' => 'B001-00000001', 'estado_sunat' => 'aceptado', 'xml_path' => 'sunat/xml/x.xml',
        ]));
        $this->actingAs($this->adminA)->get(route('comprobantes.descargar-xml', $comprobante))->assertNotFound();
    }

    public function test_listados_solo_muestran_datos_de_la_empresa(): void
    {
        $this->datosBase($this->empresaB);

        $this->actingAs($this->adminA)->get(route('clientes.search', ['q' => 'Juan']))
            ->assertOk()
            ->assertJsonCount(0);
    }

    public function test_nombre_de_categoria_y_dni_son_unicos_por_empresa(): void
    {
        $this->datosBase($this->empresaB);

        $this->actingAs($this->adminA)->post(route('categorias.store'), ['nombre' => 'Frenos'])
            ->assertSessionHasNoErrors();
        $this->actingAs($this->adminA)->post(route('clientes.store'), [
            'nombre' => 'Otro Juan', 'dni' => '12345678', 'telefono' => '999888777',
        ])->assertSessionHasNoErrors();

        $this->assertSame(1, Categoria::withoutGlobalScope('tenant')->where('empresa_id', $this->empresaA->id)->count());
    }

    public function test_codigo_de_repuesto_se_genera_y_es_portable(): void
    {
        $this->comoEmpresa($this->empresaA, function () {
            Repuesto::create(['codigo' => 'REP-0007', 'nombre' => 'X', 'marca' => 'Y', 'cantidad' => 1, 'precio_unitario' => 1]);
            Repuesto::create(['codigo' => 'REP0003', 'nombre' => 'X', 'marca' => 'Y', 'cantidad' => 1, 'precio_unitario' => 1]);
        });

        $this->actingAs($this->adminA);
        $this->assertSame('REP0008', Repuesto::siguienteCodigo());
    }

    public function test_venta_recalcula_total_descuenta_stock_y_anular_lo_devuelve(): void
    {
        $datos = $this->datosBase($this->empresaA);

        $respuesta = $this->actingAs($this->vendedorA)->post(route('ventas.store'), [
            'cliente_id' => $datos['cliente']->id,
            'total' => 1, // valor manipulado: debe ignorarse
            'metodo_pago' => 'yape',
            'repuestos' => [
                ['id' => $datos['repuesto']->id, 'cantidad' => 2, 'precio' => 59.00],
                ['id' => $datos['repuesto']->id, 'cantidad' => 1, 'precio' => 59.00],
            ],
        ]);

        $venta = Venta::withoutGlobalScope('tenant')->latest('id')->first();
        $respuesta->assertRedirect(route('ventas.show', $venta));

        $this->assertEquals(177.00, (float) $venta->total);
        $this->assertSame('yape', $venta->metodo_pago);
        $this->assertSame($this->vendedorA->id, (int) $venta->usuario_id);
        $this->assertSame(7, (int) $datos['repuesto']->fresh()->cantidad);
        $this->assertDatabaseHas('detalle_ventas', ['venta_id' => $venta->id, 'cantidad' => 3, 'costo_unitario' => 40.00]);

        // Anular devuelve el stock; reactivar lo vuelve a descontar
        $this->actingAs($this->vendedorA)->postJson(route('ventas.anular', $venta->id))->assertOk();
        $this->assertSame(10, (int) $datos['repuesto']->fresh()->cantidad);

        $this->actingAs($this->vendedorA)->patchJson(route('ventas.cambiar-estado', $venta->id), ['estado' => 'pagado'])->assertOk();
        $this->assertSame(7, (int) $datos['repuesto']->fresh()->cantidad);
    }

    public function test_venta_sin_stock_suficiente_no_se_registra(): void
    {
        $datos = $this->datosBase($this->empresaA);

        $this->actingAs($this->adminA)->from(route('ventas.create'))->post(route('ventas.store'), [
            'repuestos' => [['id' => $datos['repuesto']->id, 'cantidad' => 50, 'precio' => 59]],
        ])->assertRedirect(route('ventas.create'))->assertSessionHas('error');

        $this->assertSame(0, Venta::withoutGlobalScope('tenant')->count());
        $this->assertSame(10, (int) $datos['repuesto']->fresh()->cantidad);
    }

    public function test_vendedor_no_accede_a_administracion(): void
    {
        $this->actingAs($this->vendedorA)->get(route('empresa.index'))->assertRedirect(route('dashboard'));
        $this->actingAs($this->vendedorA)->post(route('usuarios.store'), [])->assertRedirect(route('dashboard'));
    }

    public function test_admin_crea_usuarios_en_su_propia_empresa(): void
    {
        $this->actingAs($this->adminA)->post(route('usuarios.store'), [
            'nombre' => 'Nuevo', 'correo' => 'nuevo@test.com', 'contraseña' => 'claveSegura1', 'rol' => 'usuario',
        ])->assertRedirect(route('usuarios.index'));

        $this->assertDatabaseHas('usuarios', ['correo' => 'nuevo@test.com', 'empresa_id' => $this->empresaA->id]);
    }

    public function test_empresa_se_puede_guardar(): void
    {
        $this->actingAs($this->adminA)->put(route('empresa.update'), [
            'nombre' => 'Taller A Editado', 'ruc' => '20123456789', 'ambiente_sunat' => 'beta',
            'razon_social' => 'taller a sac', 'sol_user' => '20123456789usuario1',
        ])->assertSessionHasNoErrors();

        $empresa = $this->empresaA->fresh();
        $this->assertSame('TALLER A SAC', $empresa->razon_social);
        $this->assertSame('MODDATOS', $empresa->getSolUser()); // en beta usa credenciales de prueba

        $empresa->update(['ambiente_sunat' => 'produccion']);
        $this->assertSame('USUARIO1', $empresa->getSolUser()); // sin el RUC duplicado
    }

    public function test_finanzas_anula_con_borrado_logico(): void
    {
        $gasto = $this->comoEmpresa($this->empresaA, fn () => Gasto::create([
            'descripcion' => 'Luz', 'monto' => 50, 'fecha' => now()->toDateString(),
        ]));

        $this->actingAs($this->adminA)->get(route('finanzas.index'))->assertOk()->assertSee('Luz');
        $this->actingAs($this->adminA)->delete(route('finanzas.anularGasto', $gasto->id))->assertRedirect();

        $this->assertSoftDeleted('gastos', ['id' => $gasto->id]);
    }

    public function test_crear_producto_por_kilo_y_servicio(): void
    {
        $categoria = $this->comoEmpresa($this->empresaA, fn () => Categoria::create(['nombre' => 'Abarrotes']));

        $this->actingAs($this->adminA)->post(route('repuestos.store'), [
            'tipo' => 'producto', 'codigo' => 'REP0001', 'nombre' => 'Arroz a granel', 'unidad_medida' => 'KGM',
            'afectacion_igv' => '10', 'cantidad' => 25.5, 'precio_unitario' => 4.20, 'categoria_id' => $categoria->id,
            'codigo_barras' => '7750000000001',
        ])->assertSessionHasNoErrors();

        $this->actingAs($this->adminA)->post(route('repuestos.store'), [
            'tipo' => 'servicio', 'codigo' => 'REP0002', 'nombre' => 'Delivery', 'unidad_medida' => 'ZZ',
            'afectacion_igv' => '10', 'cantidad' => 99, 'precio_unitario' => 5, 'categoria_id' => $categoria->id,
        ])->assertSessionHasNoErrors();

        // Una unidad entera no acepta decimales
        $this->actingAs($this->adminA)->post(route('repuestos.store'), [
            'tipo' => 'producto', 'codigo' => 'REP0003', 'nombre' => 'Caja de clavos', 'unidad_medida' => 'BX',
            'afectacion_igv' => '10', 'cantidad' => 1.5, 'precio_unitario' => 10, 'categoria_id' => $categoria->id,
        ])->assertSessionHasErrors('cantidad');

        $arroz = Repuesto::withoutGlobalScope('tenant')->where('nombre', 'Arroz a granel')->first();
        $delivery = Repuesto::withoutGlobalScope('tenant')->where('nombre', 'Delivery')->first();

        $this->assertSame(25.5, $arroz->cantidad);
        $this->assertSame('25.5 kg', $arroz->stock_formateado);
        $this->assertTrue($delivery->esServicio());
        $this->assertSame(0.0, $delivery->cantidad); // los servicios no guardan stock
        $this->assertNull($arroz->marca); // la marca es opcional
    }

    public function test_venta_con_decimales_y_servicio_sin_stock(): void
    {
        [$arroz, $delivery, $caja] = $this->comoEmpresa($this->empresaA, function () {
            $cat = Categoria::create(['nombre' => 'General']);

            return [
                Repuesto::create(['nombre' => 'Arroz', 'unidad_medida' => 'KGM', 'cantidad' => 10, 'precio_unitario' => 4, 'categoria_id' => $cat->id]),
                Repuesto::create(['nombre' => 'Delivery', 'tipo' => 'servicio', 'unidad_medida' => 'ZZ', 'precio_unitario' => 5, 'categoria_id' => $cat->id]),
                Repuesto::create(['nombre' => 'Caja', 'unidad_medida' => 'BX', 'cantidad' => 5, 'precio_unitario' => 10, 'categoria_id' => $cat->id]),
            ];
        });

        $this->actingAs($this->vendedorA)->post(route('ventas.store'), [
            'repuestos' => [
                ['id' => $arroz->id, 'cantidad' => 2.5, 'precio' => 4],
                ['id' => $delivery->id, 'cantidad' => 1, 'precio' => 5],
            ],
        ])->assertSessionHasNoErrors()->assertSessionMissing('error');

        $venta = Venta::withoutGlobalScope('tenant')->latest('id')->first();
        $this->assertEquals(15.00, (float) $venta->total);
        $this->assertSame(7.5, $arroz->fresh()->cantidad);
        $this->assertSame(0.0, $delivery->fresh()->cantidad);
        $this->assertDatabaseHas('detalle_ventas', ['venta_id' => $venta->id, 'repuesto_id' => $arroz->id, 'unidad_medida' => 'KGM']);

        // Decimales en una unidad entera: rechazado, sin tocar stock
        $this->actingAs($this->vendedorA)->from(route('ventas.create'))->post(route('ventas.store'), [
            'repuestos' => [['id' => $caja->id, 'cantidad' => 1.5, 'precio' => 10]],
        ])->assertSessionHas('error');
        $this->assertSame(5.0, $caja->fresh()->cantidad);

        // Anular devuelve el arroz y no toca el servicio
        $this->actingAs($this->vendedorA)->postJson(route('ventas.anular', $venta->id))->assertOk();
        $this->assertSame(10.0, $arroz->fresh()->cantidad);
    }

    public function test_url_antigua_de_repuestos_redirige_a_productos(): void
    {
        $this->actingAs($this->adminA)->get('/repuestos/create')->assertRedirect('/productos/create');
        $this->actingAs($this->adminA)->get('/productos')->assertOk()->assertSee('Productos');
    }

    public function test_factura_separa_gravado_exonerado_e_inafecto(): void
    {
        $this->empresaA->update(['ruc' => '20000000001', 'facturacion_activa' => true, 'ambiente_sunat' => 'beta']);

        $venta = $this->comoEmpresa($this->empresaA, function () {
            $cat = Categoria::create(['nombre' => 'Mixto']);
            $gravado = Repuesto::create(['nombre' => 'Gaseosa', 'cantidad' => 10, 'precio_unitario' => 11.80, 'categoria_id' => $cat->id]);
            $exonerado = Repuesto::create(['nombre' => 'Libro', 'afectacion_igv' => '20', 'cantidad' => 10, 'precio_unitario' => 50, 'categoria_id' => $cat->id]);
            $venta = Venta::create(['fecha' => now(), 'total' => 61.80, 'estado' => 'pagado']);
            foreach ([[$gravado, 11.80], [$exonerado, 50]] as [$r, $p]) {
                \App\Models\DetalleVenta::create([
                    'venta_id' => $venta->id, 'repuesto_id' => $r->id, 'cantidad' => 1, 'precio_unitario' => $p,
                    'subtotal' => $p, 'afectacion_igv' => $r->afectacion_igv, 'unidad_medida' => 'NIU',
                ]);
            }

            return $venta->load('detalles.repuesto', 'cliente', 'empresa');
        });

        $servicio = new \App\Services\FacturacionService();
        $empresa = new \ReflectionProperty($servicio, 'empresa');
        $empresa->setValue($servicio, $this->empresaA->fresh());
        $crear = new \ReflectionMethod($servicio, 'crearInvoice');
        $invoice = $crear->invoke($servicio, $venta, '03', 'B001', 1);

        $this->assertEquals(10.00, $invoice->getMtoOperGravadas());
        $this->assertEquals(50.00, $invoice->getMtoOperExoneradas());
        $this->assertEquals(1.80, $invoice->getMtoIGV());
        $this->assertEquals(61.80, $invoice->getMtoImpVenta());
        $this->assertSame('20', $invoice->getDetails()[1]->getTipAfeIgv());

        // Ticket: base imponible e IGV solo de lo gravado
        $this->assertEquals(10.00, $venta->calcularBaseImponible());
        $this->assertEquals(50.00, $venta->calcularNoGravado());
        $this->assertEquals(1.80, $venta->calcularIgv());
    }

    // =====================================================================
    // COMPRAS Y KARDEX
    // =====================================================================

    /** Verifica que el kardex cuadre: suma de movimientos = stock actual. */
    private function assertKardexCuadra(Repuesto $repuesto): void
    {
        $movimientos = \App\Models\MovimientoInventario::withoutGlobalScope('tenant')->where('repuesto_id', $repuesto->id)->orderBy('id')->get();
        $saldo = 0.0;
        foreach ($movimientos as $m) {
            $this->assertEqualsWithDelta($saldo, $m->stock_anterior, 0.0001, 'Saldo anterior inconsistente');
            $saldo = round($saldo + $m->cantidad_con_signo, 3);
            $this->assertEqualsWithDelta($saldo, $m->stock_nuevo, 0.0001, 'Saldo nuevo inconsistente');
        }
        $this->assertEqualsWithDelta($repuesto->fresh()->cantidad, $saldo, 0.0001, 'El kardex no cuadra con el stock');
    }

    private function crearProductoVia(array $datos = []): Repuesto
    {
        $categoria = $this->comoEmpresa($this->empresaA, fn () => Categoria::firstOrCreate(['nombre' => 'General', 'empresa_id' => $this->empresaA->id]));

        $this->actingAs($this->adminA)->post(route('repuestos.store'), $datos + [
            'tipo' => 'producto', 'codigo' => 'REP' . random_int(1000, 9999), 'nombre' => 'Aceite 1L', 'unidad_medida' => 'NIU',
            'afectacion_igv' => '10', 'cantidad' => 10, 'precio_unitario' => 20, 'precio_compra' => 12, 'categoria_id' => $categoria->id,
        ])->assertSessionHasNoErrors();

        return Repuesto::withoutGlobalScope('tenant')->latest('id')->first();
    }

    public function test_compra_suma_stock_actualiza_costo_y_registra_kardex(): void
    {
        $producto = $this->crearProductoVia();
        $proveedor = $this->comoEmpresa($this->empresaA, fn () => \App\Models\Proveedor::create(['nombre' => 'Distribuidora SAC']));

        $this->actingAs($this->adminA)->post(route('compras.store'), [
            'proveedor_id' => $proveedor->id, 'fecha' => now()->toDateString(), 'tipo_documento' => 'factura',
            'numero_documento' => 'f001-123', 'actualizar_costo' => 1,
            'items' => [['id' => $producto->id, 'cantidad' => 5, 'costo' => 13.50]],
        ])->assertSessionHasNoErrors()->assertRedirect();

        $compra = \App\Models\Compra::withoutGlobalScope('tenant')->latest('id')->first();
        $this->assertEquals(67.50, (float) $compra->total);
        $this->assertSame('F001-123', $compra->numero_documento);
        $this->assertSame(15.0, $producto->fresh()->cantidad);
        $this->assertEquals(13.50, (float) $producto->fresh()->precio_compra);
        $this->assertDatabaseHas('movimientos_inventario', [
            'repuesto_id' => $producto->id, 'motivo' => 'compra', 'tipo' => 'entrada',
            'referencia_type' => \App\Models\Compra::class, 'referencia_id' => $compra->id,
        ]);
        $this->assertKardexCuadra($producto);

        // El mismo documento del mismo proveedor no se puede registrar dos veces
        $this->actingAs($this->adminA)->post(route('compras.store'), [
            'proveedor_id' => $proveedor->id, 'fecha' => now()->toDateString(), 'tipo_documento' => 'factura',
            'numero_documento' => 'F001-123', 'items' => [['id' => $producto->id, 'cantidad' => 1, 'costo' => 1]],
        ])->assertSessionHasErrors('numero_documento');

        // Páginas y finanzas
        $this->actingAs($this->adminA)->get(route('compras.index'))->assertOk()->assertSee($compra->numero);
        $this->actingAs($this->adminA)->get(route('compras.show', $compra))->assertOk()->assertSee('Aceite 1L');
        $this->actingAs($this->adminA)->get(route('finanzas.index'))->assertOk()->assertSee('Compra ' . $compra->numero);
    }

    public function test_anular_compra_revierte_stock_y_no_permite_si_ya_se_vendio(): void
    {
        $producto = $this->crearProductoVia(['cantidad' => 0]);

        $this->actingAs($this->adminA)->post(route('compras.store'), [
            'fecha' => now()->toDateString(), 'tipo_documento' => 'ninguno',
            'items' => [['id' => $producto->id, 'cantidad' => 4, 'costo' => 10]],
        ])->assertSessionHasNoErrors();
        $compra = \App\Models\Compra::withoutGlobalScope('tenant')->latest('id')->first();

        // Se venden 3 de las 4 unidades -> no se puede anular la compra
        $this->actingAs($this->adminA)->post(route('ventas.store'), [
            'repuestos' => [['id' => $producto->id, 'cantidad' => 3, 'precio' => 20]],
        ])->assertSessionMissing('error');

        $this->actingAs($this->adminA)->post(route('compras.anular', $compra), ['motivo' => 'Devolución'])
            ->assertSessionHas('error');
        $this->assertSame('registrada', $compra->fresh()->estado);
        $this->assertSame(1.0, $producto->fresh()->cantidad);

        // Se anula la venta -> ahora sí se puede anular la compra
        $venta = Venta::withoutGlobalScope('tenant')->latest('id')->first();
        $this->actingAs($this->adminA)->postJson(route('ventas.anular', $venta->id))->assertOk();
        $this->actingAs($this->adminA)->post(route('compras.anular', $compra), ['motivo' => 'Devolución al proveedor'])
            ->assertSessionHas('success');

        $this->assertSame('anulada', $compra->fresh()->estado);
        $this->assertSame(0.0, $producto->fresh()->cantidad);
        $this->assertKardexCuadra($producto);

        $motivos = \App\Models\MovimientoInventario::withoutGlobalScope('tenant')->where('repuesto_id', $producto->id)->orderBy('id')->pluck('motivo')->all();
        $this->assertSame(['compra', 'venta', 'anulacion_venta', 'anulacion_compra'], $motivos);
    }

    public function test_alta_edicion_y_ajustes_quedan_en_el_kardex(): void
    {
        $producto = $this->crearProductoVia(['cantidad' => 10]);
        $this->assertDatabaseHas('movimientos_inventario', ['repuesto_id' => $producto->id, 'motivo' => 'stock_inicial', 'stock_nuevo' => 10]);

        // Editar el stock desde el formulario
        $this->actingAs($this->adminA)->put(route('repuestos.update', $producto), [
            'tipo' => 'producto', 'codigo' => $producto->codigo, 'nombre' => $producto->nombre, 'unidad_medida' => 'NIU',
            'afectacion_igv' => '10', 'cantidad' => 8, 'precio_unitario' => 20, 'categoria_id' => $producto->categoria_id,
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('movimientos_inventario', ['repuesto_id' => $producto->id, 'motivo' => 'edicion_producto', 'tipo' => 'salida', 'cantidad' => 2]);

        // Agregar stock manual
        $this->actingAs($this->adminA)->post(route('repuestos.updateCantidad', $producto->id), ['cantidad' => 5])->assertSessionHasNoErrors();
        $this->assertSame(13.0, $producto->fresh()->cantidad);

        // Merma, conteo físico y conteo sin diferencia
        $this->actingAs($this->adminA)->post(route('kardex.ajustar', $producto), ['tipo_ajuste' => 'salida', 'cantidad' => 1, 'observacion' => 'Producto dañado'])->assertSessionHas('success');
        $this->actingAs($this->adminA)->post(route('kardex.ajustar', $producto), ['tipo_ajuste' => 'conteo', 'cantidad' => 10, 'observacion' => 'Conteo mensual'])->assertSessionHas('success');
        $this->actingAs($this->adminA)->post(route('kardex.ajustar', $producto), ['tipo_ajuste' => 'conteo', 'cantidad' => 10, 'observacion' => 'Reconteo'])->assertSessionHas('warning');
        // No se puede sacar más de lo que hay
        $this->actingAs($this->adminA)->post(route('kardex.ajustar', $producto), ['tipo_ajuste' => 'salida', 'cantidad' => 50, 'observacion' => 'Error'])->assertSessionHas('error');

        $this->assertSame(10.0, $producto->fresh()->cantidad);
        $this->assertKardexCuadra($producto);

        // Vistas y exportación
        $this->actingAs($this->adminA)->get(route('kardex.index'))->assertOk()->assertSee('Conteo físico');
        $this->actingAs($this->adminA)->get(route('kardex.show', $producto))->assertOk()->assertSee('Producto dañado');
        $csv = $this->actingAs($this->adminA)->get(route('kardex.show', ['repuesto' => $producto, 'exportar' => 'csv']));
        $csv->assertOk();
        $this->assertStringContainsString('Conteo físico', $csv->streamedContent());
    }

    public function test_permisos_y_aislamiento_de_compras(): void
    {
        $this->actingAs($this->vendedorA)->get(route('compras.index'))->assertRedirect(route('dashboard'));
        $this->actingAs($this->vendedorA)->get(route('kardex.index'))->assertOk(); // el vendedor puede consultar

        $compraB = $this->comoEmpresa($this->empresaB, fn () => \App\Models\Compra::create(['fecha' => now(), 'tipo_documento' => 'ninguno', 'total' => 10]));
        $this->actingAs($this->adminA)->get(route('compras.show', $compraB))->assertNotFound();
    }

    public function test_busqueda_exportacion_y_filtros_de_listados(): void
    {
        $datos = $this->datosBase($this->empresaA);
        $this->comoEmpresa($this->empresaA, fn () => Cliente::create(['nombre' => 'Ana Torres', 'dni' => '87654321', 'telefono' => '911222333']));

        // Búsqueda de clientes en el servidor
        $this->actingAs($this->adminA)->get(route('clientes.index', ['q' => 'Ana']))
            ->assertOk()->assertSee('Ana Torres')->assertDontSee('Juan Pérez');

        // Exportación real a CSV
        $csv = $this->actingAs($this->adminA)->get(route('clientes.index', ['exportar' => 'csv']));
        $csv->assertOk();
        $this->assertStringContainsString('Juan Pérez', $csv->streamedContent());

        // Filtros del historial de ventas
        $this->actingAs($this->adminA)->post(route('ventas.store'), [
            'cliente_id' => $datos['cliente']->id, 'metodo_pago' => 'tarjeta',
            'repuestos' => [['id' => $datos['repuesto']->id, 'cantidad' => 1, 'precio' => 59]],
        ]);
        $this->actingAs($this->adminA)->get(route('ventas.index', ['metodo_pago' => 'tarjeta', 'q' => 'Juan']))
            ->assertOk()->assertSee('Juan Pérez');
        $this->actingAs($this->adminA)->get(route('ventas.index', ['metodo_pago' => 'yape']))
            ->assertOk()->assertSee('Sin resultados');

        // Filtro por estado de stock en productos
        $this->actingAs($this->adminA)->get(route('repuestos.index', ['stock' => 'agotado']))->assertOk()->assertDontSee('Pastilla de freno');
    }

    public function test_mensajes_de_validacion_en_espanol(): void
    {
        $this->actingAs($this->adminA)->post(route('categorias.store'), [])
            ->assertSessionHasErrors(['nombre' => 'El nombre es obligatorio.']);
    }

    public function test_logo_de_empresa_hasta_2_mb(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $datos = ['nombre' => 'Taller A', 'ambiente_sunat' => 'beta'];

        // 1.5 MB: se acepta y se guarda
        $this->actingAs($this->adminA)->put(route('empresa.update'), $datos + [
            'logo' => \Illuminate\Http\UploadedFile::fake()->image('logo.png', 400, 400)->size(1536),
        ])->assertSessionHasNoErrors();
        $logo = $this->empresaA->fresh()->logo;
        $this->assertNotNull($logo);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($logo);

        // 2.5 MB: se rechaza con mensaje claro
        $this->actingAs($this->adminA)->put(route('empresa.update'), $datos + [
            'logo' => \Illuminate\Http\UploadedFile::fake()->image('grande.png', 400, 400)->size(2560),
        ])->assertSessionHasErrors(['logo' => 'El logo no debe pesar más de 2 MB.']);
    }

    // =====================================================================
    // TICKETS RÁPIDOS
    // =====================================================================

    public function test_ticket_rapido_completo(): void
    {
        $this->empresaA->update(['nombre_comercial' => 'TALLER A', 'ruc' => '20123456789', 'direccion' => 'Av. Lima 123', 'telefono' => '999888777']);

        // Las pantallas cargan
        $this->actingAs($this->vendedorA)->get(route('tickets.create'))->assertOk()->assertSee('Vender');
        $this->actingAs($this->vendedorA)->get(route('tickets.index'))->assertOk();

        // Registrar: el total se calcula en el servidor
        $this->actingAs($this->vendedorA)->post(route('tickets.store'), [
            'cliente_documento' => '45678912',
            'cliente_nombre' => 'María López',
            'items' => [
                ['descripcion' => 'Cambio de aceite', 'cantidad' => 1, 'precio' => 80],
                ['descripcion' => 'Filtro de aire', 'cantidad' => 2, 'precio' => 25.5],
            ],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $ticket = \App\Models\Ticket::withoutGlobalScope('tenant')->latest('id')->first();
        $this->assertSame(1, (int) $ticket->numero);
        $this->assertEquals(131.00, (float) $ticket->total);
        $this->assertSame('T-000001', $ticket->numero_formateado);
        $this->assertSame((int) $this->empresaA->id, (int) $ticket->empresa_id);

        // Detalle, ticket imprimible y boleta PDF con los datos de la empresa
        $this->actingAs($this->vendedorA)->get(route('tickets.show', $ticket))->assertOk()->assertSee('Filtro de aire');
        $this->actingAs($this->vendedorA)->get(route('tickets.imprimir', $ticket))->assertOk()
            ->assertSee('TALLER A')->assertSee('RUC: 20123456789')->assertSee('María López')->assertSee('131.00');
        $pdf = $this->actingAs($this->vendedorA)->get(route('tickets.pdf', $ticket));
        $pdf->assertOk();
        $this->assertSame('application/pdf', $pdf->headers->get('Content-Type'));

        // El nombre se recuerda por DNI (sin internet)
        $this->actingAs($this->vendedorA)->getJson(route('tickets.buscar-cliente', ['doc' => '45678912']))
            ->assertOk()->assertJson(['encontrado' => true, 'nombre' => 'María López', 'origen' => 'tickets']);

        // El correlativo es por empresa
        $this->actingAs($this->adminB)->post(route('tickets.store'), ['items' => [['descripcion' => 'X', 'cantidad' => 1, 'precio' => 1]]]);
        $this->assertSame(1, (int) \App\Models\Ticket::withoutGlobalScope('tenant')->where('empresa_id', $this->empresaB->id)->value('numero'));
        $this->actingAs($this->vendedorA)->post(route('tickets.store'), ['items' => [['descripcion' => 'Y', 'cantidad' => 1, 'precio' => 1]]]);
        $this->assertSame(2, (int) \App\Models\Ticket::withoutGlobalScope('tenant')->where('empresa_id', $this->empresaA->id)->max('numero'));

        // Otra empresa no puede ver el ticket; el vendedor no puede anular
        $this->actingAs($this->adminB)->get(route('tickets.show', $ticket))->assertNotFound();
        $this->actingAs($this->vendedorA)->post(route('tickets.anular', $ticket))->assertRedirect(route('dashboard'));
        $this->actingAs($this->adminA)->post(route('tickets.anular', $ticket))->assertSessionHas('success');
        $this->assertSame('anulado', $ticket->fresh()->estado);
    }

    public function test_tickets_cuentan_como_ingreso_en_finanzas(): void
    {
        $this->actingAs($this->adminA)->post(route('tickets.store'), [
            'cliente_nombre' => 'Pedro Ruiz',
            'items' => [['descripcion' => 'Servicio', 'cantidad' => 1, 'precio' => 150]],
        ]);
        $this->actingAs($this->adminA)->post(route('tickets.store'), [
            'items' => [['descripcion' => 'Anulado', 'cantidad' => 1, 'precio' => 999]],
        ]);
        $anulado = \App\Models\Ticket::withoutGlobalScope('tenant')->latest('id')->first();
        $this->actingAs($this->adminA)->post(route('tickets.anular', $anulado));

        // Activado (por defecto): suma el ticket emitido, no el anulado
        $vista = $this->actingAs($this->adminA)->get(route('finanzas.index'))->assertOk();
        $this->assertEquals(150.0, $vista->viewData('totalIngresos'));
        $vista->assertSee('Ticket T-000001 - Pedro Ruiz');

        $this->actingAs($this->adminA)->get(route('finanzas.index', ['tipo_movimiento' => 'ingresos']))
            ->assertOk()->assertSee('Ticket T-000001');

        // Desactivado: deja de contar
        config(['sistema.tickets_en_finanzas' => false]);
        $vista = $this->actingAs($this->adminA)->get(route('finanzas.index'))->assertOk();
        $this->assertEquals(0.0, $vista->viewData('totalIngresos'));
        $vista->assertDontSee('Ticket T-000001');
    }

    public function test_ticket_valida_documento_e_items(): void
    {
        $this->actingAs($this->adminA)->post(route('tickets.store'), [
            'cliente_documento' => '1234',
            'items' => [['descripcion' => '', 'cantidad' => 0, 'precio' => 5]],
        ])->assertSessionHasErrors(['cliente_documento', 'items.0.descripcion', 'items.0.cantidad']);

        $this->actingAs($this->adminA)->post(route('tickets.store'), [])->assertSessionHasErrors('items');
        $this->assertSame(0, \App\Models\Ticket::withoutGlobalScope('tenant')->count());
    }

    public function test_numero_a_letras(): void
    {
        $this->assertSame('CIENTO SETENTA Y SIETE CON 00/100 SOLES', \App\Support\NumeroALetras::monto(177));
        $this->assertSame('VEINTIÚN MIL CON 50/100 SOLES', \App\Support\NumeroALetras::monto(21000.5));
    }
}
