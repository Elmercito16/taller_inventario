@extends('layouts.app')

@section('title', 'Reportes y Estadísticas')
@section('page-title', 'Reportes de Ventas')

@push('styles')
<style>
    .bg-brand { background-color: #218786; }
    .text-brand { color: #218786; }
    .border-brand { border-color: #218786; }
    .hover\:bg-brand-dark:hover { background-color: #1a6b6a; }
    .focus\:ring-brand:focus { --tw-ring-color: #218786; }
    
    /* Barras de progreso animadas */
    .progress-bar {
        transition: width 1s ease-in-out;
    }

    /* Ocultar elementos Alpine antes de cargar */
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
{{-- 1. Inicializamos Alpine con el valor actual del rango --}}
<div class="space-y-8" x-data="{ rango: '{{ $rango }}' }">

    <!-- Barra de Filtros -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center space-x-2 text-gray-600">
            <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span class="text-sm font-medium">Periodo de análisis:</span>
        </div>

        <form action="{{ route('reportes.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full md:w-auto" id="reportFilter">
            <div class="flex bg-gray-100 p-1 rounded-lg">
                {{-- Usamos la lógica de PHP para la clase activa al recargar, y Alpine no interfiere aquí --}}
                <button type="submit" name="rango" value="hoy" class="px-4 py-1.5 text-sm font-medium rounded-md transition-all {{ $rango == 'hoy' ? 'bg-white text-brand shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">Día</button>
                <button type="submit" name="rango" value="semana" class="px-4 py-1.5 text-sm font-medium rounded-md transition-all {{ $rango == 'semana' ? 'bg-white text-brand shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">Semana</button>
                <button type="submit" name="rango" value="mes" class="px-4 py-1.5 text-sm font-medium rounded-md transition-all {{ $rango == 'mes' ? 'bg-white text-brand shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">Mes</button>
                <button type="submit" name="rango" value="anio" class="px-4 py-1.5 text-sm font-medium rounded-md transition-all {{ $rango == 'anio' ? 'bg-white text-brand shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">Año</button>
            </div>
            
            <!-- Opción para rango personalizado -->
            <div class="hidden md:block border-l border-gray-300 h-6 mx-2"></div>
            
            {{-- 2. Select controlado por Alpine --}}
            <select name="rango" 
                    x-model="rango" 
                    @change="if(rango !== 'personalizado') $el.form.submit()" 
                    class="text-sm border-gray-300 rounded-lg focus:ring-brand focus:border-brand cursor-pointer">
                <option value="" disabled>Más opciones...</option>
                <option value="hoy" x-show="false">Hoy</option> {{-- Ocultos en el select para no duplicar --}}
                <option value="semana" x-show="false">Semana</option>
                <option value="mes" x-show="false">Mes</option>
                <option value="anio" x-show="false">Año</option>
                <option value="personalizado">Personalizado</option>
            </select>
        </form>
    </div>

    <!-- Selector de fechas personalizado (Controlado por Alpine) -->
    {{-- 3. Quitamos el @if de Blade y usamos x-show para que exista siempre pero oculto --}}
    <div id="customDates" 
         x-show="rango === 'personalizado'" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="bg-gray-50 p-4 rounded-xl border border-gray-200">
        
        <form action="{{ route('reportes.index') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-4">
            <input type="hidden" name="rango" value="personalizado">
            <div class="w-full sm:w-auto">
                <label class="block text-xs font-medium text-gray-500 mb-1">Desde</label>
                <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="w-full text-sm border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
            </div>
            <div class="w-full sm:w-auto">
                <label class="block text-xs font-medium text-gray-500 mb-1">Hasta</label>
                <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" class="w-full text-sm border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
            </div>
            <button type="submit" class="w-full sm:w-auto px-6 py-2 bg-brand text-white text-sm font-medium rounded-lg hover:bg-brand-dark transition-colors shadow-sm">
                Aplicar Filtro
            </button>
        </form>
    </div>

    <!-- Sección 1: Resumen de Ventas (KPIs) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Ingresos -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Ingresos Totales</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2">S/ {{ number_format($totalIngresos, 2) }}</h3>
                </div>
                <div class="p-3 bg-green-50 rounded-xl text-green-600 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-gray-400">
                <span class="text-green-600 font-medium bg-green-50 px-2 py-0.5 rounded-full mr-2">100%</span>
                del periodo seleccionado
            </div>
        </div>

        <!-- Ventas Realizadas -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Ventas Realizadas</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $cantidadVentas }}</h3>
                </div>
                <div class="p-3 bg-blue-50 rounded-xl text-blue-600 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-gray-400">
                <span class="text-blue-600 font-medium bg-blue-50 px-2 py-0.5 rounded-full mr-2">Ticket Prom:</span>
                S/ {{ $cantidadVentas > 0 ? number_format($totalIngresos / $cantidadVentas, 2) : '0.00' }}
            </div>
        </div>

        <!-- Ganancia Estimada -->
        <div class="bg-gradient-to-br from-[#218786] to-[#1a5f5e] p-6 rounded-2xl shadow-lg text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
            <div class="flex justify-between items-start relative z-10">
                <div>
                    <p class="text-sm font-medium text-teal-100">Ganancia Estimada (30%)</p>
                    <h3 class="text-3xl font-bold mt-2">S/ {{ number_format($gananciaEstimada, 2) }}</h3>
                </div>
                <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <div class="mt-4 text-xs text-teal-100 opacity-80">
                Calculado sobre el margen base
            </div>
        </div>
    </div>

    <!-- Sección 2: Tablas de Detalles -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Mejores Productos -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    Productos Top
                </h3>
                <a href="{{ route('repuestos.index') }}" class="text-xs font-medium text-brand hover:text-teal-700">Ver inventario →</a>
            </div>
            <div class="p-4">
                <div class="space-y-4">
                    @forelse($topProductos as $producto)
                        <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition-colors">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center font-bold text-gray-500 text-sm">
                                    {{ $loop->iteration }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 line-clamp-1">{{ $producto['nombre'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $producto['codigo'] }}</p>
                                </div>
                            </div>
                            <div class="text-right min-w-max pl-4">
                                <p class="text-sm font-bold text-brand">S/ {{ number_format($producto['total_generado'], 2) }}</p>
                                <p class="text-xs text-gray-500">{{ $producto['cantidad_vendida'] }} unds.</p>
                            </div>
                        </div>
                        <!-- Barra de progreso visual -->
                        <div class="w-full bg-gray-100 rounded-full h-1.5 mt-1 mb-2">
                            <div class="bg-brand h-1.5 rounded-full" style="width: {{ ($producto['total_generado'] / ($topProductos->first()['total_generado'] ?? 1)) * 100 }}%"></div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500 text-sm">
                            <div class="bg-gray-50 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            </div>
                            No hay datos de ventas en este periodo.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Mejores Categorías y Método de Pago -->
        <div class="space-y-6">
            <!-- Mejores Categorías -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        Mejores Categorías
                    </h3>
                </div>
                <div class="p-4">
                    <div class="space-y-4">
                        @forelse($topCategorias as $categoria)
                            <div class="group">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-medium text-gray-700">{{ $categoria['nombre'] }}</span>
                                    <span class="font-bold text-gray-900">S/ {{ number_format($categoria['total_generado'], 2) }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-purple-500 h-2 rounded-full progress-bar" style="width: {{ ($categoria['total_generado'] / ($totalIngresos > 0 ? $totalIngresos : 1)) * 100 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-400 mt-1 text-right">{{ $categoria['cantidad_items'] }} items vendidos</p>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500 text-sm">No hay datos de categorías.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Método de Pago (Simulado) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        Métodos de Pago
                    </h3>
                </div>
                <div class="p-6 flex items-center justify-around">
                    <div class="text-center">
                        <div class="w-12 h-12 mx-auto bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="text-sm font-medium text-gray-500">Efectivo</p>
                        <p class="text-lg font-bold text-gray-900">100%</p>
                    </div>
                    <!-- Opacidad al 50% para indicar que no hay datos aun -->
                    <div class="text-center opacity-40">
                        <div class="w-12 h-12 mx-auto bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="text-sm font-medium text-gray-500">Digital</p>
                        <p class="text-lg font-bold text-gray-900">0%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection