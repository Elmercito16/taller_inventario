@extends('layouts.app')

@section('title', 'Clientes')
@section('page-title', 'Gestión de Clientes')
@section('page-description', 'Administra tu cartera de clientes')

@push('styles')
<style>
    .card-fade {
        animation: slideUp 0.5s ease-out both;
    }
    
    .card-fade:nth-child(n) {
        animation-delay: calc(0.03s * var(--index));
    }
    
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .stat-card {
        animation: slideUp 0.6s ease-out both;
    }
    
    .stat-card:nth-child(1) { animation-delay: 0s; }
    .stat-card:nth-child(2) { animation-delay: 0.1s; }
    .stat-card:nth-child(3) { animation-delay: 0.2s; }
    .stat-card:nth-child(4) { animation-delay: 0.3s; }
    
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="clientesManager()">

    <!-- Stats Dashboard -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Clientes -->
        <div class="stat-card bg-gradient-to-br from-[#218786] to-[#1a6d6c] rounded-xl shadow-lg p-6 text-white hover:shadow-xl transition-all duration-300 hover:scale-105 relative overflow-hidden">
    <!-- Decoración de fondo -->
    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full"></div>
    <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white/5 rounded-full"></div>
    
    <!-- Contenido -->
    <div class="relative z-10">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm shadow-lg ring-2 ring-white/30">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-sm font-semibold text-white/90 uppercase tracking-wide">Total Clientes</p>
        <p class="text-4xl font-bold mt-2">{{ $clientes->count() }}</p>
        <div class="mt-4 flex items-center text-xs text-white/80">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            <span>Activos en el sistema</span>
        </div>
    </div>
