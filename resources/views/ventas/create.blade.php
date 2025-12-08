@extends('layouts.app')

@section('title', 'Nueva Venta')
@section('page-title', 'Nueva Venta')
@section('page-description', 'Registra una nueva transacción de venta')

@push('styles')
<style>
    .fade-in {
        animation: fadeIn 0.5s ease-out both;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .fade-in:nth-child(1) { animation-delay: 0s; }
    .fade-in:nth-child(2) { animation-delay: 0.1s; }
    .fade-in:nth-child(3) { animation-delay: 0.2s; }
    
    .metodo-pago-card {
        transition: all 0.3s ease;
    }
    
    .metodo-pago-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(33, 135, 134, 0.15);
    }
    
    .metodo-pago-card.selected {
        border-color: #218786 !important;
        background: linear-gradient(135deg, #e6f7f6 0%, #d4f1ef 100%) !important;
        transform: scale(1.02);
    }
    
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="ventaManager()">

    <!-- Breadcrumbs -->
    <nav class="flex items-center space-x-2 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-[#218786] transition-colors flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <a href="{{ route('ventas.index') }}" class="hover:text-[#218786] transition-colors">Ventas</a>
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <span class="font-medium text-gray-900">Nueva Venta</span>
    </nav>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Productos Disponibles -->
        <div class="fade-in bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Productos Disponibles</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $repuestos->where('cantidad', '>', 0)->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">En inventario</p>
        </div>

        <!-- Productos en Venta -->
        <div class="fade-in bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Productos en Venta</p>
            <p class="text-3xl font-bold text-purple-600 mt-1" x-text="carritoItems.length">0</p>
            <p class="text-xs text-gray-500 mt-1">En carrito</p>
        </div>

        <!-- Total -->
        <div class="fade-in bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total</p>
            <p class="text-3xl font-bold text-green-600 mt-1" x-text="'S/ ' + calcularTotal().toFixed(2)">S/ 0.00</p>
            <p class="text-xs text-gray-500 mt-1">A cobrar</p>
        </div>
    </div>

    <form id="ventaForm" action="{{ route('ventas.store') }}" method="POST" class="space-y-6" @submit.prevent="confirmarVenta">
        @csrf
        
        <!-- Cliente -->
        <div class="fade-in bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Cliente</h2>
                        <p class="text-sm text-gray-600">Selecciona el cliente para esta venta</p>
                    </div>
                </div>
                <button type="button" 
                        @click="abrirModalClientes()"
                        class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-bold rounded-lg hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Buscar Cliente
                </button>
            </div>

            <input type="hidden" name="cliente_id" x-model="selectedCliente?.id">

            <!-- Cliente Seleccionado -->
            <div x-show="selectedCliente" 
                 x-cloak
                 x-transition
                 class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center">
                        <div class="w-14 h-14 bg-green-500 rounded-full flex items-center justify-center mr-4">
                            <span class="text-white text-xl font-bold" x-text="selectedCliente?.nombre?.charAt(0) || '?'">?</span>
                        </div>
                        <div>
                            <p class="font-bold text-green-900 text-lg" x-text="selectedCliente?.nombre">-</p>
                            <p class="text-sm text-green-700">
                                DNI: <span class="font-semibold" x-text="selectedCliente?.dni">-</span>
                            </p>
                        </div>
                    </div>
                    <button type="button" 
                            @click="cambiarCliente()"
                            class="inline-flex items-center px-4 py-2 border-2 border-green-500 text-green-700 font-bold rounded-lg hover:bg-green-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Cambiar
                    </button>
                </div>
            </div>

            <!-- Sin Cliente -->
            <div x-show="!selectedCliente" 
                 x-cloak
                 class="p-12 text-center border-2 border-dashed border-gray-300 rounded-xl hover:border-gray-400 transition-colors">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <p class="text-gray-500 font-medium">No hay cliente seleccionado</p>
                <p class="text-sm text-gray-400 mt-1">Haz clic en "Buscar Cliente" para continuar</p>
            </div>
        </div>

        <!-- Productos -->
        <div class="fade-in bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Productos</h2>
                        <p class="text-sm text-gray-600">Agrega productos a la venta</p>
                    </div>
                </div>
                <button type="button" 
                        @click="abrirModalRepuestos()"
                        class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-bold rounded-lg hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Agregar Producto
                </button>
            </div>
        </div>

        <!-- Carrito -->
        <div class="fade-in bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-gray-50 to-gray-100 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center mr-3 shadow-sm">
                        <svg class="w-6 h-6 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Carrito de Compras</h2>
                        <p class="text-sm text-gray-600" x-text="`${carritoItems.length} productos agregados`">0 productos agregados</p>
                    </div>
                </div>
                <button type="button" 
                        x-show="carritoItems.length > 0"
                        @click="limpiarCarrito()"
                        class="inline-flex items-center px-4 py-2 text-red-600 bg-red-50 border border-red-200 font-bold rounded-lg hover:bg-red-100 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Limpiar Carrito
                </button>
            </div>

            <!-- Tabla de productos -->
            <div x-show="carritoItems.length > 0" class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Producto</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Precio</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Cantidad</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Subtotal</th>
                            <th class="px-6 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <template x-for="(item, index) in carritoItems" :key="item.id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-[#218786] to-[#1a6d6c] rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900" x-text="item.nombre"></p>
                                            <p class="text-xs text-gray-500">Stock: <span x-text="item.stock"></span></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-gray-900" x-text="'S/ ' + item.precio.toFixed(2)"></span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" 
                                                @click="updateCantidad(item.id, item.cantidad - 1)"
                                                :disabled="item.cantidad <= 1"
                                                class="w-8 h-8 rounded-lg bg-gray-200 hover:bg-[#218786] hover:text-white disabled:opacity-50 disabled:cursor-not-allowed font-bold transition-colors">
                                            -
                                        </button>
                                        
                                        <!-- 👇 INPUT EDITABLE -->
                                        <input type="number"
                                               :value="item.cantidad"
                                               @input="updateCantidad(item.id, parseInt($event.target.value) || 1)"
                                               @blur="validarCantidad(item.id, $event.target.value, item.stock)"
                                               min="1"
                                               :max="item.stock"
                                               class="w-16 text-center font-bold text-gray-900 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] py-1.5 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                        
                                        <button type="button" 
                                                @click="updateCantidad(item.id, item.cantidad + 1)"
                                                :disabled="item.cantidad >= item.stock"
                                                class="w-8 h-8 rounded-lg bg-gray-200 hover:bg-[#218786] hover:text-white disabled:opacity-50 disabled:cursor-not-allowed font-bold transition-colors">
                                            +
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-bold text-green-600 text-lg" x-text="'S/ ' + (item.precio * item.cantidad).toFixed(2)"></span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button type="button" 
                                            @click="removeItem(item.id)"
                                            class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                                <!-- Hidden inputs para el formulario -->
                                <td class="hidden">
                                    <input type="hidden" :name="'repuestos[' + index + '][id]'" :value="item.id">
                                    <input type="hidden" :name="'repuestos[' + index + '][cantidad]'" :value="item.cantidad">
                                    <input type="hidden" :name="'repuestos[' + index + '][precio]'" :value="item.precio">
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            
            <!-- Carrito Vacío -->
            <div x-show="carritoItems.length === 0" 
                 x-cloak
                 class="p-16 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Carrito vacío</h3>
                <p class="text-gray-500">Agrega productos para comenzar la venta</p>
            </div>
        </div>

        <!-- Total y Método de Pago -->
        <div class="fade-in bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Header con Total -->
            <div class="bg-gradient-to-r from-[#218786] to-[#1a6d6c] p-6 text-white">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Total a Pagar</p>
                        <p class="text-4xl font-bold" x-text="'S/ ' + calcularTotal().toFixed(2)">S/ 0.00</p>
                    </div>
                    <div class="text-right text-sm opacity-90 space-y-1">
                        <p><span class="font-bold" x-text="carritoItems.length">0</span> productos</p>
                        <p><span class="font-bold" x-text="calcularCantidadTotal()">0</span> artículos</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Método de Pago -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Método de Pago *
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" 
                                   name="metodo_pago" 
                                   value="efectivo" 
                                   x-model="metodoPago"
                                   class="sr-only" 
                                   required>
                            <div class="metodo-pago-card p-6 border-2 rounded-xl text-center transition-all"
                                 :class="metodoPago === 'efectivo' ? 'selected' : 'border-gray-300'">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <span class="font-bold text-gray-900">Efectivo</span>
                                <p class="text-xs text-gray-500 mt-1">Pago en efectivo</p>
                            </div>
                        </label>
                        
                        <label class="cursor-pointer">
                            <input type="radio" 
                                   name="metodo_pago" 
                                   value="tarjeta" 
                                   x-model="metodoPago"
                                   class="sr-only" 
                                   required>
                            <div class="metodo-pago-card p-6 border-2 rounded-xl text-center transition-all"
                                 :class="metodoPago === 'tarjeta' ? 'selected' : 'border-gray-300'">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                </div>
                                <span class="font-bold text-gray-900">Tarjeta</span>
                                <p class="text-xs text-gray-500 mt-1">Débito o crédito</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('ventas.index') }}" 
                       class="flex-1 px-6 py-4 border-2 border-gray-300 text-gray-700 font-bold rounded-lg text-center hover:bg-gray-50 transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancelar
                    </a>
                    
                    <button type="submit" 
                            :disabled="!puedeConfirmar()"
                            class="flex-1 px-6 py-4 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-bold rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-xl transition-all flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span x-text="procesando ? 'Procesando...' : 'Confirmar Venta'">Confirmar Venta</span>
                    </button>
                </div>
            </div>
            
            <input type="hidden" name="total" :value="calcularTotal().toFixed(2)">
        </div>
    </form>

    <!-- Modales -->
    @include('ventas.modals.buscar-cliente')
    @include('ventas.modals.buscar-repuesto')
    @include('ventas.modals.nuevo-cliente')
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('ventaManager', () => ({
        carritoItems: [],
        selectedCliente: null,
        metodoPago: '',
        procesando: false,
        
        calcularTotal() {
            return this.carritoItems.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
        },
        
        calcularCantidadTotal() {
            return this.carritoItems.reduce((sum, item) => sum + item.cantidad, 0);
        },
        
        puedeConfirmar() {
            return this.carritoItems.length > 0 && this.selectedCliente && this.metodoPago && !this.procesando;
        },
        
        abrirModalClientes() {
            if (window.abrirModalClientes) window.abrirModalClientes();
        },
        
        abrirModalRepuestos() {
            if (window.abrirModalRepuestos) window.abrirModalRepuestos();
        },
        
        selectCliente(cliente) {
            this.selectedCliente = cliente;
            Swal.fire({
                icon: 'success',
                title: 'Cliente seleccionado',
                text: cliente.nombre,
                toast: true,
                position: 'top-end',
                timer: 2000,
                showConfirmButton: false
            });
        },
        
        cambiarCliente() {
            this.selectedCliente = null;
            this.abrirModalClientes();
        },
        
        addRepuesto(repuesto) {
            if (this.carritoItems.find(item => item.id === repuesto.id)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Producto ya agregado',
                    text: 'Este producto ya está en el carrito',
                    toast: true,
                    position: 'top-end',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
            
            this.carritoItems.push({
                id: repuesto.id,
                nombre: repuesto.nombre,
                precio: parseFloat(repuesto.precio_unitario),
                stock: repuesto.stock,
                cantidad: 1
            });
            
            Swal.fire({
                icon: 'success',
                title: 'Producto agregado',
                text: repuesto.nombre,
                toast: true,
                position: 'top-end',
                timer: 1500,
                showConfirmButton: false
            });
        },
        
        updateCantidad(itemId, nuevaCantidad) {
            const item = this.carritoItems.find(i => i.id === itemId);
            if (!item) return;
            
            if (nuevaCantidad < 1) nuevaCantidad = 1;
            if (nuevaCantidad > item.stock) {
                nuevaCantidad = item.stock;
                Swal.fire({
                    icon: 'warning',
                    title: 'Stock máximo alcanzado',
                    text: `Solo hay ${item.stock} unidades disponibles`,
                    toast: true,
                    position: 'top-end',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
            item.cantidad = nuevaCantidad;
        },
        
        // 👇 NUEVA FUNCIÓN PARA VALIDAR
        validarCantidad(itemId, valor, stockMax) {
            const item = this.carritoItems.find(i => i.id === itemId);
            if (!item) return;
            
            let cantidad = parseInt(valor);
            
            // Si no es un número válido, regresar a 1
            if (isNaN(cantidad) || cantidad < 1) {
                item.cantidad = 1;
                Swal.fire({
                    icon: 'warning',
                    title: 'Cantidad inválida',
                    text: 'Se estableció la cantidad mínima (1)',
                    toast: true,
                    position: 'top-end',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
            
            // Si excede el stock
            if (cantidad > stockMax) {
                item.cantidad = stockMax;
                Swal.fire({
                    icon: 'warning',
                    title: 'Stock insuficiente',
                    text: `Solo hay ${stockMax} unidades disponibles`,
                    toast: true,
                    position: 'top-end',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        },
        
        removeItem(itemId) {
            this.carritoItems = this.carritoItems.filter(i => i.id !== itemId);
            Swal.fire({
                icon: 'info',
                title: 'Producto eliminado',
                toast: true,
                position: 'top-end',
                timer: 1500,
                showConfirmButton: false
            });
        },
        
        limpiarCarrito() {
            Swal.fire({
                title: '¿Limpiar carrito?',
                text: 'Se eliminarán todos los productos',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Sí, limpiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.carritoItems = [];
                    Swal.fire({
                        icon: 'success',
                        title: 'Carrito limpiado',
                        toast: true,
                        position: 'top-end',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        },
        
        async confirmarVenta(e) {
            if (!this.puedeConfirmar()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Datos incompletos',
                    text: 'Por favor completa todos los campos requeridos'
                });
                return;
            }
            
            const result = await Swal.fire({
                title: '¿Confirmar venta?',
                html: `
                    <div class="text-left space-y-2">
                        <p><strong>Cliente:</strong> ${this.selectedCliente.nombre}</p>
                        <p><strong>Productos:</strong> ${this.carritoItems.length}</p>
                        <p><strong>Total:</strong> <span class="text-green-600 font-bold">S/ ${this.calcularTotal().toFixed(2)}</span></p>
                        <p><strong>Método:</strong> ${this.metodoPago === 'efectivo' ? 'Efectivo' : 'Tarjeta'}</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#218786',
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'Revisar'
            });
            
            if (result.isConfirmed) {
                this.procesando = true;
                e.target.submit();
            }
        },
        
        init() {
            // Exponer funciones globalmente para los modales
            window.selectCliente = (cliente) => this.selectCliente(cliente);
            window.addRepuesto = (repuesto) => this.addRepuesto(repuesto);
        }
    }));
});
</script>
@endpush
@endsection
