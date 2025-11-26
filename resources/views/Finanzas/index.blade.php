@extends('layouts.app')

@section('title', 'Finanzas')
@section('page-title', 'Finanzas y Flujo de Caja')

@push('styles')
<style>
    /* Color personalizado solicitado */
    .text-brand { color: #218786; }
    .bg-brand { background-color: #218786; }
    .bg-brand-light { background-color: #e0f2f1; } /* Un tono muy suave para fondos */
    .border-brand { border-color: #218786; }
    .hover\:bg-brand-dark:hover { background-color: #1a6b6a; }
    .focus\:ring-brand:focus { --tw-ring-color: #218786; }
    
    /* Gradiente suave para tarjetas */
    .card-gradient { background: linear-gradient(145deg, #ffffff, #f9fafb); }
</style>
@endpush

@section('content')
<div class="space-y-8" x-data="{ showGastoModal: false }">

    <!-- Encabezado: Filtros y Acciones -->
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6">
        
        <!-- Filtros de Fecha Estilizados -->
        <div class="w-full xl:w-auto bg-white p-1.5 rounded-xl shadow-sm border border-gray-200 flex flex-col sm:flex-row items-center gap-2">
            <form action="{{ route('finanzas.index') }}" method="GET" class="contents" id="filterForm">
                <div class="relative w-full sm:w-48">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <select name="filtro" onchange="this.form.submit()" 
                            class="block w-full pl-10 pr-10 py-2.5 text-sm border-none rounded-lg bg-gray-50 focus:ring-2 focus:ring-brand text-gray-700 font-medium cursor-pointer hover:bg-gray-100 transition-colors">
                        <option value="hoy" {{ $filtro == 'hoy' ? 'selected' : '' }}>Hoy</option>
                        <option value="semana" {{ $filtro == 'semana' ? 'selected' : '' }}>Esta Semana</option>
                        <option value="mes_actual" {{ $filtro == 'mes_actual' ? 'selected' : '' }}>Este Mes</option>
                        <option value="personalizado" {{ $filtro == 'personalizado' ? 'selected' : '' }}>Personalizado</option>
                    </select>
                </div>

                @if($filtro == 'personalizado')
                    <div class="flex items-center gap-2 w-full sm:w-auto px-2 animate-fade-in">
                        <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="w-full sm:w-32 text-sm border-gray-200 rounded-lg focus:ring-brand focus:border-brand">
                        <span class="text-gray-400">a</span>
                        <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" class="w-full sm:w-32 text-sm border-gray-200 rounded-lg focus:ring-brand focus:border-brand">
                        <button type="submit" class="p-2.5 bg-brand text-white rounded-lg hover:bg-brand-dark transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </button>
                    </div>
                @endif
            </form>
        </div>

        <!-- Botones de Acción Principales -->
        <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto">
            <button @click="showGastoModal = true" 
                    class="group flex-1 sm:flex-none inline-flex items-center justify-center px-5 py-3 bg-white border-2 border-red-100 text-red-600 font-semibold rounded-xl hover:bg-red-50 hover:border-red-200 transition-all duration-200 shadow-sm">
                <div class="mr-2 p-1 bg-red-100 rounded-lg group-hover:bg-red-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                </div>
                Registrar Gasto
            </button>

            <a href="{{ route('ventas.create') }}" 
               class="group flex-1 sm:flex-none inline-flex items-center justify-center px-5 py-3 bg-brand text-white font-semibold rounded-xl hover:bg-brand-dark transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                <div class="mr-2 p-1 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                </div>
                Nuevo Ingreso
            </a>
        </div>
    </div>

    <!-- KPIs / Tarjetas de Resumen -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Tarjeta Ingresos -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
            </div>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-50 rounded-xl text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Ingresos Totales</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">S/ {{ number_format($totalIngresos, 2) }}</h3>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-50">
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">
                    + Ventas
                </span>
            </div>
        </div>

        <!-- Tarjeta Gastos -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            </div>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-red-50 rounded-xl text-red-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Gastos Totales</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">S/ {{ number_format($totalGastos, 2) }}</h3>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-50">
                <span class="text-xs font-medium text-red-600 bg-red-50 px-2 py-1 rounded-full">
                    - Egresos
                </span>
            </div>
        </div>

        <!-- Tarjeta Balance -->
        <div class="bg-gradient-to-br {{ $balance >= 0 ? 'from-[#218786] to-[#1d7874]' : 'from-red-600 to-red-700' }} p-6 rounded-2xl shadow-lg text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-4 mb-2">
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    </div>
                    <p class="text-sm font-medium text-white/80">Balance Neto</p>
                </div>
                <h3 class="text-4xl font-bold mt-2">S/ {{ number_format($balance, 2) }}</h3>
                <p class="text-xs text-white/60 mt-1">Rentabilidad del periodo seleccionado</p>
            </div>
        </div>
    </div>

    <!-- Tabla de Flujo de Caja -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                Movimientos
            </h3>
            <span class="text-xs font-medium text-gray-500 bg-white border border-gray-200 px-2 py-1 rounded-md">
                {{ count($cajaFlujo) }} registros
            </span>
        </div>
        
        <div class="overflow-x-auto custom-scrollbar">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha y Hora</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Categoría</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Monto</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($cajaFlujo as $movimiento)
                        <tr class="hover:bg-gray-50/80 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex flex-col">
                                    <span class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($movimiento['fecha'])->format('d/m/Y') }}</span>
                                    <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($movimiento['fecha'])->format('H:i A') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="flex items-center">
                                    <div class="w-2 h-2 rounded-full mr-3 {{ $movimiento['tipo'] == 'ingreso' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                                    {{ $movimiento['descripcion'] }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($movimiento['tipo'] == 'ingreso')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        Venta
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                        Gasto
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="font-bold text-base {{ $movimiento['tipo'] == 'ingreso' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $movimiento['tipo'] == 'ingreso' ? '+' : '-' }} S/ {{ number_format($movimiento['monto'], 2) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-50 rounded-full p-4 mb-3">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <p class="text-gray-500 font-medium">No hay movimientos en este periodo</p>
                                    <p class="text-gray-400 text-sm mt-1">Intenta cambiar los filtros de fecha</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para Registrar Gasto (Diseño Mejorado) -->
    <div x-show="showGastoModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showGastoModal = false">
                <div class="absolute inset-0 bg-gray-900 opacity-75 backdrop-blur-sm"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <form action="{{ route('finanzas.storeGasto') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-gray-900">Registrar Salida de Dinero</h3>
                                <p class="text-sm text-gray-500 mt-1">Ingresa los detalles del gasto para mantener la caja cuadrada.</p>
                                
                                <div class="mt-6 space-y-5">
                                    <!-- Monto con énfasis -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Monto del Gasto</label>
                                        <div class="relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-lg font-bold">S/</span>
                                            </div>
                                            <input type="number" name="monto" step="0.01" required 
                                                   class="block w-full pl-10 pr-4 py-3 text-lg font-bold text-red-600 border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" 
                                                   placeholder="0.00">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                        <input type="text" name="descripcion" required 
                                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-brand focus:border-brand py-2.5" 
                                               placeholder="Ej: Pago recibo de luz del mes">
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                                            <input type="date" name="fecha" value="{{ date('Y-m-d') }}" required 
                                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-brand focus:border-brand py-2.5">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                                            <select name="categoria" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-brand focus:border-brand py-2.5">
                                                <option value="">Seleccionar...</option>
                                                <option value="Servicios">Servicios</option>
                                                <option value="Alquiler">Alquiler</option>
                                                <option value="Planilla">Planilla</option>
                                                <option value="Mantenimiento">Mantenimiento</option>
                                                <option value="Mercadería">Compra Mercadería</option>
                                                <option value="Otros">Otros</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-2.5 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Confirmar Gasto
                        </button>
                        <button type="button" @click="showGastoModal = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection