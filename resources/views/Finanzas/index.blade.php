@extends('layouts.app')

@section('title', 'Finanzas')
@section('page-title', 'Gestión Financiera')
@section('page-description', 'Control completo de ingresos y gastos')

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
    .fade-in:nth-child(4) { animation-delay: 0.3s; }
    
    [x-cloak] { display: none !important; }
    
    .stat-card {
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
    }
    
    .pulse-glow {
        animation: pulseGlow 2s infinite;
    }
    
    @keyframes pulseGlow {
        0%, 100% { box-shadow: 0 0 20px rgba(33, 135, 134, 0.3); }
        50% { box-shadow: 0 0 30px rgba(33, 135, 134, 0.5); }
    }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="finanzasManager()">

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
        <span class="font-medium text-gray-900">Finanzas</span>
    </nav>

    <!-- Filtros y Acciones -->
    <div class="fade-in bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            
            <!-- Filtros de Fecha -->
            <form action="{{ route('finanzas.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-[#21878610] rounded-xl">
                        <svg class="w-5 h-5 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-gray-900">Periodo:</span>
                </div>
                
                <select name="filtro" 
                          onchange="this.form.submit()" 
                          class="px-4 py-2.5 text-sm font-semibold border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all">
                   <option value="hoy" {{ $filtro == 'hoy' ? 'selected' : '' }}>📅 Hoy</option>
                   <option value="semana" {{ $filtro == 'semana' ? 'selected' : '' }}>📊 Esta Semana</option>
                   <option value="mes_actual" {{ $filtro == 'mes_actual' ? 'selected' : '' }}>📈 Este Mes</option>
                   <option value="anio_actual" {{ $filtro == 'anio_actual' ? 'selected' : '' }}>📆 Este Año</option>
                   <option value="personalizado" {{ $filtro == 'personalizado' ? 'selected' : '' }}>🎯 Personalizado</option>
                </select>

                @if($filtro == 'personalizado')
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 bg-gray-50 p-3 rounded-xl border border-gray-200">
                        <input type="date" 
                               name="fecha_inicio" 
                               value="{{ request('fecha_inicio') }}" 
                               class="px-3 py-2 text-sm border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786]">
                        <svg class="w-5 h-5 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                        <input type="date" 
                               name="fecha_fin" 
                               value="{{ request('fecha_fin') }}" 
                               class="px-3 py-2 text-sm border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786]">
                        <button type="submit" 
                                class="px-4 py-2 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-bold rounded-xl hover:shadow-lg transition-all">
                            Aplicar
                        </button>
                    </div>
                @endif
            </form>

            <!-- Botones de Acción -->
            <div class="flex flex-col sm:flex-row gap-3">
                <button @click="showIngresoModal = true" 
                        class="inline-flex items-center justify-center px-5 py-2.5 bg-white border-2 border-green-300 text-green-600 font-bold rounded-xl hover:bg-green-50 hover:border-green-400 transition-all shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Otro Ingreso
                </button>

                <button @click="showGastoModal = true" 
                        class="inline-flex items-center justify-center px-5 py-2.5 bg-white border-2 border-red-300 text-red-600 font-bold rounded-xl hover:bg-red-50 hover:border-red-400 transition-all shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                    Registrar Gasto
                </button>

                <a href="{{ route('ventas.create') }}" 
                   class="inline-flex items-center justify-center px-5 py-2.5 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-bold rounded-xl hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Nueva Venta
                </a>
            </div>
        </div>
    </div>

    <!-- KPIs Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Ingresos -->
        <div class="fade-in stat-card bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-green-50 rounded-full opacity-50"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <div class="flex items-center gap-1 px-3 py-1.5 bg-green-50 rounded-lg border border-green-200">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                        <span class="text-xs font-bold text-green-700">Ingresos</span>
                    </div>
                </div>
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Ingresos Totales</p>
                <h3 class="text-4xl font-bold text-green-600 mt-2">S/ {{ number_format($totalIngresos, 2) }}</h3>
                <div class="mt-4 flex items-center justify-between text-xs">
                    <span class="text-gray-500">Del periodo seleccionado</span>
                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-lg font-bold">+100%</span>
                </div>
            </div>
        </div>

        <!-- Gastos -->
        <div class="fade-in stat-card bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-red-50 rounded-full opacity-50"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                        </svg>
                    </div>
                    <div class="flex items-center gap-1 px-3 py-1.5 bg-red-50 rounded-lg border border-red-200">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                        <span class="text-xs font-bold text-red-700">Egresos</span>
                    </div>
                </div>
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Gastos Totales</p>
                <h3 class="text-4xl font-bold text-red-600 mt-2">S/ {{ number_format($totalGastos, 2) }}</h3>
                <div class="mt-4 flex items-center justify-between text-xs">
                    <span class="text-gray-500">Del periodo seleccionado</span>
                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded-lg font-bold">Egresos</span>
                </div>
            </div>
        </div>

        <!-- Balance -->
        <div class="fade-in stat-card bg-gradient-to-br from-[#218786] to-[#1a6d6c] rounded-2xl shadow-xl p-6 text-white relative overflow-hidden pulse-glow">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-40 h-40 bg-white opacity-10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl ring-2 ring-white/30">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                        </svg>
                    </div>
                    <div class="px-3 py-1.5 bg-white/20 backdrop-blur-sm rounded-lg">
                        <span class="text-xs font-bold">Balance Neto</span>
                    </div>
                </div>
                <p class="text-xs font-semibold text-white/80 uppercase tracking-wide">Balance Final</p>
                <h3 class="text-5xl font-bold mt-2">S/ {{ number_format($balance, 2) }}</h3>
                <div class="mt-4 flex items-center justify-between text-xs text-white/80">
                    <span>Ingresos - Gastos</span>
                    <span class="font-bold uppercase">{{ $filtro }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Movimientos -->
    <div class="fade-in bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-r from-gray-50 via-white to-gray-50 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-[#218786] to-[#1a6d6c] rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Flujo de Caja Detallado</h3>
                        <p class="text-sm text-gray-600">
                            @if(request('tipo_movimiento') == 'ingresos')
                                Solo Ingresos
                            @elseif(request('tipo_movimiento') == 'gastos')
                                Solo Gastos
                            @else
                                Historial completo de movimientos
                            @endif
                        </p>
                    </div>
                </div>
                
                <!-- Filtro de Tipo -->
                <div class="flex items-center gap-3">
                    <form action="{{ route('finanzas.index') }}" method="GET" class="flex items-center gap-2">
                        <!-- Mantener filtros existentes -->
                        <input type="hidden" name="filtro" value="{{ $filtro }}">
                        @if($filtro == 'personalizado')
                            <input type="hidden" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
                            <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin') }}">
                        @endif
                        
                        <select name="tipo_movimiento" 
                                onchange="this.form.submit()" 
                                class="px-4 py-2.5 text-sm font-semibold border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all bg-white">
                            <option value="todos" {{ request('tipo_movimiento', 'todos') == 'todos' ? 'selected' : '' }}>📋 Todos</option>
                            <option value="ingresos" {{ request('tipo_movimiento') == 'ingresos' ? 'selected' : '' }}>💰 Solo Ingresos</option>
                            <option value="gastos" {{ request('tipo_movimiento') == 'gastos' ? 'selected' : '' }}>💸 Solo Gastos</option>
                        </select>
                    </form>
                    
                    <div class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#21878610] to-[#21878620] rounded-xl border border-[#21878630]">
                        <svg class="w-4 h-4 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-sm font-bold text-[#218786]">{{ $cajaFlujo->total() }} registros</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Fecha
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                                Descripción
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                Tipo
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center justify-end gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Monto
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($cajaFlujo as $movimiento)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center mr-3 group-hover:bg-[#21878610] transition-colors">
                                        <svg class="w-5 h-5 text-gray-500 group-hover:text-[#218786] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">
                                            {{ \Carbon\Carbon::parse($movimiento['fecha'])->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($movimiento['fecha'])->format('H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-2 h-2 rounded-full mr-3 {{ $movimiento['tipo'] == 'ingreso' ? 'bg-green-500 shadow-lg shadow-green-500/50' : 'bg-red-500 shadow-lg shadow-red-500/50' }}"></div>
                                    <span class="text-sm font-semibold text-gray-900">{{ $movimiento['descripcion'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($movimiento['tipo'] == 'ingreso')
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r from-green-50 to-green-100 text-green-700 border border-green-200">
                                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                        </svg>
                                        Ingreso
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r from-red-50 to-red-100 text-red-700 border border-red-200">
                                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                        </svg>
                                        Gasto
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-lg font-bold {{ $movimiento['tipo'] == 'ingreso' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $movimiento['tipo'] == 'ingreso' ? '+' : '-' }} S/ {{ number_format($movimiento['monto'], 2) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">Sin movimientos registrados</h3>
                                    <p class="text-sm text-gray-500">No hay transacciones en este periodo</p>
                                    <p class="text-xs text-gray-400 mt-1">Cambia los filtros de fecha para ver más resultados</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 👇 PAGINACIÓN AGREGADA AQUÍ -->
        @if($cajaFlujo->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <!-- Info de resultados -->
                    <div class="text-sm text-gray-600">
                        Mostrando 
                        <span class="font-bold text-[#218786]">{{ $cajaFlujo->firstItem() }}</span>
                        a 
                        <span class="font-bold text-[#218786]">{{ $cajaFlujo->lastItem() }}</span>
                        de 
                        <span class="font-bold text-[#218786]">{{ $cajaFlujo->total() }}</span>
                        registros
                    </div>

                    <!-- Links de paginación -->
                    <div class="flex items-center gap-2">
                        {{-- Botón Anterior --}}
                        @if ($cajaFlujo->onFirstPage())
                            <span class="px-4 py-2 text-sm font-semibold text-gray-400 bg-gray-100 rounded-xl cursor-not-allowed">
                                ← Anterior
                            </span>
                        @else
                            <a href="{{ $cajaFlujo->appends(request()->except('page'))->previousPageUrl() }}" 
                               class="px-4 py-2 text-sm font-semibold text-[#218786] bg-white border-2 border-[#218786] rounded-xl hover:bg-[#218786] hover:text-white transition-all">
                                ← Anterior
                            </a>
                        @endif

                        {{-- Números de página --}}
                        <div class="hidden sm:flex items-center gap-2">
                            @foreach ($cajaFlujo->getUrlRange(1, $cajaFlujo->lastPage()) as $page => $url)
                                @if ($page == $cajaFlujo->currentPage())
                                    <span class="px-4 py-2 text-sm font-bold text-white bg-gradient-to-r from-[#218786] to-[#1a6d6c] rounded-xl shadow-lg">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $cajaFlujo->appends(request()->except('page'))->url($page) }}" 
                                       class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:border-[#218786] hover:text-[#218786] transition-all">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        </div>

                        {{-- Botón Siguiente --}}
                        @if ($cajaFlujo->hasMorePages())
                            <a href="{{ $cajaFlujo->appends(request()->except('page'))->nextPageUrl() }}" 
                               class="px-4 py-2 text-sm font-semibold text-[#218786] bg-white border-2 border-[#218786] rounded-xl hover:bg-[#218786] hover:text-white transition-all">
                                Siguiente →
                            </a>
                        @else
                            <span class="px-4 py-2 text-sm font-semibold text-gray-400 bg-gray-100 rounded-xl cursor-not-allowed">
                                Siguiente →
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal Registrar Otro Ingreso -->
    <div x-show="showIngresoModal" 
         x-cloak
         @click.self="showIngresoModal = false"
         class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.stop
             x-show="showIngresoModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden">
            
            <form action="{{ route('finanzas.storeIngreso') }}" method="POST">
                @csrf
                
                <!-- Header -->
                <div class="px-6 py-5 bg-gradient-to-r from-green-500 to-green-600 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-white/10 rounded-full"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold">Registrar Ingreso</h3>
                                    <p class="text-sm text-white/80">Ingreso adicional no relacionado con ventas</p>
                                </div>
                            </div>
                            <button type="button" 
                                    @click="showIngresoModal = false"
                                    class="text-white/80 hover:text-white hover:bg-white/20 p-2 rounded-lg transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Body -->
                <div class="px-6 py-6 space-y-5">
                    <!-- Monto -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Monto *
                        </label>
                        <div class="relative group">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-green-500 font-bold text-xl">S/</span>
                            <input type="number" 
                                   name="monto" 
                                   step="0.01" 
                                   required 
                                   class="w-full pl-14 pr-4 py-4 text-xl font-bold text-green-600 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all hover:border-gray-400" 
                                   placeholder="0.00">
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Descripción *
                        </label>
                        <input type="text" 
                               name="descripcion" 
                               required 
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all hover:border-gray-400" 
                               placeholder="Ej: Intereses bancarios, alquiler, servicio de instalación...">
                    </div>
                    
                    <!-- Fecha y Tipo de Ingreso -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Fecha *
                            </label>
                            <input type="date" 
                                   name="fecha" 
                                   value="{{ date('Y-m-d') }}" 
                                   required 
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all hover:border-gray-400">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                Tipo de Ingreso *
                            </label>
                            <select name="tipo_ingreso" 
                                    required
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all hover:border-gray-400">
                                <option value="">Selecciona...</option>
                                <optgroup label="Ingresos Operacionales">
                                    <option value="Servicios">🔧 Servicios/Reparación</option>
                                    <option value="Instalación">📦 Instalación</option>
                                    <option value="Mantenimiento">⚙️ Mantenimiento</option>
                                    <option value="Alquiler Equipo">🛠️ Alquiler de Equipo</option>
                                </optgroup>
                                <optgroup label="Ingresos No Operacionales">
                                    <option value="Intereses">💵 Intereses Bancarios</option>
                                    <option value="Alquiler Espacio">🏢 Alquiler de Espacio</option>
                                    <option value="Recuperación">📊 Recuperación de Deudas</option>
                                    <option value="Venta Activos">💸 Venta de Activos</option>
                                    <option value="Aporte Capital">🎁 Aporte de Capital</option>
                                    <option value="Reembolso">↩️ Reembolso/Devolución</option>
                                    <option value="Otros">📋 Otros Ingresos</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col-reverse sm:flex-row justify-end gap-3">
                    <button type="button" 
                            @click="showIngresoModal = false" 
                            class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-100 transition-all">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Confirmar Ingreso
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Registrar Gasto -->
    <div x-show="showGastoModal" 
         x-cloak
         @click.self="showGastoModal = false"
         class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.stop
             x-show="showGastoModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden">
            
            <form action="{{ route('finanzas.storeGasto') }}" method="POST">
                @csrf
                
                <!-- Header -->
                <div class="px-6 py-5 bg-gradient-to-r from-red-500 to-red-600 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-white/10 rounded-full"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold">Registrar Gasto</h3>
                                    <p class="text-sm text-white/80">Ingresa los detalles del egreso</p>
                                </div>
                            </div>
                            <button type="button" 
                                    @click="showGastoModal = false"
                                    class="text-white/80 hover:text-white hover:bg-white/20 p-2 rounded-lg transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Body -->
                <div class="px-6 py-6 space-y-5">
                    <!-- Monto -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Monto *
                        </label>
                        <div class="relative group">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-red-500 font-bold text-xl">S/</span>
                            <input type="number" 
                                   name="monto" 
                                   step="0.01" 
                                   required 
                                   class="w-full pl-14 pr-4 py-4 text-xl font-bold text-red-600 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all hover:border-gray-400" 
                                   placeholder="0.00">
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Descripción *
                        </label>
                        <input type="text" 
                               name="descripcion" 
                               required 
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all hover:border-gray-400" 
                               placeholder="Ej: Pago de servicios, compra de materiales...">
                    </div>
                    
                    <!-- Fecha y Categoría -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Fecha *
                            </label>
                            <input type="date" 
                                   name="fecha" 
                                   value="{{ date('Y-m-d') }}" 
                                   required 
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all hover:border-gray-400">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                Categoría
                            </label>
                            <select name="categoria" 
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all hover:border-gray-400">
                                <option value="">Sin categoría</option>
                                <option value="Servicios">💡 Servicios</option>
                                <option value="Alquiler">🏢 Alquiler</option>
                                <option value="Planilla">👥 Planilla</option>
                                <option value="Mantenimiento">🔧 Mantenimiento</option>
                                <option value="Mercadería">📦 Mercadería</option>
                                <option value="Impuestos">📊 Impuestos</option>
                                <option value="Transporte">🚚 Transporte</option>
                                <option value="Otros">📋 Otros</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col-reverse sm:flex-row justify-end gap-3">
                    <button type="button" 
                            @click="showGastoModal = false" 
                            class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-100 transition-all">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Confirmar Gasto
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('finanzasManager', () => ({
        showGastoModal: false,
        showIngresoModal: false
    }));
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
    
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            confirmButtonColor: '#218786',
            customClass: {
                popup: 'rounded-2xl'
            }
        });
    @endif
});
</script>
@endpush
@endsection
