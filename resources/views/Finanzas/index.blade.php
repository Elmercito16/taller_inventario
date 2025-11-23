@extends('layouts.app')

@section('title', 'Finanzas')
@section('page-title', 'Finanzas y Flujo de Caja')

@section('content')
<div class="space-y-6" x-data="{ showGastoModal: false }">

    <!-- Filtros y Botones -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        
        <!-- Filtros de Fecha -->
        <form action="{{ route('finanzas.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <select name="filtro" onchange="this.form.submit()" class="border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="hoy" {{ $filtro == 'hoy' ? 'selected' : '' }}>Hoy</option>
                <option value="semana" {{ $filtro == 'semana' ? 'selected' : '' }}>Esta Semana</option>
                <option value="mes_actual" {{ $filtro == 'mes_actual' ? 'selected' : '' }}>Este Mes</option>
                <option value="personalizado" {{ $filtro == 'personalizado' ? 'selected' : '' }}>Personalizado</option>
            </select>

            @if($filtro == 'personalizado')
                <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="border-gray-300 rounded-lg text-sm">
                <span class="text-gray-500">-</span>
                <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" class="border-gray-300 rounded-lg text-sm">
                <button type="submit" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 text-xs font-medium">Filtrar</button>
            @endif
        </form>

        <!-- Botones de Acción -->
        <div class="flex gap-3 w-full lg:w-auto">
            <a href="{{ route('ventas.create') }}" class="flex-1 lg:flex-none inline-flex justify-center items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Nuevo Ingreso (Venta)
            </a>
            <button @click="showGastoModal = true" class="flex-1 lg:flex-none inline-flex justify-center items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                Registrar Gasto
            </button>
        </div>
    </div>

    <!-- Tarjetas de Resumen -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Ingresos -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-green-100 relative overflow-hidden">
            <div class="absolute right-0 top-0 h-full w-2 bg-green-500"></div>
            <p class="text-sm font-medium text-gray-500 mb-1">Total Ingresos</p>
            <h3 class="text-3xl font-bold text-gray-900">S/ {{ number_format($totalIngresos, 2) }}</h3>
            <div class="mt-4 flex items-center text-sm text-green-600">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                <span>Ventas registradas</span>
            </div>
        </div>

        <!-- Gastos -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-red-100 relative overflow-hidden">
            <div class="absolute right-0 top-0 h-full w-2 bg-red-500"></div>
            <p class="text-sm font-medium text-gray-500 mb-1">Total Gastos</p>
            <h3 class="text-3xl font-bold text-gray-900">S/ {{ number_format($totalGastos, 2) }}</h3>
            <div class="mt-4 flex items-center text-sm text-red-600">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                <span>Egresos registrados</span>
            </div>
        </div>

        <!-- Balance -->
        <div class="bg-white p-6 rounded-xl shadow-sm border {{ $balance >= 0 ? 'border-primary-100' : 'border-red-100' }} relative overflow-hidden">
            <div class="absolute right-0 top-0 h-full w-2 {{ $balance >= 0 ? 'bg-primary-500' : 'bg-red-500' }}"></div>
            <p class="text-sm font-medium text-gray-500 mb-1">Balance Neto</p>
            <h3 class="text-3xl font-bold {{ $balance >= 0 ? 'text-primary-600' : 'text-red-600' }}">
                S/ {{ number_format($balance, 2) }}
            </h3>
            <p class="mt-4 text-xs text-gray-400">Ingresos - Gastos</p>
        </div>
    </div>

    <!-- Caja de Flujo (Tabla) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Flujo de Caja Detallado</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($cajaFlujo as $movimiento)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($movimiento['fecha'])->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $movimiento['descripcion'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($movimiento['tipo'] == 'ingreso')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Ingreso
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Gasto
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold {{ $movimiento['tipo'] == 'ingreso' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $movimiento['tipo'] == 'ingreso' ? '+' : '-' }} S/ {{ number_format($movimiento['monto'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                No hay movimientos registrados en este periodo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para Registrar Gasto -->
    <div x-show="showGastoModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showGastoModal = false">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="{{ route('finanzas.storeGasto') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Registrar Nuevo Gasto</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                <input type="text" name="descripcion" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Ej: Pago de luz, Alquiler...">
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Monto (S/)</label>
                                    <input type="number" name="monto" step="0.01" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" placeholder="0.00">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                                    <input type="date" name="fecha" value="{{ date('Y-m-d') }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Categoría (Opcional)</label>
                                <select name="categoria" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Seleccionar...</option>
                                    <option value="Servicios">Servicios (Luz/Agua/Internet)</option>
                                    <option value="Alquiler">Alquiler</option>
                                    <option value="Personal">Personal / Planilla</option>
                                    <option value="Mantenimiento">Mantenimiento</option>
                                    <option value="Otros">Otros</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Guardar Gasto
                        </button>
                        <button type="button" @click="showGastoModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection