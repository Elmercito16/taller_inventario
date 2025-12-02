@extends('layouts.app')

@section('title', 'Finanzas')

@push('styles')
<style>
    .card-slide { animation: slideUp 0.4s ease-out; }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .gradient-brand { background: linear-gradient(135deg, #218786 0%, #1a6d6c 100%); }
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="{ showGastoModal: false }">

    <!-- Header con Título -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Finanzas</h1>
            <p class="text-sm text-gray-600 mt-1">Control de ingresos y gastos</p>
        </div>
    </div>

    <!-- Filtros y Acciones -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            
            <!-- Filtros de Fecha -->
            <form action="{{ route('finanzas.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1">
                <div class="flex items-center gap-2 text-gray-600">
                    <svg class="w-5 h-5 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm font-semibold">Periodo:</span>
                </div>

                <select name="filtro" onchange="this.form.submit()" 
                        class="px-4 py-2.5 text-sm font-semibold border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-transparent">
                    <option value="hoy" {{ $filtro == 'hoy' ? 'selected' : '' }}>Hoy</option>
                    <option value="semana" {{ $filtro == 'semana' ? 'selected' : '' }}>Esta Semana</option>
                    <option value="mes_actual" {{ $filtro == 'mes_actual' ? 'selected' : '' }}>Este Mes</option>
                    <option value="personalizado" {{ $filtro == 'personalizado' ? 'selected' : '' }}>Personalizado</option>
                </select>

                @if($filtro == 'personalizado')
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                        <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" 
                               class="px-3 py-2 text-sm border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-transparent">
                        <span class="text-gray-400 text-center sm:text-left">hasta</span>
                        <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" 
                               class="px-3 py-2 text-sm border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-transparent">
                        <button type="submit" 
                                class="px-4 py-2 bg-[#218786] hover:bg-[#1a6d6c] text-white font-semibold rounded-xl transition-all shadow-md">
                            Filtrar
                        </button>
                    </div>
                @endif
            </form>

            <!-- Botones de Acción -->
            <div class="flex flex-col sm:flex-row gap-3">
                <button @click="showGastoModal = true" 
                        class="inline-flex items-center justify-center px-5 py-2.5 bg-white border-2 border-red-200 text-red-600 font-semibold rounded-xl hover:bg-red-50 transition-all shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                    Registrar Gasto
                </button>

                <a href="{{ route('ventas.create') }}" 
                   class="inline-flex items-center justify-center px-5 py-2.5 gradient-brand text-white font-semibold rounded-xl hover:shadow-xl transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nuevo Ingreso
                </a>
            </div>
        </div>
    </div>

    <!-- KPIs Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Ingresos -->
        <div class="card-slide bg-white p-6 rounded-2xl shadow-sm border border-gray-200 hover:shadow-lg transition-all">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 bg-green-50 text-green-700 rounded-lg text-xs font-bold">
                    +100%
                </span>
            </div>
            <p class="text-sm font-semibold text-gray-600">Ingresos Totales</p>
            <h3 class="text-3xl font-bold text-gray-900 mt-2">S/ {{ number_format($totalIngresos, 2) }}</h3>
            <p class="text-xs text-gray-500 mt-2">Ventas registradas</p>
        </div>

        <!-- Gastos -->
        <div class="card-slide bg-white p-6 rounded-2xl shadow-sm border border-gray-200 hover:shadow-lg transition-all">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                    </svg>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 bg-red-50 text-red-700 rounded-lg text-xs font-bold">
                    Egresos
                </span>
            </div>
            <p class="text-sm font-semibold text-gray-600">Gastos Totales</p>
            <h3 class="text-3xl font-bold text-gray-900 mt-2">S/ {{ number_format($totalGastos, 2) }}</h3>
            <p class="text-xs text-gray-500 mt-2">Gastos del periodo</p>
        </div>

        <!-- Balance -->
        <div class="card-slide gradient-brand p-6 rounded-2xl shadow-lg text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-semibold text-teal-100">Balance Neto</p>
                <h3 class="text-3xl font-bold mt-2">S/ {{ number_format($balance, 2) }}</h3>
                <p class="text-xs text-teal-100 mt-2">Ingresos - Gastos</p>
            </div>
        </div>
    </div>

    <!-- Tabla de Movimientos -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Flujo de Caja Detallado
                </h3>
                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs font-bold">
                    {{ count($cajaFlujo) }} registros
                </span>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Monto</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($cajaFlujo as $movimiento)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::parse($movimiento['fecha'])->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($movimiento['fecha'])->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-2 h-2 rounded-full mr-3 {{ $movimiento['tipo'] == 'ingreso' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                                    <span class="text-sm font-medium text-gray-900">{{ $movimiento['descripcion'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($movimiento['tipo'] == 'ingreso')
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-green-100 text-green-700">
                                        Ingreso
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-red-100 text-red-700">
                                        Gasto
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-base font-bold {{ $movimiento['tipo'] == 'ingreso' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $movimiento['tipo'] == 'ingreso' ? '+' : '-' }} S/ {{ number_format($movimiento['monto'], 2) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-600 font-semibold">Sin movimientos en este periodo</p>
                                    <p class="text-sm text-gray-500 mt-1">Cambia los filtros de fecha</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Registrar Gasto -->
    <div x-show="showGastoModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-900 opacity-75 backdrop-blur-sm" @click="showGastoModal = false"></div>

            <!-- Modal Content -->
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                
                <form action="{{ route('finanzas.storeGasto') }}" method="POST">
                    @csrf
                    
                    <!-- Header -->
                    <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-red-50 to-white">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-red-100 rounded-xl">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Registrar Gasto</h3>
                                <p class="text-sm text-gray-600">Ingresa los detalles del egreso</p>
                            </div>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-5 space-y-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Monto (S/)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3 text-gray-500 font-bold text-lg">S/</span>
                                <input type="number" name="monto" step="0.01" required 
                                       class="w-full pl-12 pr-4 py-3 text-lg font-bold text-red-600 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                                       placeholder="0.00">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Descripción</label>
                            <input type="text" name="descripcion" required 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-transparent" 
                                   placeholder="Ej: Pago de servicios">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Fecha</label>
                                <input type="date" name="fecha" value="{{ date('Y-m-d') }}" required 
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Categoría</label>
                                <select name="categoria" 
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-transparent">
                                    <option value="">Sin categoría</option>
                                    <option value="Servicios">Servicios</option>
                                    <option value="Alquiler">Alquiler</option>
                                    <option value="Planilla">Planilla</option>
                                    <option value="Mantenimiento">Mantenimiento</option>
                                    <option value="Mercadería">Mercadería</option>
                                    <option value="Otros">Otros</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col-reverse sm:flex-row justify-end gap-3">
                        <button type="button" @click="showGastoModal = false" 
                                class="px-6 py-2.5 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-100 transition-all">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl">
                            Confirmar Gasto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection