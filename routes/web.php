<?php

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RepuestoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;


// Redirección raíz
Route::get('/', function() {
    return redirect()->route('repuestos.index');
});

// Rutas de usuarios
Route::resource('usuarios', UsuarioController::class);

// Rutas de proveedores
Route::resource('proveedores', ProveedorController::class);

// Rutas de repuestos
Route::resource('repuestos', RepuestoController::class);

// Formulario para agregar cantidad
Route::get('/repuestos/{id}/cantidad', [RepuestoController::class, 'cantidad'])
    ->name('repuestos.cantidad');

// Actualizar cantidad (sumar stock)
Route::post('/repuestos/{id}/cantidad', [RepuestoController::class, 'updateCantidad'])
    ->name('repuestos.updateCantidad');

// Rutas de categorías
Route::resource('categorias', CategoriaController::class);

// Rutas de clientes
Route::resource('clientes', ClienteController::class);

// Rutas de ventas
Route::resource('ventas', VentaController::class);
Route::get('/ventas/success', [VentaController::class, 'success'])->name('ventas.success');

//Anular venta
Route::resource('ventas', VentaController::class);

// Ruta personalizada para anular venta
Route::post('ventas/{venta}/anular', [VentaController::class, 'anular'])->name('ventas.anular');

//Historial en vista Cliente
Route::get('/clientes/{id}/historial', [ClienteController::class, 'historial'])
    ->name('clientes.historial');

Route::get('/clientes/{id}/historial', [VentaController::class, 'historialCliente'])
    ->name('clientes.historial');

//API RENIEC


Route::get('/clientes/buscar-dni/{dni}', [ClienteController::class, 'buscarDni'])->name('clientes.buscarDni');

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

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

//Ruta para buscar clientes

// web.php
Route::get('/clientes/search', [ClienteController::class, 'search']);

