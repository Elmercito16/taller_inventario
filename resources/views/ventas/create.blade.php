@extends('layouts.app')

@section('title', 'Nueva Venta')
@section('page-title', 'Registrar Nueva Venta')
@section('page-description', 'Crea una nueva transacción de venta')

@section('breadcrumbs')
<nav class="flex items-center space-x-2 text-sm text-gray-500">
    <a href="{{ route('dashboard') }}" class="hover:text-[#218786] transition-colors flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/></svg>
        Dashboard
    </a>
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
    <a href="{{ route('ventas.index') }}" class="hover:text-[#218786] transition-colors">Ventas</a>
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
    <span class="font-medium text-gray-900">Nueva Venta</span>
</nav>
@endsection

@push('styles')
<style>
    .section-card {
        animation: slideUp 0.5s ease-out;
        animation-fill-mode: both;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .section-card:nth-child(1) { animation-delay: 0.1s; }
    .section-card:nth-child(2) { animation-delay: 0.2s; }
    .section-card:nth-child(3) { animation-delay: 0.3s; }
    .section-card:nth-child(4) { animation-delay: 0.4s; }
    
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .search-results {
        background: linear-gradient(to bottom, #ffffff, #f9fafb);
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        z-index: 100 !important; /* CRÍTICO: z-index alto */
    }
    
    .search-item:hover {
        background: linear-gradient(90deg, #f0fdfa 0%, #ecfdf5 100%);
        transform: translateX(4px);
    }
    
    .venta-item {
        transition: all 0.2s ease;
    }
    
    .venta-item:hover {
        background-color: #f8fafc;
        transform: scale(1.01);
    }
    
    .total-section {
        background: linear-gradient(135deg, #218786 0%, #1d7874 100%);
        color: white;
        box-shadow: 0 10px 15px -3px rgba(33, 135, 134, 0.2);
    }
    
    input:focus {
        border-color: #218786;
        ring-color: #218786;
    }
    
    /* Fix para z-index */
    .z-dropdown {
        position: relative;
        z-index: 50;
    }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="ventaManager()">
    <!-- Header con stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Productos Disponibles</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="repuestosDisponibles"></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Productos en Venta</p>
                    <p class="text-2xl font-bold text-[#218786]" x-text="items.length"></p>
                </div>
                <div class="p-3 bg-[#e6f7f6] rounded-full">
                    <svg class="w-5 h-5 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6m0 0L5.4 5M7 13h10M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Venta</p>
                    <p class="text-2xl font-bold text-green-600" x-text="'S/ ' + total.toFixed(2)"></p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('ventas.store') }}" method="POST" @submit.prevent="submitVenta" class="space-y-6">
        @csrf
        
        <!-- Sección Cliente - CON Z-INDEX ALTO -->
        <div class="section-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 z-dropdown">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Información del Cliente</h2>
                    <p class="text-sm text-gray-600">Busca o registra un cliente (opcional)</p>
                </div>
            </div>

            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input x-model="clienteSearch" 
                       @input="searchClientes"
                       @focus="showClientesList = true"
                       type="text" 
                       placeholder="Buscar por nombre o DNI del cliente..."
                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-colors duration-200"
                       autocomplete="off">
                
                <!-- Lista de resultados clientes - Z-INDEX 100 -->
                <div x-show="showClientesList && clienteResults.length > 0" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-cloak
                     class="absolute z-[100] mt-2 w-full search-results rounded-lg shadow-lg max-h-60 overflow-auto">
                    <template x-for="cliente in clienteResults" :key="cliente.id">
                        <div @click="selectCliente(cliente)" 
                             class="search-item px-4 py-3 cursor-pointer border-b border-gray-100 last:border-b-0 transition-all duration-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900" x-text="cliente.nombre"></p>
                                    <p class="text-sm text-gray-500">DNI: <span x-text="cliente.dni"></span></p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </template>
                </div>
                
                <input type="hidden" name="cliente_id" x-model="selectedCliente.id">
            </div>
            
            <!-- Cliente seleccionado -->
            <div x-show="selectedCliente.id" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-cloak
                 class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-green-900" x-text="selectedCliente.nombre"></p>
                            <p class="text-sm text-green-700">DNI: <span x-text="selectedCliente.dni"></span></p>
                        </div>
                    </div>
                    <button @click="clearCliente" type="button" 
                            class="text-green-600 hover:text-green-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sección Búsqueda de Productos - CON Z-INDEX MEDIO -->
        <div class="section-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 z-dropdown">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Buscar Repuestos</h2>
                    <p class="text-sm text-gray-600">Encuentra y agrega productos a la venta</p>
                </div>
            </div>

            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input x-model="repuestoSearch" 
                       @input="searchRepuestos"
                       @focus="showRepuestosList = true"
                       type="text" 
                       placeholder="Buscar por nombre, código o categoría..."
                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-colors duration-200"
                       autocomplete="off">
                
                <!-- Lista de resultados repuestos - Z-INDEX 90 -->
                <div x-show="showRepuestosList && repuestoResults.length > 0" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-cloak
                     class="absolute z-[90] mt-2 w-full search-results rounded-lg shadow-lg max-h-60 overflow-auto">
                    <template x-for="repuesto in repuestoResults" :key="repuesto.id">
                        <div @click="addRepuesto(repuesto)" 
                             class="search-item px-4 py-3 cursor-pointer border-b border-gray-100 last:border-b-0 transition-all duration-200">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900" x-text="repuesto.nombre"></p>
                                    <div class="flex items-center space-x-2 text-sm text-gray-500 mt-1">
                                        <span x-text="repuesto.categoria"></span>
                                        <span>•</span>
                                        <span>Stock: <span x-text="repuesto.stock" :class="repuesto.stock <= 5 ? 'text-red-500 font-medium' : ''"></span></span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-semibold text-green-600">S/ <span x-text="repuesto.precio_unitario.toFixed(2)"></span></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <!-- Estado sin resultados -->
                <div x-show="showRepuestosList && repuestoResults.length === 0 && repuestoSearch.length > 0" 
                     x-cloak
                     class="absolute z-[90] mt-2 w-full bg-white rounded-lg shadow-lg p-4 text-center text-gray-500">
                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.467-.881-6.077-2.33"/>
                    </svg>
                    <p>No se encontraron repuestos</p>
                </div>
            </div>
        </div>

        <!-- Sección Carrito de Compras - Z-INDEX BAJO -->
        <div class="section-card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative z-10">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6m0 0L5.4 5M7 13h10M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Productos en la Venta</h2>
                            <p class="text-sm text-gray-600">Revisa y ajusta las cantidades</p>
                        </div>
                    </div>
                    <button x-show="items.length > 0" @click="clearCarrito" type="button" x-cloak
                            class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors">
                        Limpiar todo
                    </button>
                </div>
            </div>

            <!-- Tabla de productos -->
            <div x-show="items.length > 0" x-cloak class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unit.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="(item, index) in items" :key="item.id">
                            <tr class="venta-item hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-[#e6f7f6] rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900" x-text="item.nombre"></p>
                                            <p class="text-xs text-gray-500" x-text="item.categoria"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    S/ <span x-text="item.precio.toFixed(2)"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <button @click="updateCantidad(item.id, item.cantidad - 1)" type="button"
                                                :disabled="item.cantidad <= 1"
                                                class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        <input type="number" 
                                               x-model.number="item.cantidad"
                                               @change="updateCantidad(item.id, $event.target.value)"
                                               :max="item.stock"
                                               min="1"
                                               class="w-16 text-center border border-gray-300 rounded-lg py-1 focus:ring-2 focus:ring-[#218786] focus:border-[#218786]">
                                        <button @click="updateCantidad(item.id, item.cantidad + 1)" type="button"
                                                :disabled="item.cantidad >= item.stock"
                                                class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm" 
                                          :class="item.stock <= 5 ? 'text-red-600 font-medium' : 'text-gray-900'"
                                          x-text="item.stock"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    S/ <span x-text="(item.precio * item.cantidad).toFixed(2)"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button @click="removeItem(item.id)" type="button"
                                            class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                                
                                <!-- Inputs ocultos para el formulario -->
                                <input type="hidden" :name="'repuestos[' + item.id + '][id]'" :value="item.id">
                                <input type="hidden" :name="'repuestos[' + item.id + '][cantidad]'" :value="item.cantidad">
                                <input type="hidden" :name="'repuestos[' + item.id + '][precio]'" :value="item.precio">
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Estado vacío -->
            <div x-show="items.length === 0" x-cloak class="p-12 text-center">
                <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6m0 0L5.4 5M7 13h10M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Carrito vacío</h3>
                <p class="text-gray-500">Busca y agrega productos para comenzar la venta</p>
            </div>
        </div>

        <!-- Sección Total y Finalizar -->
        <div class="section-card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="total-section px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white text-opacity-90 text-sm">Total de la Venta</p>
                        <p class="text-3xl font-bold text-white" x-text="'S/ ' + total.toFixed(2)"></p>
                    </div>
                    <div class="text-right text-white text-opacity-90">
                        <p class="text-sm" x-text="items.length + ' productos'"></p>
                        <p class="text-sm" x-text="totalCantidad + ' artículos'"></p>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <!-- Método de pago -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Método de Pago</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="relative">
                            <input type="radio" name="metodo_pago" value="efectivo" x-model="metodoPago" class="sr-only">
                            <div class="flex items-center p-4 border rounded-lg cursor-pointer transition-all duration-200"
                                 :class="metodoPago === 'efectivo' ? 'border-[#218786] bg-[#e6f7f6]' : 'border-gray-300 hover:border-gray-400'">
                                <svg class="w-6 h-6 mr-3" :class="metodoPago === 'efectivo' ? 'text-[#218786]' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <div>
                                    <p class="font-medium" :class="metodoPago === 'efectivo' ? 'text-[#218786]' : 'text-gray-900'">Efectivo</p>
                                    <p class="text-sm text-gray-500">Pago en efectivo</p>
                                </div>
                            </div>
                        </label>
                        
                        <label class="relative">
                            <input type="radio" name="metodo_pago" value="tarjeta" x-model="metodoPago" class="sr-only">
                            <div class="flex items-center p-4 border rounded-lg cursor-pointer transition-all duration-200"
                                 :class="metodoPago === 'tarjeta' ? 'border-[#218786] bg-[#e6f7f6]' : 'border-gray-300 hover:border-gray-400'">
                                <svg class="w-6 h-6 mr-3" :class="metodoPago === 'tarjeta' ? 'text-[#218786]' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                <div>
                                    <p class="font-medium" :class="metodoPago === 'tarjeta' ? 'text-[#218786]' : 'text-gray-900'">Tarjeta</p>
                                    <p class="text-sm text-gray-500">Débito/Crédito</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('ventas.index') }}" 
                       class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200 text-center inline-flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancelar
                    </a>
                    
                    <button type="submit" 
                            :disabled="items.length === 0 || isSubmitting"
                            :class="items.length === 0 || isSubmitting ? 'opacity-50 cursor-not-allowed' : 'hover:shadow-lg'"
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-medium rounded-lg transition-all duration-200 inline-flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed shadow-md">
                        <svg x-show="!isSubmitting" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg x-show="isSubmitting" x-cloak class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isSubmitting ? 'Procesando...' : 'Confirmar Venta'"></span>
                    </button>
                </div>
            </div>
            
            <input type="hidden" name="total" :value="total.toFixed(2)">
        </div>
    </form>
