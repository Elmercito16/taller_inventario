<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ComprobanteController;
use App\Http\Controllers\KardexController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\FinanzasController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RepuestoController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// ==============================
// RUTAS PÚBLICAS
// ==============================

Route::get('/', fn () => redirect()->route('login.form'));

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [LoginController::class, 'login'])->name('login')->middleware('throttle:10,1');

// Healthcheck para el balanceador (no expone información).
// Sin el grupo "web": no abre sesión ni crea filas en la tabla sessions.
Route::get('/healthz', fn () => response()->json(['ok' => true]))
    ->withoutMiddleware('web');

// Diagnóstico SOLO en entorno local
if (app()->environment('local')) {
    Route::get('/test', function () {
        try {
            DB::connection()->getPdo();

            return response()->json(['status' => 'OK', 'database' => 'Connected']);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'ERROR', 'message' => $e->getMessage()], 500);
        }
    });
}

// ==============================
// RUTAS PROTEGIDAS
// ==============================
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Panel principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ------------------------------
    // Productos y servicios (URL /productos; los nombres internos "repuestos.*"
    // se mantienen por compatibilidad). Rutas personalizadas ANTES del resource.
    // ------------------------------
    Route::get('/productos/{id}/cantidad', [RepuestoController::class, 'cantidad'])->name('repuestos.cantidad');
    Route::post('/productos/{id}/cantidad', [RepuestoController::class, 'updateCantidad'])->name('repuestos.updateCantidad');
    Route::post('/productos/quick', [RepuestoController::class, 'storeQuick'])->name('repuestos.storeQuick');
    Route::resource('productos', RepuestoController::class)
        ->names('repuestos')
        ->parameters(['productos' => 'repuesto'])
        ->except(['show']);

    // Enlaces antiguos /repuestos/... siguen funcionando
    Route::get('/repuestos/{ruta?}', fn (?string $ruta = null) => redirect('/productos' . ($ruta ? '/' . $ruta : ''), 301))
        ->where('ruta', '.*');

    // ------------------------------
    // Compras y kardex (registrar compras y ajustar stock: admin y encargado)
    // ------------------------------
    Route::middleware('rol:admin,encargado')->group(function () {
        Route::get('/compras', [CompraController::class, 'index'])->name('compras.index');
        Route::get('/compras/create', [CompraController::class, 'create'])->name('compras.create');
        Route::post('/compras', [CompraController::class, 'store'])->name('compras.store');
        Route::get('/compras/{compra}', [CompraController::class, 'show'])->name('compras.show');
        Route::post('/compras/{compra}/anular', [CompraController::class, 'anular'])->name('compras.anular');

        Route::post('/kardex/{repuesto}/ajuste', [KardexController::class, 'ajustar'])->name('kardex.ajustar');
    });
    Route::get('/kardex', [KardexController::class, 'index'])->name('kardex.index');
    Route::get('/kardex/{repuesto}', [KardexController::class, 'show'])->name('kardex.show');

    // ------------------------------
    // Categorías y proveedores
    // ------------------------------
    Route::resource('categorias', CategoriaController::class)->except(['show']);
    Route::resource('proveedores', ProveedorController::class)
        ->parameters(['proveedores' => 'proveedor'])
        ->except(['show']);

    // ------------------------------
    // Clientes
    // ------------------------------
    Route::get('/clientes/search', [ClienteController::class, 'search'])->name('clientes.search');
    Route::post('/clientes/quick', [ClienteController::class, 'storeQuick'])->name('clientes.storeQuick');
    Route::get('/clientes/buscar-dni/{dni}', [ClienteController::class, 'buscarDni'])
        ->where('dni', '[0-9]{8}')
        ->name('clientes.buscarDni');
    Route::get('/clientes/{id}/historial', [VentaController::class, 'historialCliente'])->name('clientes.historial');
    Route::resource('clientes', ClienteController::class)->except(['show']);

    // ------------------------------
    // Ventas
    // ------------------------------
    Route::prefix('ventas')->name('ventas.')->group(function () {
        Route::get('/{id}/boleta', [VentaController::class, 'generarBoleta'])->name('boleta');
        Route::get('/{id}/ticket', [VentaController::class, 'generarTicket'])->name('ticket');
        Route::get('/{id}/detalles', [VentaController::class, 'detalles'])->name('detalles');
        Route::patch('/{id}/estado', [VentaController::class, 'cambiarEstado'])->name('cambiar-estado');
        Route::post('/{id}/anular', [VentaController::class, 'anular'])->name('anular');
    });
    Route::resource('ventas', VentaController::class)->only(['index', 'create', 'store', 'show']);

    // ------------------------------
    // Tickets rápidos (venta escrita a mano, sin inventario)
    // ------------------------------
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('index');
        Route::get('/nuevo', [TicketController::class, 'create'])->name('create');
        Route::post('/', [TicketController::class, 'store'])->name('store');
        Route::get('/buscar-cliente', [TicketController::class, 'buscarCliente'])->name('buscar-cliente');
        Route::get('/{ticket}', [TicketController::class, 'show'])->name('show');
        Route::get('/{ticket}/imprimir', [TicketController::class, 'imprimir'])->name('imprimir');
        Route::get('/{ticket}/pdf', [TicketController::class, 'pdf'])->name('pdf');
        Route::post('/{ticket}/anular', [TicketController::class, 'anular'])->name('anular')->middleware('rol:admin,encargado');
    });

    // ------------------------------
    // Comprobantes electrónicos
    // ------------------------------
    Route::prefix('comprobantes')->name('comprobantes.')->group(function () {
        Route::get('/', [ComprobanteController::class, 'index'])->name('index');
        Route::post('/emitir/{venta}', [ComprobanteController::class, 'emitir'])->name('emitir');
        Route::get('/{comprobante}', [ComprobanteController::class, 'show'])->name('show');
        Route::get('/{comprobante}/xml', [ComprobanteController::class, 'descargarXml'])->name('descargar-xml');
        Route::get('/{comprobante}/cdr', [ComprobanteController::class, 'descargarCdr'])->name('descargar-cdr');
        Route::get('/{comprobante}/pdf', [ComprobanteController::class, 'descargarPdf'])->name('descargar-pdf');
        Route::get('/{comprobante}/ver-pdf', [ComprobanteController::class, 'verPdf'])->name('ver-pdf');
        Route::get('/{comprobante}/consultar-estado', [ComprobanteController::class, 'consultarEstado'])->name('consultar-estado');
        Route::post('/{comprobante}/anular', [ComprobanteController::class, 'anular'])->name('anular');
    });

    // ------------------------------
    // Finanzas y reportes
    // ------------------------------
    Route::get('/finanzas', [FinanzasController::class, 'index'])->name('finanzas.index');
    Route::post('/finanzas/gasto', [FinanzasController::class, 'storeGasto'])->name('finanzas.storeGasto');
    Route::post('/finanzas/ingreso', [FinanzasController::class, 'storeIngreso'])->name('finanzas.storeIngreso');
    Route::delete('/finanzas/ingreso/{id}/anular', [FinanzasController::class, 'anularIngreso'])->name('finanzas.anularIngreso');
    Route::delete('/finanzas/gasto/{id}/anular', [FinanzasController::class, 'anularGasto'])->name('finanzas.anularGasto');

    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');

    // ------------------------------
    // Solo administradores
    // ------------------------------
    Route::middleware('admin')->group(function () {
        Route::get('/empresa', [EmpresaController::class, 'index'])->name('empresa.index');
        Route::put('/empresa', [EmpresaController::class, 'update'])->name('empresa.update');

        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
    });
});
