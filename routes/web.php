<?php

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RepuestoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB; // Necesario para la ruta /test
use App\Http\Controllers\FinanzasController; //
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\EmpresaController;
// ==============================
// RUTAS PÚBLICAS (sin autenticación)
// ==============================

// Página inicial -> login
Route::get('/', function () {
    return redirect()->route('login.form');
});

// Registro
Route::get('/register', [UsuarioController::class, 'create'])->name('register');
Route::post('/register', [UsuarioController::class, 'store'])->name('register.store');

// Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [LoginController::class, 'login'])->name('login');

// ==============================
// RUTAS DE DEBUGGING (temporales)
// ==============================
Route::get('/healthz', fn () => response()->json(['ok' => true]));

Route::get('/test', function () {
    try {
        return response()->json([
            'status' => 'OK',
            'database' => DB::connection()->getPdo() ? 'Connected' : 'Failed',
            'cache' => config('cache.default'),
            'session' => config('session.driver'),
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'ERROR',
            'message' => $e->getMessage()
        ], 500);
    }
});

Route::get('/debug-log', function () {
    $logPath = storage_path('logs/laravel.log');
    if (file_exists($logPath)) {
        return nl2br(file_get_contents($logPath));
    }
    return 'No log file found';
});

// ==============================
// RUTAS PROTEGIDAS (requieren autenticación)
// ==============================
// Todo lo que esté aquí dentro forzará el login y activará el Multi-Tenant
Route::middleware(['auth'])->group(function () {

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // ==============================
    // RESOURCES (CRUD)
    // ==============================

    // Usuarios
    Route::resource('usuarios', UsuarioController::class);

    // Proveedores
    Route::resource('proveedores', ProveedorController::class)->parameters([
        'proveedores' => 'proveedor'
    ]);

    // Categorías
    Route::resource('categorias', CategoriaController::class);

    // Clientes ------------------------------
    Route::get('/clientes/search', [ClienteController::class, 'search'])->name('clientes.search');
Route::post('/clientes/storeQuick', [ClienteController::class, 'storeQuick'])->name('clientes.storeQuick');
    Route::resource('clientes', ClienteController::class);
 // Búsqueda de clientes (Autocompletado)
   
 //-------------------------------------------------------------------------
    // Repuestos
    Route::resource('repuestos', RepuestoController::class);

    // Ventas
    Route::resource('ventas', VentaController::class);

    // ==============================
    // RUTAS PERSONALIZADAS
    // ==============================

    // Repuestos - Gestión de cantidad
    Route::get('/repuestos/{id}/cantidad', [RepuestoController::class, 'cantidad'])
        ->name('repuestos.cantidad');
    Route::post('/repuestos/{id}/cantidad', [RepuestoController::class, 'updateCantidad'])
        ->name('repuestos.updateCantidad');

    // Repuestos - Búsqueda
    Route::get('/repuestos/search', [RepuestoController::class, 'search'])
        ->name('repuestos.search');

    Route::post('/repuestos/quick', [RepuestoController::class, 'storeQuick'])->name('repuestos.storeQuick');

    // Clientes - API RENIEC
    Route::get('/clientes/buscar-dni/{dni}', [ClienteController::class, 'buscarDni'])
        ->name('clientes.buscarDni');

    // Clientes - Búsqueda
    Route::get('/clientes/search', [ClienteController::class, 'search']);
    Route::get('/clientes/search', [ClienteController::class, 'search'])->name('clientes.search');
    Route::post('/clientes/storeQuick', [ClienteController::class, 'storeQuick'])->name('clientes.storeQuick');

    // Clientes - Historial
    Route::get('/clientes/{id}/historial', [VentaController::class, 'historialCliente'])
        ->name('clientes.historial');

    // Ventas - Funciones especiales
    Route::get('/ventas/success', [VentaController::class, 'success'])->name('ventas.success');
    Route::post('ventas/{id}/anular', [VentaController::class, 'anular'])->name('ventas.anular');

    Route::get('ventas/{id}/historial', [VentaController::class, 'historialCliente'])->name('ventas.historial');
    Route::get('ventas/{id}/detalles', [VentaController::class, 'detalles']);

    // Rutas específicas de ventas con nombres personalizados
    Route::prefix('ventas')->name('ventas.')->group(function () {
        // Boleta y Ticket PDF
        Route::get('/{id}/boleta', [VentaController::class, 'generarBoleta'])->name('boleta');
        Route::get('/{id}/ticket', [VentaController::class, 'generarTicket'])->name('ticket');
        
        // Detalles JSON para modal
        Route::get('/{id}/detalles', [VentaController::class, 'detalles'])->name('detalles');
        
        // Cambiar estado
        Route::patch('/{id}/estado', [VentaController::class, 'cambiarEstado'])->name('cambiar-estado');
    });

    // Exportar Categorías
    Route::get('/categorias/export', [CategoriaController::class, 'export'])->name('categorias.export');

    // ==============================
    // REDIRECCIÓN DESPUÉS DEL LOGIN
    // ==============================

    // Ruta principal del sistema (después de login)
    Route::get('/dashboard', function() {
        return redirect()->route('repuestos.index');
    })->name('dashboard');

     // ==============================
    // MÓDULO FINANZAS (NUEVO)
    // ==============================
    Route::get('/finanzas', [FinanzasController::class, 'index'])->name('finanzas.index');
    Route::post('/finanzas/gasto', [FinanzasController::class, 'storeGasto'])->name('finanzas.storeGasto');
    // Ruta para registrar otros ingresos
Route::post('/finanzas/ingreso', [FinanzasController::class, 'storeIngreso'])->name('finanzas.storeIngreso');

    //Modulo de Reportes
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');

    //Modulo Empresa, cambios app.blade
    Route::get('/empresa', [EmpresaController::class, 'index'])->name('empresa.index');
Route::put('/empresa', [EmpresaController::class, 'update'])->name('empresa.update');


// --- RUTAS AJAX PARA EL PUNTO DE VENTA ---

    // 1. Buscar clientes (Autocompletado)
    Route::get('/clientes/search', [ClienteController::class, 'search'])->name('clientes.search');

    // 2. Guardar cliente rápido (Modal)
    Route::post('/clientes/quick', [ClienteController::class, 'storeQuick'])->name('clientes.storeQuick');

}); // <-- Fin del grupo 'auth'