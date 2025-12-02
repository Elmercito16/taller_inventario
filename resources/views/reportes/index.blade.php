@extends('layouts.app')

@section('title', 'Reportes de Ventas')

@push('styles')
<style>
    .card-fade { animation: fadeIn 0.5s ease-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .progress-bar { transition: width 1.2s ease-out; }
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="{ rango: '{{ $rango }}' }">

    <!-- Header con Título -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Reportes de Ventas</h1>
            <p class="text-sm text-gray-600 mt-1">Análisis detallado de tus ventas</p>
        </div>
    </div>

    <!-- Filtros de Periodo -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-gray-200">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-2 text-gray-600">
                <svg class="w-5 h-5 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm font-semibold">Periodo:</span>
            </div>

            <form action="{{ route('reportes.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full lg:w-auto">
                <!-- Botones de Filtro Rápido -->
                <div class="flex bg-gray-100 p-1 rounded-xl overflow-x-auto">
                    <button type="submit" name="rango" value="hoy" 
                            class="flex-1 sm:flex-none px-4 py-2 text-sm font-semibold rounded-lg transition-all whitespace-nowrap {{ $rango == 'hoy' ? 'bg-white text-[#218786] shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        Hoy
                    </button>
                    <button type="submit" name="rango" value="semana" 
                            class="flex-1 sm:flex-none px-4 py-2 text-sm font-semibold rounded-lg transition-all whitespace-nowrap {{ $rango == 'semana' ? 'bg-white text-[#218786] shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        Semana
                    </button>
                    <button type="submit" name="rango" value="mes" 
                            class="flex-1 sm:flex-none px-4 py-2 text-sm font-semibold rounded-lg transition-all whitespace-nowrap {{ $rango == 'mes' ? 'bg-white text-[#218786] shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        Mes
                    </button>
                    <button type="submit" name="rango" value="anio" 
                            class="flex-1 sm:flex-none px-4 py-2 text-sm font-semibold rounded-lg transition-all whitespace-nowrap {{ $rango == 'anio' ? 'bg-white text-[#218786] shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        Año
                    </button>
                </div>
                
                <!-- Select Personalizado -->
                <select name="rango" 
                        x-model="rango" 
                        @change="if(rango !== 'personalizado') $el.form.submit()" 
                        class="px-4 py-2 text-sm font-semibold border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-transparent">
                    <option value="personalizado">📅 Personalizado</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Selector de Fechas Personalizado -->
    <div x-show="rango === 'personalizado'" 
         x-cloak
         x-transition
         class="bg-gradient-to-r from-gray-50 to-gray-100 p-5 rounded-2xl border-2 border-gray-200">
        <form action="{{ route('reportes.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-4">
            <input type="hidden" name="rango" value="personalizado">
            <div class="flex-1">
                <label class="block text-xs font-bold text-gray-700 mb-2">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" 
                       class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-transparent">
            </div>
            <div class="flex-1">
                <label class="block text-xs font-bold text-gray-700 mb-2">Fecha Fin</label>
                <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" 
                       class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-transparent">
            </div>
            <button type="submit" 
                    class="px-8 py-2.5 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-bold rounded-xl hover:shadow-lg transition-all">
                Aplicar
            </button>
        </form>
    </div>

    <!-- KPIs Principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Total Ingresos -->
        <div class="card-fade bg-white p-6 rounded-2xl shadow-sm border border-gray-200 hover:shadow-lg transition-all">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-600">Ingresos Totales</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2">S/ {{ number_format($totalIngresos, 2) }}</h3>
                    <div class="mt-3 inline-flex items-center px-2.5 py-1 bg-green-50 text-green-700 rounded-lg text-xs font-semibold">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        100% del periodo
                    </div>
                </div>
                <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Ventas Realizadas -->
        <div class="card-fade bg-white p-6 rounded-2xl shadow-sm border border-gray-200 hover:shadow-lg transition-all">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-600">Ventas Realizadas</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $cantidadVentas }}</h3>
                    <div class="mt-3 inline-flex items-center px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-semibold">
                        Ticket: S/ {{ $cantidadVentas > 0 ? number_format($totalIngresos / $cantidadVentas, 2) : '0.00' }}
                    </div>
                </div>
                <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Ganancia Estimada -->
        <div class="card-fade bg-gradient-to-br from-[#218786] to-[#1a6d6c] p-6 rounded-2xl shadow-lg text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <p class="text-sm font-semibold text-teal-100">Ganancia Estimada (30%)</p>
                <h3 class="text-3xl font-bold mt-2">S/ {{ number_format($gananciaEstimada, 2) }}</h3>
                <div class="mt-3 inline-flex items-center px-2.5 py-1 bg-white/20 backdrop-blur-sm rounded-lg text-xs font-semibold">
                    Margen base aplicado
                </div>
            </div>
            <div class="absolute bottom-3 right-3">
                <svg class="w-12 h-12 text-white opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Sección de Detalles -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Productos Top -->
        <div class="card-fade bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                        Productos Destacados
                    </h3>
                    <a href="{{ route('repuestos.index') }}" 
                       class="text-xs font-semibold text-[#218786] hover:text-[#1a6d6c] flex items-center gap-1">
                        Ver todo
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="p-5">
                @forelse($topProductos as $producto)
                    <div class="mb-4 last:mb-0">
                        <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl transition-colors">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-[#218786] to-[#1a6d6c] rounded-xl flex items-center justify-center text-white font-bold shadow-md">
                                    {{ $loop->iteration }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-900 truncate">{{ $producto['nombre'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $producto['codigo'] }}</p>
                                </div>
                            </div>
                            <div class="text-right ml-4">
                                <p class="text-sm font-bold text-[#218786]">S/ {{ number_format($producto['total_generado'], 2) }}</p>
                                <p class="text-xs text-gray-500">{{ $producto['cantidad_vendida'] }} unds</p>
                            </div>
                        </div>
                        <div class="mt-2 w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-gradient-to-r from-[#218786] to-[#1a6d6c] h-2 rounded-full progress-bar shadow-sm" 
                                 style="width: {{ ($producto['total_generado'] / ($topProductos->first()['total_generado'] ?? 1)) * 100 }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-600 font-medium">Sin datos en este periodo</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Categorías y Métodos -->
        <div class="space-y-6">
            <!-- Mejores Categorías -->
            <div class="card-fade bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-purple-100">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Categorías Principales
                    </h3>
                </div>
                <div class="p-5">
                    @forelse($topCategorias as $categoria)
                        <div class="mb-4 last:mb-0">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-bold text-gray-900">{{ $categoria['nombre'] }}</span>
                                <span class="text-sm font-bold text-purple-600">S/ {{ number_format($categoria['total_generado'], 2) }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5">
                                <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2.5 rounded-full progress-bar shadow-sm" 
                                     style="width: {{ ($categoria['total_generado'] / ($totalIngresos > 0 ? $totalIngresos : 1)) * 100 }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $categoria['cantidad_items'] }} items vendidos</p>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-sm text-gray-500">Sin datos de categorías</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Métodos de Pago -->
            <div class="card-fade bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-green-100">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Métodos de Pago
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-4 bg-green-50 rounded-xl">
                            <div class="w-14 h-14 mx-auto bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mb-3 shadow-lg">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-600 mb-1">Efectivo</p>
                            <p class="text-2xl font-bold text-gray-900">100%</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-xl opacity-50">
                            <div class="w-14 h-14 mx-auto bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-3">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-600 mb-1">Digital</p>
                            <p class="text-2xl font-bold text-gray-900">0%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animación de las barras de progreso
    setTimeout(() => {
        document.querySelectorAll('.progress-bar').forEach(bar => {
            bar.style.width = bar.style.width;
        });
    }, 100);
});
</script>
@endpush
@endsection