</div>

@php
    $repuestos_js = $repuestos->map(function($r){
        return [
            'id' => (int) $r->id,
            'nombre' => (string) $r->nombre,
            'categoria' => $r->categoria ? (string) $r->categoria->nombre : 'Sin categoría',
            'precio_unitario' => (float) ($r->precio_unitario ?? 0),
            'stock' => (int) ($r->cantidad ?? 0),
        ];
    })->values()->toArray();

    $clientes_js = $clientes->map(function($c){
        return [
            'id' => $c->id,
            'nombre' => $c->nombre,
            'dni' => $c->dni
        ];
    })->values()->toArray();
@endphp

@push('scripts')
<script>
function ventaManager() {
    return {
        repuestos: @json($repuestos_js),
        clientes: @json($clientes_js),
        
        items: [],
        selectedCliente: { id: '', nombre: '', dni: '' },
        metodoPago: 'efectivo',
        isSubmitting: false,
        
        clienteSearch: '',
        repuestoSearch: '',
        clienteResults: [],
        repuestoResults: [],
        showClientesList: false,
        showRepuestosList: false,
        
        get total() {
            return this.items.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
        },
        
        get totalCantidad() {
            return this.items.reduce((sum, item) => sum + item.cantidad, 0);
        },
        
        get repuestosDisponibles() {
            return this.repuestos.filter(r => r.stock > 0).length;
        },
        
        searchClientes() {
            if (!this.clienteSearch.trim()) {
                this.clienteResults = [];
                this.showClientesList = false;
                return;
            }
            
            const query = this.clienteSearch.toLowerCase();
            this.clienteResults = this.clientes.filter(cliente => 
                cliente.nombre.toLowerCase().includes(query) || 
                cliente.dni.toLowerCase().includes(query)
            ).slice(0, 10);
            
            this.showClientesList = true;
        },
        
        selectCliente(cliente) {
            this.selectedCliente = { ...cliente };
            this.clienteSearch = `${cliente.nombre} - ${cliente.dni}`;
            this.showClientesList = false;
            this.clienteResults = [];
        },
        
        clearCliente() {
            this.selectedCliente = { id: '', nombre: '', dni: '' };
            this.clienteSearch = '';
            this.clienteResults = [];
        },
        
        searchRepuestos() {
            if (!this.repuestoSearch.trim()) {
                this.repuestoResults = [];
                this.showRepuestosList = false;
                return;
            }
            
            const query = this.repuestoSearch.toLowerCase();
            this.repuestoResults = this.repuestos.filter(repuesto => 
                (repuesto.nombre.toLowerCase().includes(query) || 
                 repuesto.categoria.toLowerCase().includes(query)) &&
                repuesto.stock > 0
            ).slice(0, 10);
            
            this.showRepuestosList = true;
        },
        
        addRepuesto(repuesto) {
            const existingItem = this.items.find(item => item.id === repuesto.id);
            if (existingItem) {
                this.showNotification('Este producto ya está en la venta', 'warning');
                return;
            }
            
            if (repuesto.stock <= 0) {
                this.showNotification('Este producto no tiene stock disponible', 'error');
                return;
            }
            
            this.items.push({
                id: repuesto.id,
                nombre: repuesto.nombre,
                categoria: repuesto.categoria,
                precio: repuesto.precio_unitario,
                stock: repuesto.stock,
                cantidad: 1
            });
            
            this.repuestoSearch = '';
            this.repuestoResults = [];
            this.showRepuestosList = false;
            
            this.showNotification(`${repuesto.nombre} agregado a la venta`, 'success');
        },
        
        updateCantidad(itemId, nuevaCantidad) {
            const item = this.items.find(i => i.id === itemId);
            if (!item) return;
            
            nuevaCantidad = parseInt(nuevaCantidad);
            
            if (nuevaCantidad < 1) nuevaCantidad = 1;
            if (nuevaCantidad > item.stock) {
                nuevaCantidad = item.stock;
                this.showNotification(`Stock máximo: ${item.stock} unidades`, 'warning');
            }
            
            item.cantidad = nuevaCantidad;
        },
        
        removeItem(itemId) {
            const itemIndex = this.items.findIndex(i => i.id === itemId);
            if (itemIndex > -1) {
                const item = this.items[itemIndex];
                this.items.splice(itemIndex, 1);
                this.showNotification(`${item.nombre} eliminado de la venta`, 'info');
            }
        },
        
        clearCarrito() {
            Swal.fire({
                title: '¿Limpiar carrito?',
                text: 'Se eliminarán todos los productos de la venta',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, limpiar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.items = [];
                    this.showNotification('Carrito limpiado', 'info');
                }
            });
        },
        
        async submitVenta(event) {
            if (this.items.length === 0) {
                this.showNotification('Agrega al menos un producto a la venta', 'error');
                return;
            }
            
            const result = await Swal.fire({
                title: '¿Confirmar venta?',
                html: this.getVentaResumen(),
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'Revisar',
                confirmButtonColor: '#218786',
                cancelButtonColor: '#6b7280',
                reverseButtons: true
            });
            
            if (!result.isConfirmed) return;
            
            this.isSubmitting = true;
            
            Swal.fire({
                title: 'Procesando venta...',
                html: 'Por favor espera mientras registramos la transacción',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            event.target.submit();
        },
        
        getVentaResumen() {
            const clienteInfo = this.selectedCliente.id ? 
                `<p><strong>Cliente:</strong> ${this.selectedCliente.nombre}</p>` : 
                '<p><strong>Cliente:</strong> Venta general</p>';
                
            return `
                <div class="text-left space-y-2">
                    ${clienteInfo}
                    <p><strong>Productos:</strong> ${this.items.length}</p>
                    <p><strong>Cantidad total:</strong> ${this.totalCantidad} artículos</p>
                    <p><strong>Método de pago:</strong> ${this.metodoPago}</p>
                    <p class="text-xl font-bold text-green-600">Total: S/ ${this.total.toFixed(2)}</p>
                </div>
            `;
        },
        
        showNotification(message, type = 'info') {
            const config = {
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                text: message
            };
            
            switch (type) {
                case 'success':
                    config.icon = 'success';
                    config.background = '#f0fdf4';
                    config.color = '#166534';
                    break;
                case 'error':
                    config.icon = 'error';
                    config.background = '#fef2f2';
                    config.color = '#dc2626';
                    break;
                case 'warning':
                    config.icon = 'warning';
                    config.background = '#fffbeb';
                    config.color = '#d97706';
                    break;
                default:
                    config.icon = 'info';
                    config.background = '#f0f9ff';
                    config.color = '#1e40af';
            }
            
            Swal.fire(config);
        },
        
        init() {
            document.addEventListener('click', (e) => {
                if (!this.$el.contains(e.target)) {
                    this.showClientesList = false;
                    this.showRepuestosList = false;
                }
            });
            
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.showClientesList = false;
                    this.showRepuestosList = false;
                }
                
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter' && this.items.length > 0) {
                    e.preventDefault();
                    this.$el.querySelector('form').dispatchEvent(new Event('submit', { cancelable: true }));
                }
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const repuestoInput = document.querySelector('input[x-model="repuestoSearch"]');
        if (repuestoInput) repuestoInput.focus();
    }, 500);
    
    window.addEventListener('beforeunload', function(e) {
        const ventaData = Alpine.$data(document.querySelector('[x-data]'));
        if (ventaData && ventaData.items.length > 0) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
});
</script>
@endpush

@endsection