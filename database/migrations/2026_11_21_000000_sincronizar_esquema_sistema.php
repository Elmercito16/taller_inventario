<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sincroniza el esquema completo del sistema.
 *
 * Es IDEMPOTENTE: crea las tablas que no existen y agrega SOLO las columnas
 * que falten. No modifica ni borra columnas existentes, por lo que se puede
 * ejecutar sin riesgo tanto en MySQL (local) como en PostgreSQL (producción).
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->esquema() as $tabla => $columnas) {
            if (! Schema::hasTable($tabla)) {
                Schema::create($tabla, function (Blueprint $table) use ($columnas, $tabla) {
                    $table->id();
                    foreach ($columnas as $definir) {
                        $definir($table);
                    }
                    $table->timestamps();

                    if (array_key_exists('empresa_id', $columnas) && $tabla !== 'usuarios') {
                        $table->index('empresa_id');
                    }
                });

                continue;
            }

            $faltantes = array_filter(
                $columnas,
                fn ($nombre) => ! Schema::hasColumn($tabla, $nombre),
                ARRAY_FILTER_USE_KEY
            );

            if ($faltantes) {
                Schema::table($tabla, function (Blueprint $table) use ($faltantes) {
                    foreach ($faltantes as $definir) {
                        $definir($table);
                    }
                });
            }
        }
    }

    public function down(): void
    {
        // Solo revierte las columnas nuevas de funcionalidades; nunca borra tablas con datos.
        $nuevas = [
            'ventas' => ['metodo_pago', 'usuario_id'],
            'repuestos' => ['precio_compra'],
            'detalle_ventas' => ['costo_unitario'],
            'gastos' => ['deleted_at'],
            'ingresos' => ['deleted_at'],
            'comprobantes' => ['ticket_baja'],
        ];

        foreach ($nuevas as $tabla => $cols) {
            $existentes = array_values(array_filter($cols, fn ($c) => Schema::hasColumn($tabla, $c)));
            if ($existentes) {
                Schema::table($tabla, fn (Blueprint $t) => $t->dropColumn($existentes));
            }
        }
    }

    /**
     * Definición de columnas por tabla (nombre => callback que la define).
     */
    private function esquema(): array
    {
        $empresa = ['empresa_id' => fn (Blueprint $t) => $t->unsignedBigInteger('empresa_id')->nullable()];

        return [
            'empresas' => [
                'nombre' => fn ($t) => $t->string('nombre'),
                'ruc' => fn ($t) => $t->string('ruc', 20)->nullable(),
                'razon_social' => fn ($t) => $t->string('razon_social')->nullable(),
                'nombre_comercial' => fn ($t) => $t->string('nombre_comercial')->nullable(),
                'direccion' => fn ($t) => $t->string('direccion')->nullable(),
                'direccion_fiscal' => fn ($t) => $t->string('direccion_fiscal')->nullable(),
                'ubigeo' => fn ($t) => $t->string('ubigeo', 6)->nullable(),
                'departamento' => fn ($t) => $t->string('departamento', 100)->nullable(),
                'provincia' => fn ($t) => $t->string('provincia', 100)->nullable(),
                'distrito' => fn ($t) => $t->string('distrito', 100)->nullable(),
                'urbanizacion' => fn ($t) => $t->string('urbanizacion', 100)->nullable(),
                'codigo_pais' => fn ($t) => $t->string('codigo_pais', 2)->default('PE'),
                'telefono' => fn ($t) => $t->string('telefono', 20)->nullable(),
                'email' => fn ($t) => $t->string('email')->nullable(),
                'web' => fn ($t) => $t->string('web')->nullable(),
                'logo' => fn ($t) => $t->string('logo')->nullable(),
                'sol_user' => fn ($t) => $t->string('sol_user')->nullable(),
                'sol_pass' => fn ($t) => $t->string('sol_pass')->nullable(),
                'certificado_path' => fn ($t) => $t->string('certificado_path')->nullable(),
                'certificado_password' => fn ($t) => $t->string('certificado_password')->nullable(),
                'facturacion_activa' => fn ($t) => $t->boolean('facturacion_activa')->default(false),
                'ambiente_sunat' => fn ($t) => $t->string('ambiente_sunat', 20)->default('beta'),
            ],

            'usuarios' => $empresa + [
                'nombre' => fn ($t) => $t->string('nombre'),
                'correo' => fn ($t) => $t->string('correo')->unique(),
                'contraseña' => fn ($t) => $t->string('contraseña'),
                'rol' => fn ($t) => $t->string('rol', 20)->default('usuario'),
                'remember_token' => fn ($t) => $t->rememberToken(),
            ],

            'categorias' => $empresa + [
                'nombre' => fn ($t) => $t->string('nombre'),
                'descripcion' => fn ($t) => $t->text('descripcion')->nullable(),
            ],

            'proveedors' => $empresa + [
                'nombre' => fn ($t) => $t->string('nombre'),
                'contacto' => fn ($t) => $t->string('contacto')->nullable(),
                'telefono' => fn ($t) => $t->string('telefono')->nullable(),
                'direccion' => fn ($t) => $t->string('direccion')->nullable(),
            ],

            'clientes' => $empresa + [
                'dni' => fn ($t) => $t->string('dni', 8)->nullable(),
                'ruc' => fn ($t) => $t->string('ruc', 11)->nullable(),
                'nombre' => fn ($t) => $t->string('nombre'),
                'telefono' => fn ($t) => $t->string('telefono', 20)->nullable(),
                'direccion' => fn ($t) => $t->string('direccion')->nullable(),
                'email' => fn ($t) => $t->string('email')->nullable(),
            ],

            'repuestos' => $empresa + [
                'imagen' => fn ($t) => $t->string('imagen')->nullable(),
                'codigo' => fn ($t) => $t->string('codigo'),
                'nombre' => fn ($t) => $t->string('nombre'),
                'marca' => fn ($t) => $t->string('marca')->nullable(),
                'descripcion' => fn ($t) => $t->text('descripcion')->nullable(),
                'cantidad' => fn ($t) => $t->integer('cantidad')->default(0),
                'minimo_stock' => fn ($t) => $t->integer('minimo_stock')->default(0),
                'precio_unitario' => fn ($t) => $t->decimal('precio_unitario', 10, 2)->default(0),
                'precio_compra' => fn ($t) => $t->decimal('precio_compra', 10, 2)->nullable(),
                'proveedor_id' => fn ($t) => $t->unsignedBigInteger('proveedor_id')->nullable(),
                'categoria_id' => fn ($t) => $t->unsignedBigInteger('categoria_id')->nullable(),
                'fecha_ingreso' => fn ($t) => $t->date('fecha_ingreso')->nullable(),
            ],

            'ventas' => $empresa + [
                'cliente_id' => fn ($t) => $t->unsignedBigInteger('cliente_id')->nullable(),
                'usuario_id' => fn ($t) => $t->unsignedBigInteger('usuario_id')->nullable(),
                'fecha' => fn ($t) => $t->dateTime('fecha'),
                'total' => fn ($t) => $t->decimal('total', 10, 2)->default(0),
                'estado' => fn ($t) => $t->string('estado', 20)->default('pagado'),
                'metodo_pago' => fn ($t) => $t->string('metodo_pago', 20)->default('efectivo'),
            ],

            'detalle_ventas' => $empresa + [
                'venta_id' => fn ($t) => $t->unsignedBigInteger('venta_id'),
                'repuesto_id' => fn ($t) => $t->unsignedBigInteger('repuesto_id'),
                'cantidad' => fn ($t) => $t->integer('cantidad'),
                'precio_unitario' => fn ($t) => $t->decimal('precio_unitario', 10, 2),
                'subtotal' => fn ($t) => $t->decimal('subtotal', 10, 2),
                'costo_unitario' => fn ($t) => $t->decimal('costo_unitario', 10, 2)->nullable(),
            ],

            'gastos' => $empresa + [
                'descripcion' => fn ($t) => $t->string('descripcion'),
                'monto' => fn ($t) => $t->decimal('monto', 10, 2),
                'fecha' => fn ($t) => $t->date('fecha'),
                'categoria' => fn ($t) => $t->string('categoria', 100)->nullable(),
                'deleted_at' => fn ($t) => $t->softDeletes(),
            ],

            'ingresos' => $empresa + [
                'descripcion' => fn ($t) => $t->string('descripcion'),
                'monto' => fn ($t) => $t->decimal('monto', 10, 2),
                'fecha' => fn ($t) => $t->date('fecha'),
                'tipo' => fn ($t) => $t->string('tipo', 100)->nullable(),
                'deleted_at' => fn ($t) => $t->softDeletes(),
            ],

            'series' => $empresa + [
                'tipo_comprobante' => fn ($t) => $t->string('tipo_comprobante', 2),
                'serie' => fn ($t) => $t->string('serie', 4),
                'correlativo_actual' => fn ($t) => $t->unsignedInteger('correlativo_actual')->default(0),
                'activo' => fn ($t) => $t->boolean('activo')->default(true),
                'descripcion' => fn ($t) => $t->string('descripcion')->nullable(),
            ],

            'comprobantes' => $empresa + [
                'venta_id' => fn ($t) => $t->unsignedBigInteger('venta_id')->nullable(),
                'comprobante_afectado_id' => fn ($t) => $t->unsignedBigInteger('comprobante_afectado_id')->nullable(),
                'motivo_nota' => fn ($t) => $t->string('motivo_nota', 500)->nullable(),
                'codigo_motivo_nota' => fn ($t) => $t->string('codigo_motivo_nota', 2)->nullable(),
                'cliente_id' => fn ($t) => $t->unsignedBigInteger('cliente_id')->nullable(),
                'tipo_comprobante' => fn ($t) => $t->string('tipo_comprobante', 2),
                'serie' => fn ($t) => $t->string('serie', 4),
                'correlativo' => fn ($t) => $t->unsignedInteger('correlativo'),
                'numero_completo' => fn ($t) => $t->string('numero_completo', 20),
                'cliente_tipo_doc' => fn ($t) => $t->string('cliente_tipo_doc', 2)->nullable(),
                'cliente_num_doc' => fn ($t) => $t->string('cliente_num_doc', 15)->nullable(),
                'cliente_nombre' => fn ($t) => $t->string('cliente_nombre')->nullable(),
                'subtotal' => fn ($t) => $t->decimal('subtotal', 12, 2)->default(0),
                'igv' => fn ($t) => $t->decimal('igv', 12, 2)->default(0),
                'total' => fn ($t) => $t->decimal('total', 12, 2)->default(0),
                'total_letras' => fn ($t) => $t->string('total_letras')->nullable(),
                'moneda' => fn ($t) => $t->string('moneda', 3)->default('PEN'),
                'total_gravadas' => fn ($t) => $t->decimal('total_gravadas', 12, 2)->nullable(),
                'total_igv' => fn ($t) => $t->decimal('total_igv', 12, 2)->nullable(),
                'total_exoneradas' => fn ($t) => $t->decimal('total_exoneradas', 12, 2)->nullable(),
                'total_inafectas' => fn ($t) => $t->decimal('total_inafectas', 12, 2)->nullable(),
                'total_gratuitas' => fn ($t) => $t->decimal('total_gratuitas', 12, 2)->nullable(),
                'total_descuentos' => fn ($t) => $t->decimal('total_descuentos', 12, 2)->nullable(),
                'fecha_emision' => fn ($t) => $t->dateTime('fecha_emision')->nullable(),
                'fecha_envio_sunat' => fn ($t) => $t->dateTime('fecha_envio_sunat')->nullable(),
                'fecha_vencimiento' => fn ($t) => $t->date('fecha_vencimiento')->nullable(),
                'hora_emision' => fn ($t) => $t->time('hora_emision')->nullable(),
                'estado_sunat' => fn ($t) => $t->string('estado_sunat', 20)->default('pendiente'),
                'codigo_sunat' => fn ($t) => $t->string('codigo_sunat', 10)->nullable(),
                'mensaje_sunat' => fn ($t) => $t->text('mensaje_sunat')->nullable(),
                'observaciones_sunat' => fn ($t) => $t->text('observaciones_sunat')->nullable(),
                'hash' => fn ($t) => $t->string('hash')->nullable(),
                'hash_cpe' => fn ($t) => $t->string('hash_cpe')->nullable(),
                'hash_cdr' => fn ($t) => $t->string('hash_cdr')->nullable(),
                'xml' => fn ($t) => $t->longText('xml')->nullable(),
                'xml_path' => fn ($t) => $t->string('xml_path')->nullable(),
                'cdr' => fn ($t) => $t->longText('cdr')->nullable(),
                'cdr_path' => fn ($t) => $t->string('cdr_path')->nullable(),
                'pdf_path' => fn ($t) => $t->string('pdf_path')->nullable(),
                'anulado' => fn ($t) => $t->boolean('anulado')->default(false),
                'fecha_anulacion' => fn ($t) => $t->dateTime('fecha_anulacion')->nullable(),
                'motivo_anulacion' => fn ($t) => $t->text('motivo_anulacion')->nullable(),
                'usuario_anulacion_id' => fn ($t) => $t->unsignedBigInteger('usuario_anulacion_id')->nullable(),
                'ticket_baja' => fn ($t) => $t->string('ticket_baja', 50)->nullable(),
                'ambiente' => fn ($t) => $t->string('ambiente', 20)->nullable(),
                'usuario_emision_id' => fn ($t) => $t->unsignedBigInteger('usuario_emision_id')->nullable(),
                'estado' => fn ($t) => $t->string('estado', 20)->nullable(),
                'numero_comprobante' => fn ($t) => $t->string('numero_comprobante', 20)->nullable(),
            ],
        ];
    }
};
