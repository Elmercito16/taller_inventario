@extends('layouts.app')

@section('title', 'Historial de Compras')
@section('page-title', 'Historial de Compras')
@section('page-description', 'Revisa el historial completo de transacciones del cliente')

@push('styles')
<style>
    .fade-in {
        animation: fadeIn 0.5s ease-out both;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .stat-card {
        animation: fadeIn 0.6s ease-out both;
    }
    
    .stat-card:nth-child(1) { animation-delay: 0s; }
    .stat-card:nth-child(2) { animation-delay: 0.1s; }
    .stat-card:nth-child(3) { animation-delay: 0.2s; }
    .stat-card:nth-child(4) { animation-delay: 0.3s; }
    
    .transaction-card {
        animation: fadeIn 0.5s ease-out both;
    }
    
    .transaction-card:nth-child(n) {
        animation-delay: calc(0.05s * var(--index));
    }
    
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="historialManager()">

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
        <a href="{{ route('clientes.index') }}" class="hover:text-[#218786] transition-colors">Clientes</a>
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <span class="font-medium text-gray-900">Historial</span>
    </nav>

    <!-- Header Card -->
    <div class="fade-in bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-[#218786] to-[#1a6d6c] rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                    <span class="text-white text-2xl font-bold">
                        {{ strtoupper(substr($cliente->nombre, 0, 1)) }}
                    </span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Historial de Compras</h1>
                    <p class="text-sm text-gray-600 mt-1">
                        Cliente: <span class="font-bold text-[#218786]">{{ $cliente->nombre }}</span>
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">DNI: {{ $cliente->dni }}</p>
                </div>
            </div>
            
            <div class="flex gap-3">
                <button @click="openFilters()" 
                        class="inline-flex items-center px-5 py-3 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-bold rounded-lg hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filtrar
                </button>
                
                <a href="{{ route('clientes.index') }}" 
                   class="inline-flex items-center px-5 py-3 bg-white border-2 border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-50 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Ventas -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total Ventas</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $ventas->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Transacciones registradas</p>
        </div>

        <!-- Monto Total -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Monto Total</p>
            <p class="text-3xl font-bold text-green-600 mt-1">S/ {{ number_format($ventas->sum('total'), 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Acumulado total</p>
        </div>

        <!-- Promedio -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Promedio</p>
            <p class="text-3xl font-bold text-purple-600 mt-1">
                S/ {{ $ventas->count() > 0 ? number_format($ventas->avg('total'), 2) : '0.00' }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Por transacción</p>
        </div>

        <!-- Última Compra -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="p-3 bg-amber-100 rounded-lg">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Última Compra</p>
            <p class="text-xl font-bold text-gray-900 mt-1">
                {{ optional($ventas->first())->fecha ? \Carbon\Carbon::parse($ventas->first()->fecha)->format('d/m/Y') : '—' }}
            </p>
            <p class="text-xs text-gray-500 mt-1">
                {{ optional($ventas->first())->fecha ? \Carbon\Carbon::parse($ventas->first()->fecha)->diffForHumans() : 'Sin compras' }}
            </p>
        </div>
    </div>

    <!-- Lista de Transacciones -->
    <div class="fade-in bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-orange-100 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Transacciones</h2>
                    <p class="text-sm text-gray-600">{{ $ventas->count() }} registros encontrados</p>
                </div>
            </div>
        </div>

        @if($ventas->count() > 0)
            <div class="space-y-4">
                @foreach ($ventas as $index => $venta)
                    <div class="transaction-card border-2 border-gray-200 rounded-xl overflow-hidden hover:border-[#218786] hover:shadow-md transition-all"
                         style="--index: {{ $index }}"
                         x-data="{ open: false }">
                        
                        <!-- Header -->
                        <div class="p-5 cursor-pointer hover:bg-gray-50 transition-colors" @click="open = !open">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4 flex-1">
                                    <div class="w-12 h-12 bg-gradient-to-br from-[#218786] to-[#1a6d6c] rounded-lg flex items-center justify-center shadow-md">
                                        <span class="text-white text-sm font-bold">#{{ $venta->id }}</span>
                                    </div>
                                    
                                    <div class="flex-1">
                                        <h3 class="font-bold text-gray-900">Venta #{{ $venta->id }}</h3>
                                        <div class="flex items-center gap-3 text-sm text-gray-600 mt-1">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ \Carbon\Carbon::parse($venta->fecha)->format('H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-4">
                                    <div class="text-right">
                                        <p class="text-xl font-bold text-gray-900">S/ {{ number_format($venta->total, 2) }}</p>
                                        @if($venta->estado === 'pagado')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 mt-1">
                                                ✓ Pagado
                                            </span>
                                        @elseif($venta->estado === 'pendiente')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700 mt-1">
                                                ⏱ Pendiente
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 mt-1">
                                                ✕ {{ ucfirst($venta->estado) }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <svg class="w-5 h-5 text-gray-400 transition-transform duration-300" 
                                         :class="{ 'rotate-180': open }"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Detalles -->
                        <div x-show="open" 
                             x-collapse
                             class="border-t-2 border-gray-100">
                            <div class="p-6 bg-gradient-to-br from-gray-50 to-white">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-4">
                                        <h4 class="font-bold text-gray-900 flex items-center">
                                            <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            Información de la Venta
                                        </h4>
                                        
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                                                <span class="text-sm text-gray-600">Fecha completa:</span>
                                                <span class="font-bold text-gray-900">
                                                    {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}
                                                </span>
                                            </div>
                                            
                                            <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                                                <span class="text-sm text-gray-600">Monto total:</span>
                                                <span class="font-bold text-green-600">
                                                    S/ {{ number_format($venta->total, 2) }}
                                                </span>
                                            </div>
                                            
                                            <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                                                <span class="text-sm text-gray-600">Estado:</span>
                                                @if($venta->estado === 'pagado')
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                                        ✓ Pagado
                                                    </span>
                                                @elseif($venta->estado === 'pendiente')
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                                                        ⏱ Pendiente
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                                        ✕ {{ ucfirst($venta->estado) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-center">
                                        <a href="{{ route('ventas.show', $venta) }}" 
                                           class="inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-bold rounded-xl hover:shadow-xl transform hover:-translate-y-1 transition-all">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Ver Detalle Completo
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Estado Vacío -->
            <div class="text-center py-20">
                <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Sin historial de compras</h3>
                <p class="text-gray-600 max-w-md mx-auto">
                    Este cliente aún no ha realizado ninguna transacción en el sistema
                </p>
            </div>
        @endif
    </div>

    <!-- Modal de Filtros -->
    <div x-show="showFilters" 
         x-cloak
         @click.self="closeFilters()"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.stop 
             x-show="showFilters"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-[#218786] to-[#1a6d6c] p-6 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filtrar Compras
                    </h2>
                    <button @click="closeFilters()" class="text-white hover:bg-white/20 p-2 rounded-lg transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Body -->
            <form method="GET" action="{{ route('clientes.historial', $cliente) }}" class="p-6">
                <div class="space-y-2 mb-6">
                    @php
                        $filtros = [
                            'today' => ['label' => 'Hoy', 'icon' => '📅'],
                            'yesterday' => ['label' => 'Ayer', 'icon' => '⏮️'],
                            'last_7_days' => ['label' => 'Últimos 7 días', 'icon' => '📊'],
                            'last_30_days' => ['label' => 'Últimos 30 días', 'icon' => '📈'],
                            'this_month' => ['label' => 'Este mes', 'icon' => '🗓️'],
                            'last_month' => ['label' => 'Mes pasado', 'icon' => '📆'],
                            'this_year' => ['label' => 'Este año', 'icon' => '🎯'],
                            'custom' => ['label' => 'Fecha personalizada', 'icon' => '🔧']
                        ];
                    @endphp

                    @foreach ($filtros as $value => $data)
                        <label class="flex items-center gap-3 p-4 rounded-xl hover:bg-gray-50 cursor-pointer transition-all border-2 border-transparent hover:border-[#218786]">
                            <input type="radio" 
                                   name="filter" 
                                   value="{{ $value }}" 
                                   x-model="selectedFilter"
                                   class="w-5 h-5 text-[#218786] focus:ring-[#218786]">
                            <span class="text-2xl">{{ $data['icon'] }}</span>
                            <span class="font-semibold text-gray-900">{{ $data['label'] }}</span>
                        </label>
                    @endforeach
                </div>

                <!-- Custom Date Range -->
                <div x-show="selectedFilter === 'custom'" 
                     x-transition
                     class="bg-gray-50 p-5 rounded-xl mb-6">
                    <h4 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wide">
                        Rango de fechas
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Fecha inicial</label>
                            <input type="date" 
                                   name="start_date" 
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Fecha final</label>
                            <input type="date" 
                                   name="end_date" 
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all">
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3 pt-4 border-t-2 border-gray-200">
                    <button type="button" 
                            @click="closeFilters()" 
                            class="flex-1 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-bold rounded-lg hover:shadow-lg transition-all">
                        Aplicar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('historialManager', () => ({
        showFilters: false,
        selectedFilter: 'last_30_days',
        
        openFilters() {
            this.showFilters = true;
            document.body.style.overflow = 'hidden';
        },
        
        closeFilters() {
            this.showFilters = false;
            document.body.style.overflow = '';
        },
        
        init() {
            // Detectar filtro activo de la URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('filter')) {
                this.selectedFilter = urlParams.get('filter');
            }
        }
    }));
});

// Cerrar modal con Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        Alpine.store('historial')?.closeFilters();
    }
});

// Mensajes de éxito
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
                popup: 'rounded-2xl'
            }
        });
    @endif
});
</script>
@endpush
@endsection
