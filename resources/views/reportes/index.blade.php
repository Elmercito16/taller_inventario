@extends('layouts.app')

@section('title', 'Reportes de Ventas')
@section('page-title', 'Reportes de Ventas')
@section('page-description', 'Análisis detallado y estadísticas de ventas')

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
    .fade-in:nth-child(5) { animation-delay: 0.4s; }
    
    .progress-bar { 
        transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    [x-cloak] { display: none !important; }
    
    .stat-card {
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
    }
    
    .pulse-ring {
        animation: pulseRing 2s infinite;
    }
    
    @keyframes pulseRing {
        0%, 100% { box-shadow: 0 0 20px rgba(33, 135, 134, 0.3); }
        50% { box-shadow: 0 0 40px rgba(33, 135, 134, 0.5); }
    }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="reportesManager()">

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
        <span class="font-medium text-gray-900">Reportes</span>
    </nav>

    <!-- Filtros de Periodo Mejorados -->
    <div class="fade-in bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-gradient-to-br from-[#218786] to-[#1a6d6c] rounded-xl shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">Periodo de Análisis</p>
                    <p class="text-xs text-gray-500">Selecciona el rango de fechas para el reporte</p>
                </div>
            </div>

            <form action="{{ route('reportes.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <!-- Botones de Filtro Rápido -->
                <div class="flex bg-gray-100 p-1.5 rounded-xl gap-1 overflow-x-auto">
                    <button type="submit" name="rango" value="hoy" 
                            class="px-5 py-2.5 text-sm font-bold rounded-lg transition-all whitespace-nowrap {{ $rango == 'hoy' ? 'bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white shadow-lg transform scale-105' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-200' }}">
                        📅 Hoy
                    </button>
                    <button type="submit" name="rango" value="semana" 
                            class="px-5 py-2.5 text-sm font-bold rounded-lg transition-all whitespace-nowrap {{ $rango == 'semana' ? 'bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white shadow-lg transform scale-105' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-200' }}">
                        📊 Semana
                    </button>
                    <button type="submit" name="rango" value="mes" 
                            class="px-5 py-2.5 text-sm font-bold rounded-lg transition-all whitespace-nowrap {{ $rango == 'mes' ? 'bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white shadow-lg transform scale-105' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-200' }}">
                        📈 Mes
                    </button>
                    <button type="submit" name="rango" value="anio" 
                            class="px-5 py-2.5 text-sm font-bold rounded-lg transition-all whitespace-nowrap {{ $rango == 'anio' ? 'bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white shadow-lg transform scale-105' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-200' }}">
                        📆 Año
                    </button>
                </div>
                
                <!-- Botón Personalizado -->
                <button type="button"
                        @click="rangoPersonalizado = !rangoPersonalizado"
                        class="inline-flex items-center px-5 py-2.5 text-sm font-bold border-2 {{ $rango == 'personalizado' ? 'border-[#218786] bg-[#21878610] text-[#218786] shadow-lg' : 'border-gray-300 text-gray-700 hover:border-gray-400 hover:bg-gray-50' }} rounded-xl transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    Personalizado
                    <svg class="w-4 h-4 ml-2 transition-transform" :class="rangoPersonalizado ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Selector de Fechas Personalizado -->
    <div x-show="rangoPersonalizado" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="fade-in bg-gradient-to-br from-blue-50 via-white to-indigo-50 rounded-2xl border-2 border-blue-200 shadow-xl p-6">
        <form action="{{ route('reportes.index') }}" method="GET" class="space-y-5">
            <input type="hidden" name="rango" value="personalizado">
            
            <div class="flex items-center gap-3 mb-6">
                <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Rango Personalizado</h3>
                    <p class="text-sm text-gray-600">Define tu periodo de análisis específico</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Fecha Inicio *
                    </label>
                    <input type="date" 
                           name="fecha_inicio" 
                           value="{{ request('fecha_inicio') }}" 
                           required
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all hover:border-gray-400">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Fecha Fin *
                    </label>
                    <input type="date" 
                           name="fecha_fin" 
                           value="{{ request('fecha_fin') }}" 
                           required
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all hover:border-gray-400">
                </div>
            </div>
            
            <div class="flex gap-3 pt-3">
                <button type="button"
                        @click="rangoPersonalizado = false"
                        class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all">
                    Cancelar
                </button>
                <button type="submit" 
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-bold rounded-xl hover:shadow-2xl transition-all transform hover:-translate-y-0.5">
                    <span class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Aplicar Filtro
                    </span>
                </button>
            </div>
        </form>
    </div>

    <!-- KPIs Principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Total Ingresos -->
        <div class="fade-in stat-card bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-green-50 rounded-full opacity-50"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-xl">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex items-center gap-1 px-3 py-1.5 bg-green-50 rounded-lg border border-green-200">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        <span class="text-xs font-bold text-green-700">Ingresos</span>
                    </div>
                </div>
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Ingresos Totales</p>
                <h3 class="text-4xl font-bold text-green-600 mt-2">S/ {{ number_format($totalIngresos, 2) }}</h3>
                <p class="text-xs text-gray-500 mt-3 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    Ventas del periodo seleccionado
                </p>
            </div>
        </div>

        <!-- Ventas Realizadas -->
        <div class="fade-in stat-card bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-blue-50 rounded-full opacity-50"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-xl">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <div class="flex items-center gap-1 px-3 py-1.5 bg-blue-50 rounded-lg border border-blue-200">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="text-xs font-bold text-blue-700">Ventas</span>
                    </div>
                </div>
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Ventas Realizadas</p>
                <h3 class="text-4xl font-bold text-blue-600 mt-2">{{ $cantidadVentas }}</h3>
                <div class="mt-3 flex items-center justify-between text-xs">
                    <span class="text-gray-500">Ticket promedio:</span>
                    <span class="font-bold text-blue-600 text-sm">
                        S/ {{ $cantidadVentas > 0 ? number_format($totalIngresos / $cantidadVentas, 2) : '0.00' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Ganancia Estimada -->
        <div class="fade-in stat-card bg-gradient-to-br from-[#218786] to-[#1a6d6c] rounded-2xl shadow-2xl p-6 text-white relative overflow-hidden pulse-ring">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-40 h-40 bg-white opacity-10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl ring-2 ring-white/30">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <div class="px-3 py-1.5 bg-white/20 backdrop-blur-sm rounded-lg">
                        <span class="text-xs font-bold">30% Margen</span>
                    </div>
                </div>
                <p class="text-xs font-semibold text-white/80 uppercase tracking-wide">Ganancia Estimada</p>
                <h3 class="text-5xl font-bold mt-2">S/ {{ number_format($gananciaEstimada, 2) }}</h3>
                <p class="text-xs text-white/80 mt-3 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                    Margen base aplicado del periodo
                </p>
            </div>
        </div>
    </div>

    <!-- Sección de Detalles -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Productos Top -->
        <div class="fade-in bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 bg-gradient-to-r from-orange-50 via-white to-orange-50 border-b border-orange-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Productos Destacados</h3>
                            <p class="text-xs text-gray-600">Top productos más vendidos</p>
                        </div>
                    </div>
                    <a href="{{ route('repuestos.index') }}" 
                       class="inline-flex items-center text-xs font-bold text-orange-600 hover:text-orange-700 bg-orange-50 px-4 py-2 rounded-xl hover:bg-orange-100 transition-all">
                        Ver todo
                        <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="p-6">
                @forelse($topProductos as $producto)
                    <div class="mb-5 last:mb-0">
                        <div class="flex items-center justify-between p-4 hover:bg-gradient-to-r hover:from-orange-50 hover:to-transparent rounded-xl transition-all group cursor-pointer">
                            <div class="flex items-center gap-4 flex-1 min-w-0">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center text-white font-bold shadow-lg text-lg group-hover:scale-110 transition-transform">
                                    {{ $loop->iteration }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-900 truncate">{{ $producto['nombre'] }}</p>
                                    <p class="text-xs text-gray-500 font-medium">{{ $producto['codigo'] }}</p>
                                </div>
                            </div>
                            <div class="text-right ml-4">
                                <p class="text-sm font-bold text-orange-600">S/ {{ number_format($producto['total_generado'], 2) }}</p>
                                <p class="text-xs text-gray-500">{{ $producto['cantidad_vendida'] }} unidades</p>
                            </div>
                        </div>
                        <div class="mt-2 w-full bg-gray-100 rounded-full h-2.5 overflow-hidden shadow-inner">
                            <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-2.5 rounded-full progress-bar shadow-sm" 
                                 style="width: {{ ($producto['total_generado'] / ($topProductos->first()['total_generado'] ?? 1)) * 100 }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16">
                        <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900 mb-1">Sin datos disponibles</h3>
                        <p class="text-xs text-gray-500">No hay productos vendidos en este periodo</p>
                    </div>
                @endforelse
            </div>
        </div>

                <!-- Categorías y Clientes Top -->
        <div class="space-y-6">
            <!-- Mejores Categorías -->
            <div class="fade-in bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 bg-gradient-to-r from-purple-50 via-white to-purple-50 border-b border-purple-100">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Categorías Principales</h3>
                            <p class="text-xs text-gray-600">Rendimiento por categoría</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @forelse($topCategorias as $categoria)
                        <div class="mb-5 last:mb-0 p-4 hover:bg-gradient-to-r hover:from-purple-50 hover:to-transparent rounded-xl transition-all cursor-pointer">
                            <div class="flex justify-between items-center mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full shadow-md"></div>
                                    <span class="text-sm font-bold text-gray-900">{{ $categoria['nombre'] }}</span>
                                </div>
                                <span class="text-sm font-bold text-purple-600">S/ {{ number_format($categoria['total_generado'], 2) }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden shadow-inner">
                                <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-3 rounded-full progress-bar shadow-sm" 
                                     style="width: {{ ($categoria['total_generado'] / ($totalIngresos > 0 ? $totalIngresos : 1)) * 100 }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2 flex items-center justify-between">
                                <span>{{ $categoria['cantidad_items'] }} items vendidos</span>
                                <span class="font-bold text-purple-600">{{ number_format(($categoria['total_generado'] / ($totalIngresos > 0 ? $totalIngresos : 1)) * 100, 1) }}%</span>
                            </p>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-600 font-medium">Sin categorías</p>
                        </div>
                    @endforelse
                </div>
            </div>


            <!-- Top Clientes -->
            <div class="fade-in bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 bg-gradient-to-r from-blue-50 via-white to-blue-50 border-b border-blue-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Clientes Top</h3>
                                <p class="text-xs text-gray-600">Mejores compradores</p>
                            </div>
                        </div>
                        <a href="{{ route('clientes.index') }}" 
                           class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50 px-4 py-2 rounded-xl hover:bg-blue-100 transition-all">
                            Ver todos
                            <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    @forelse($topClientes as $index => $cliente)
                        <div class="mb-4 last:mb-0">
                            <div class="flex items-center justify-between p-4 hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent rounded-xl transition-all group cursor-pointer">
                                <div class="flex items-center gap-4 flex-1 min-w-0">
                                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                        <span class="text-white text-xl font-bold">
                                            {{ strtoupper(substr($cliente['nombre'], 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate">{{ $cliente['nombre'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $cliente['cantidad_compras'] }} compras realizadas</p>
                                    </div>
                                </div>
                                <div class="text-right ml-4">
                                    <p class="text-sm font-bold text-blue-600">S/ {{ number_format($cliente['total_gastado'], 2) }}</p>
                                    <p class="text-xs text-gray-500">Promedio: S/ {{ number_format($cliente['total_gastado'] / $cliente['cantidad_compras'], 2) }}</p>
                                </div>
                            </div>
                            <div class="mt-2 w-full bg-gray-100 rounded-full h-2 overflow-hidden shadow-inner">
                                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full progress-bar shadow-sm" 
                                     style="width: {{ ($cliente['total_gastado'] / ($topClientes->first()['total_gastado'] ?? 1)) * 100 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-gray-900 mb-1">Sin clientes</h3>
                            <p class="text-xs text-gray-500">No hay clientes con compras en este periodo</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>


           
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('reportesManager', () => ({
        rangoPersonalizado: {{ $rango == 'personalizado' ? 'true' : 'false' }}
    }));
});

// Animación de las barras de progreso
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        document.querySelectorAll('.progress-bar').forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
            }, 100);
        });
    }, 200);
});

// Mensajes de éxito
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
</script>
@endpush
@endsection