</div>

        
        <!-- Con Email -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600">Con Email</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">
                {{ $clientes->filter(fn($c) => !empty($c->email))->count() }}
            </p>
        </div>
        
        <!-- Con Teléfono -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600">Con Teléfono</p>
            <p class="text-3xl font-bold text-green-600 mt-1">
                {{ $clientes->filter(fn($c) => !empty($c->telefono))->count() }}
            </p>
        </div>
        
        <!-- Registrados Hoy -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600">Registrados Hoy</p>
            <p class="text-3xl font-bold text-purple-600 mt-1">
                {{ $clientes->filter(fn($c) => $c->created_at->isToday())->count() }}
            </p>
        </div>
    </div>

    <!-- Barra de Búsqueda y Filtros -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            
            <!-- Búsqueda y Filtros -->
            <div class="flex flex-col sm:flex-row gap-3 flex-1">
                <div class="relative flex-1 max-w-md">
                    <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input x-model="search" 
                           type="text" 
                           placeholder="Buscar por nombre o DNI..."
                           class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all">
                </div>
                
                <select x-model="filter" 
                        class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] font-medium">
                    <option value="todos">📋 Todos</option>
                    <option value="con_email">📧 Con Email</option>
                    <option value="con_telefono">📱 Con Teléfono</option>
                    <option value="sin_contacto">⚠️ Sin Contacto</option>
                </select>
                
                <button @click="resetFilters()" 
                        class="px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Limpiar
                </button>
            </div>

            <!-- Botones de Acción -->
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('clientes.create') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-bold rounded-lg hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nuevo Cliente
                </a>
                
                <button onclick="exportarClientes()" 
                        class="inline-flex items-center justify-center px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-50 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Exportar
                </button>
            </div>
        </div>
        
        <!-- Contador de resultados -->
        <div x-show="filteredCount >= 0" class="mt-4 pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-600">
                Mostrando <strong x-text="filteredCount"></strong> de <strong>{{ $clientes->count() }}</strong> clientes
            </p>
        </div>
    </div>

    <!-- Lista de Clientes -->
    <div class="space-y-4">
        @forelse ($clientes as $index => $cliente)
            <div class="card-fade bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all overflow-hidden"
                 style="--index: {{ $index }}"
                 x-show="filterCliente({{ json_encode([
                     'nombre' => strtolower($cliente->nombre),
                     'dni' => $cliente->dni,
                     'email' => (bool)$cliente->email,
                     'telefono' => (bool)$cliente->telefono
                 ]) }})"
                 x-data="{ open: false }">
                
                <!-- Header -->
                <div class="p-5 cursor-pointer hover:bg-gray-50 transition-colors" @click="open = !open">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4 flex-1 min-w-0">
                            <div class="w-14 h-14 bg-gradient-to-br from-[#218786] to-[#1a6d6c] rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                <span class="text-white text-xl font-bold">
                                    {{ strtoupper(substr($cliente->nombre, 0, 1)) }}
                                </span>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-gray-900 truncate">{{ $cliente->nombre }}</h3>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-600 mt-1">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 011-1h2a2 2 0 011 1v2m-4 0a2 2 0 01-1 1h-2a2 2 0 01-1-1"/>
                                        </svg>
                                        {{ $cliente->dni }}
                                    </span>
                                    <span class="hidden sm:flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $cliente->created_at->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2 flex-shrink-0">
                            @if($cliente->email)
                            <span class="hidden lg:inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-100 text-blue-700">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Email
                            </span>
                            @endif
                            
                            @if($cliente->telefono)
                            <span class="hidden lg:inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-green-100 text-green-700">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                Tel
                            </span>
                            @endif
                            
                            <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="w-5 h-5 text-gray-400 transition-transform duration-300" 
                                     :class="{ 'rotate-180': open }"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Detalles Expandibles -->
                <div x-show="open" 
                     x-collapse
                     class="border-t border-gray-100">
                    <div class="p-6 bg-gradient-to-br from-gray-50 to-white">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
                            <!-- Contacto -->
                            <div class="bg-white rounded-xl p-5 border-2 border-gray-100 hover:border-[#218786] transition-colors">
                                <h4 class="font-bold text-gray-900 mb-4 flex items-center">
                                    <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    Información de Contacto
                                </h4>
                                <div class="space-y-4">
                                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-gray-500 mb-1">Email</p>
                                            <p class="font-semibold text-gray-900 truncate">{{ $cliente->email ?: 'No registrado' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <div class="flex-1">
                                            <p class="text-xs text-gray-500 mb-1">Teléfono</p>
                                            <p class="font-semibold text-gray-900">{{ $cliente->telefono ?: 'No registrado' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                        <svg class="w-5 h-5 text-purple-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <div class="flex-1">
                                            <p class="text-xs text-gray-500 mb-1">Dirección</p>
                                            <p class="font-semibold text-gray-900">{{ $cliente->direccion ?: 'No registrada' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sistema -->
                            <div class="bg-white rounded-xl p-5 border-2 border-gray-100 hover:border-[#218786] transition-colors">
                                <h4 class="font-bold text-gray-900 mb-4 flex items-center">
                                    <div class="p-2 bg-purple-100 rounded-lg mr-3">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                    </div>
                                    Estadísticas
                                </h4>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-[#218786]/10 to-[#218786]/5 rounded-lg">
                                        <span class="text-sm text-gray-600">Ventas Registradas</span>
                                        <span class="text-2xl font-bold text-[#218786]">{{ $cliente->ventas_count ?? 0 }}</span>
                                    </div>
                                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                        <svg class="w-5 h-5 text-gray-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <div class="flex-1">
                                            <p class="text-xs text-gray-500 mb-1">Registrado</p>
                                            <p class="font-semibold text-gray-900">{{ $cliente->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                        <svg class="w-5 h-5 text-gray-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        <div class="flex-1">
                                            <p class="text-xs text-gray-500 mb-1">Actualizado</p>
                                            <p class="font-semibold text-gray-900">{{ $cliente->updated_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('clientes.edit', $cliente) }}" 
                               class="flex-1 sm:flex-none inline-flex items-center justify-center px-5 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg transition-all shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Editar
                            </a>

                            @if(Route::has('clientes.historial'))
                            <a href="{{ route('clientes.historial', $cliente) }}" 
                               class="flex-1 sm:flex-none inline-flex items-center justify-center px-5 py-3 bg-indigo-500 hover:bg-indigo-600 text-white font-bold rounded-lg transition-all shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                Historial
                            </a>
                            @endif

                            <button onclick="contactarCliente('{{ $cliente->telefono }}', '{{ $cliente->email }}')" 
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-5 py-3 bg-green-500 hover:bg-green-600 text-white font-bold rounded-lg transition-all shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                Contactar
                            </button>

                            <button onclick="confirmarEliminacion('{{ route('clientes.destroy', $cliente) }}', '{{ addslashes($cliente->nombre) }}')" 
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-5 py-3 bg-red-500 hover:bg-red-600 text-white font-bold rounded-lg transition-all shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <!-- Estado Vacío -->
            <div class="text-center py-20 bg-white rounded-xl shadow-sm border-2 border-dashed border-gray-300">
                <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">No hay clientes registrados</h3>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">
                    Comienza a construir tu cartera de clientes registrando el primero
                </p>
                <a href="{{ route('clientes.create') }}" 
                   class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-bold rounded-xl hover:shadow-xl transform hover:-translate-y-1 transition-all">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Registrar Primer Cliente
                </a>
            </div>
        @endforelse
    </div>

    <!-- Sin resultados -->
    <div x-show="filteredCount === 0" x-cloak class="text-center py-16 bg-white rounded-xl border-2 border-dashed border-gray-300">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <h3 class="text-xl font-bold text-gray-900 mb-2">No se encontraron resultados</h3>
        <p class="text-gray-600 mb-6">Intenta con otros términos de búsqueda o filtros</p>
        <button @click="resetFilters()" 
                class="inline-flex items-center px-6 py-3 bg-[#218786] text-white font-bold rounded-lg hover:bg-[#1a6d6c] transition-colors">
            Limpiar filtros
        </button>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('clientesManager', () => ({
        search: '',
        filter: 'todos',
        filteredCount: {{ $clientes->count() }},
        
        filterCliente(cliente) {
            const searchLower = this.search.toLowerCase();
            const matchesSearch = !this.search || 
                cliente.nombre.includes(searchLower) || 
                cliente.dni.includes(this.search);
            
            let matchesFilter = true;
            switch(this.filter) {
                case 'con_email':
                    matchesFilter = cliente.email;
                    break;
                case 'con_telefono':
                    matchesFilter = cliente.telefono;
                    break;
                case 'sin_contacto':
                    matchesFilter = !cliente.email || !cliente.telefono;
                    break;
            }
            
            const shouldShow = matchesSearch && matchesFilter;
            
            this.$nextTick(() => {
                this.updateFilteredCount();
            });
            
            return shouldShow;
        },
        
        updateFilteredCount() {
            const visibleCards = document.querySelectorAll('.card-fade[x-show]:not([style*="display: none"])');
            this.filteredCount = visibleCards.length;
        },
        
        resetFilters() {
            this.search = '';
            this.filter = 'todos';
            this.updateFilteredCount();
        },
        
        init() {
            this.$watch('search', () => this.updateFilteredCount());
            this.$watch('filter', () => this.updateFilteredCount());
        }
    }));
});

function contactarCliente(telefono, email) {
    const opciones = [];
    
    if (telefono) {
        opciones.push({
            icon: '📞',
            text: `Llamar: ${telefono}`,
            action: () => window.open(`tel:${telefono}`),
            color: 'blue'
        });
        opciones.push({
            icon: '💬',
            text: `WhatsApp: ${telefono}`,
            action: () => window.open(`https://wa.me/51${telefono.replace(/\D/g, '')}`),
            color: 'green'
        });
    }
    
    if (email) {
        opciones.push({
            icon: '✉️',
            text: `Email: ${email}`,
            action: () => window.open(`mailto:${email}`),
            color: 'purple'
        });
    }
    
    if (opciones.length === 0) {
        Swal.fire({
            icon: 'info',
            title: 'Sin información de contacto',
            text: 'Este cliente no tiene datos de contacto registrados',
            confirmButtonColor: '#218786'
        });
        return;
    }
    
    if (opciones.length === 1) {
        opciones[0].action();
        return;
    }
    
    const buttonsHtml = opciones.map((op, i) => {
        const colors = {
            blue: 'from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700',
            green: 'from-green-500 to-green-600 hover:from-green-600 hover:to-green-700',
            purple: 'from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700'
        };
        return `<button onclick="window.contactActions[${i}]()" 
                class="w-full mb-3 px-6 py-4 bg-gradient-to-r ${colors[op.color]} text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    ${op.icon} ${op.text}
                </button>`;
    }).join('');
    
    window.contactActions = opciones.map(op => op.action);
    
    Swal.fire({
        title: '<strong>Contactar Cliente</strong>',
        html: `<div class="mt-4">${buttonsHtml}</div>`,
        showConfirmButton: false,
        showCloseButton: true,
        customClass: {
            popup: 'rounded-2xl'
        }
    });
}

function confirmarEliminacion(url, nombre) {
    Swal.fire({
        title: '¿Eliminar cliente?',
        html: `
            <div class="text-left space-y-4">
                <p class="text-gray-600">Estás a punto de eliminar al cliente:</p>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <p class="font-bold text-gray-900 text-lg">"${nombre}"</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
                    <p class="text-sm text-red-700">
                        <strong>⚠️ Advertencia:</strong> Esta acción no se puede deshacer.
                    </p>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'rounded-lg px-6 py-3 font-bold',
            cancelButton: 'rounded-lg px-6 py-3 font-bold'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Eliminando cliente...',
                html: '<div class="flex justify-center mt-4"><div class="animate-spin w-10 h-10 border-4 border-[#218786] border-t-transparent rounded-full"></div></div>',
                allowOutsideClick: false,
                showConfirmButton: false
            });
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.innerHTML = `@csrf @method('DELETE')`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function exportarClientes() {
    Swal.fire({
        title: 'Exportar Clientes',
        text: 'Selecciona el formato de exportación',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'PDF',
        cancelButtonText: 'Excel',
        confirmButtonColor: '#218786',
        cancelButtonColor: '#059669',
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'rounded-lg px-6 py-3',
            cancelButton: 'rounded-lg px-6 py-3'
        }
    }).then((result) => {
        if (result.isConfirmed || result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire({
                icon: 'info',
                title: 'Próximamente',
                text: 'Funcionalidad en desarrollo',
                confirmButtonColor: '#218786'
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#218786',
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-lg px-6 py-3'
            }
        });
    @endif
});
</script>
@endpush
@endsection
