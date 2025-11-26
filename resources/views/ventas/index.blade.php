@extends('layouts.app')

@section('title', 'Ventas')
@section('page-title', 'Gestión de Ventas')
@section('page-description', 'Administra y monitorea todas las transacciones de venta')

@section('breadcrumbs')
<nav class="flex items-center space-x-2 text-sm text-gray-500">
    <a href="{{ route('dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
    <span class="font-medium text-gray-900">Ventas</span>
</nav>
@endsection

@push('styles')
<style>
    /* Animaciones personalizadas */
    .sale-card {
        animation: slideUp 0.5s ease-out;
        animation-fill-mode: both;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .sale-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .sale-card:nth-child(1) { animation-delay: 0.1s; }
    .sale-card:nth-child(2) { animation-delay: 0.2s; }
    .sale-card:nth-child(3) { animation-delay: 0.3s; }
    .sale-card:nth-child(4) { animation-delay: 0.4s; }
    .sale-card:nth-child(5) { animation-delay: 0.5s; }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Estados de venta */
    .estado-pendiente { @apply bg-yellow-100 text-yellow-800 border-yellow-200; }
    .estado-pagado { @apply bg-green-100 text-green-800 border-green-200; }
    .estado-anulado { @apply bg-red-100 text-red-800 border-red-200; }
    
    /* Modal mejorado */
    .modal-backdrop {
        backdrop-filter: blur(4px);
        background: rgba(0, 0, 0, 0.5);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Stats Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $totalVentas = $ventas->count();
            $ventasHoy = $ventas->filter(function($venta) { 
                return \Carbon\Carbon::parse($venta->fecha)->isToday(); 
            })->count();
            $ingresoTotal = $ventas->where('estado', 'pagado')->sum('total');
            $ventasPendientes = $ventas->where('estado', 'pendiente')->count();
        @endphp

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Ventas</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalVentas }}</p>
                </div>
                <div class="p-3 bg-primary-100 rounded-full">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Ventas Hoy</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $ventasHoy }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Ingresos Totales</p>
                    <p class="text-3xl font-bold text-green-600">S/ {{ number_format($ingresoTotal, 2) }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pendientes</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $ventasPendientes }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Acciones -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <!-- Filtros -->
            <div class="flex flex-col sm:flex-row gap-3" x-data="{ filtroEstado: 'todos', filtroFecha: '' }">
                <select x-model="filtroEstado" @change="filtrarVentas()" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="todos">Todos los estados</option>
                    <option value="pendiente">Pendientes</option>
                    <option value="pagado">Pagadas</option>
                    <option value="anulado">Anuladas</option>
                </select>
                
                <input type="date" x-model="filtroFecha" @change="filtrarVentas()" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Filtrar por fecha">
                
                <button @click="limpiarFiltros()" class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Limpiar filtros
                </button>
            </div>

            <!-- Acciones -->
            <div class="flex flex-col sm:flex-row gap-3">
    <a href="{{ route('ventas.create') }}" 
       class="inline-flex items-center justify-center px-4 py-2.5 bg-[#1b8c72ff] text-white font-medium rounded-lg hover:bg-[#15785f] transition-all duration-200 shadow-sm hover:shadow-md">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Nueva Venta
    </a>
    
    <button onclick="generarReporte()" 
            class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-[#1b8c72ff] text-[#1b8c72ff] font-medium rounded-lg hover:bg-[#1b8c72ff] hover:text-white transition-colors duration-200 shadow-sm hover:shadow-md">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Generar Reporte
    </button>
</div>

        </div>
    </div>

    <!-- Lista de Ventas -->
    <div class="space-y-4" id="ventas-container">
        @forelse ($ventas as $venta)
            <div class="sale-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 venta-item" 
                 data-estado="{{ $venta->estado }}" 
                 data-fecha="{{ \Carbon\Carbon::parse($venta->fecha)->format('Y-m-d') }}">
                
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <!-- Información Principal -->
                    <div class="flex-1">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        Venta #{{ $venta->id }}
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Estado -->
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border estado-{{ $venta->estado }}">
                                @if($venta->estado === 'pendiente')
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    Pendiente
                                @elseif($venta->estado === 'pagado')
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Pagado
                                @else
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    Anulado
                                @endif
                            </span>
                        </div>

                        <!-- Información del Cliente y Total -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="font-medium">{{ $venta->cliente->nombre ?? 'Cliente no especificado' }}</span>
                            </div>
                            
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                                <span class="font-bold text-green-600 text-lg">S/ {{ number_format($venta->total, 2) }}</span>
                            </div>
                        </div>

                        <!-- Resumen de productos -->
                        <div class="mt-3 text-sm text-gray-500">
                            <span>{{ $venta->detalles->count() }} {{ Str::plural('producto', $venta->detalles->count()) }}</span>
                            •
                            <span>{{ $venta->detalles->sum('cantidad') }} {{ Str::plural('artículo', $venta->detalles->sum('cantidad')) }}</span>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="flex items-center space-x-2">
                        <button onclick="openModal({{ $venta->id }})"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Ver Detalle
                        </button>

                        @if($venta->estado === 'pendiente')
                            <button onclick="cambiarEstado({{ $venta->id }}, 'pagado')" 
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-green-600 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Marcar Pagado
                            </button>
                        @endif

                        <!-- Dropdown de más acciones -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                                
                                <div class="py-1">
                                    <button onclick="imprimirVenta({{ $venta->id }})" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                        Imprimir
                                    </button>
                                    
                                    @if($venta->estado !== 'anulado')
                                        <button onclick="anularVenta({{ $venta->id }})" class="flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Anular Venta
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <!-- Estado vacío -->
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No hay ventas registradas</h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto">
                    Registra tu primera venta para comenzar a gestionar las transacciones de tu taller.
                </p>
                <a href="{{ route('ventas.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Crear primera venta
                </a>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal mejorado -->
<div id="detailModal" class="fixed inset-0 hidden z-50 flex items-center justify-center modal-backdrop px-4" onclick="if(event.target === this) closeModal()">
    <div id="modalContent" class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden shadow-2xl transform scale-95 opacity-0 transition-all duration-300">
        <!-- Header del modal -->
        <div class="bg-gradient-to-r from-primary-500 to-primary-600 px-6 py-4 text-white">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold">Detalle de la Venta</h2>
                <button onclick="closeModal()" class="p-2 hover:bg-white hover:bg-opacity-20 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Contenido del modal -->
        <div class="overflow-y-auto max-h-[calc(90vh-100px)]">
            <div id="modalVentaDetails" class="p-6">
                <!-- Los detalles se cargarán aquí -->
                <div class="flex items-center justify-center h-32">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Función para abrir el modal con detalles
function openModal(ventaId) {
    const modal = document.getElementById("detailModal");
    const modalContent = document.getElementById("modalContent");
    const modalVentaDetails = document.getElementById("modalVentaDetails");

    // Mostrar el modal
    modal.classList.remove("hidden");
    setTimeout(() => {
        modalContent.classList.remove("scale-95", "opacity-0");
        modalContent.classList.add("scale-100", "opacity-100");
    }, 10);

    // Cargar detalles con fetch
    fetch(`/ventas/${ventaId}/detalles`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al cargar los detalles');
            }
            return response.json();
        })
        .then(data => {
            modalVentaDetails.innerHTML = `
                <div class="space-y-6">
                    <!-- Información principal -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Información General</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Fecha:</span>
                                    <span class="font-medium">${data.fecha}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Estado:</span>
                                    <span class="font-medium px-2 py-1 rounded-full text-xs ${data.estado === 'pagado' ? 'bg-green-100 text-green-800' : data.estado === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'}">${data.estado}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total:</span>
                                    <span class="font-bold text-green-600">S/ ${data.total}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Cliente</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Nombre:</span>
                                    <span class="font-medium">${data.cliente.nombre}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">DNI:</span>
                                    <span class="font-medium">${data.cliente.dni || 'No especificado'}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Teléfono:</span>
                                    <span class="font-medium">${data.cliente.telefono || 'No especificado'}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Productos vendidos -->
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-4">Productos Vendidos</h4>
                        <div class="bg-gray-50 rounded-lg overflow-hidden">
                            <div class="px-4 py-3 bg-gray-100 border-b border-gray-200">
                                <div class="grid grid-cols-4 gap-4 text-xs font-medium text-gray-700 uppercase tracking-wider">
                                    <div>Producto</div>
                                    <div class="text-center">Cantidad</div>
                                    <div class="text-center">Precio Unit.</div>
                                    <div class="text-right">Subtotal</div>
                                </div>
                            </div>
                            <div class="divide-y divide-gray-200">
                                ${data.detalles.map(detalle => `
                                    <div class="px-4 py-3">
                                        <div class="grid grid-cols-4 gap-4 items-center">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">${detalle.repuesto.nombre}</p>
                                                <p class="text-xs text-gray-500">${detalle.repuesto.codigo}</p>
                                            </div>
                                            <div class="text-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    ${detalle.cantidad}
                                                </span>
                                            </div>
                                            <div class="text-center text-sm text-gray-900">
                                                S/ ${parseFloat(detalle.precio_unitario).toFixed(2)}
                                            </div>
                                            <div class="text-right">
                                                <span class="text-sm font-semibold text-gray-900">S/ ${parseFloat(detalle.subtotal).toFixed(2)}</span>
                                            </div>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                            
                            <!-- Total -->
                            <div class="px-4 py-3 bg-gray-100 border-t border-gray-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-base font-semibold text-gray-900">Total de la Venta:</span>
                                    <span class="text-xl font-bold text-green-600">S/ ${parseFloat(data.total).toFixed(2)}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones del modal -->
                    <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200">
                        <button onclick="imprimirVenta(${data.id})" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Imprimir
                        </button>
                        
                        ${data.estado === 'pendiente' ? `
                            <button onclick="cambiarEstado(${data.id}, 'pagado'); closeModal();" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Marcar como Pagado
                            </button>
                        ` : ''}
                        
                        ${data.estado !== 'anulado' ? `
                            <button onclick="anularVenta(${data.id}); closeModal();" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Anular Venta
                            </button>
                        ` : ''}
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error:', error);
            modalVentaDetails.innerHTML = `
                <div class="text-center py-8">
                    <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Error al cargar detalles</h3>
                    <p class="text-gray-500">No se pudieron cargar los detalles de la venta. Inténtalo de nuevo.</p>
                    <button onclick="closeModal()" class="mt-4 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Cerrar
                    </button>
                </div>
            `;
        });
}

// Función para cerrar el modal
function closeModal() {
    const modal = document.getElementById("detailModal");
    const modalContent = document.getElementById("modalContent");

    modalContent.classList.remove("scale-100", "opacity-100");
    modalContent.classList.add("scale-95", "opacity-0");
    setTimeout(() => modal.classList.add("hidden"), 300);
}

// Función para cambiar estado de venta
function cambiarEstado(ventaId, nuevoEstado) {
    const estadoTexto = nuevoEstado === 'pagado' ? 'pagada' : nuevoEstado;
    
    Swal.fire({
        title: '¿Cambiar estado?',
        text: `¿Estás seguro de marcar esta venta como ${estadoTexto}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, cambiar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#218786',
        cancelButtonColor: '#6b7280'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Procesando...',
                text: 'Actualizando estado de la venta',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Hacer petición para cambiar estado
            fetch(`/ventas/${ventaId}/estado`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    estado: nuevoEstado
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Estado actualizado',
                        text: `La venta ha sido marcada como ${estadoTexto}`,
                        confirmButtonColor: '#218786'
                    }).then(() => {
                        location.reload(); // Recargar la página para mostrar cambios
                    });
                } else {
                    throw new Error(data.message || 'Error al actualizar');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo actualizar el estado de la venta',
                    confirmButtonColor: '#218786'
                });
            });
        }
    });
}

// Función para anular venta
function anularVenta(ventaId) {
    Swal.fire({
        title: '¿Anular venta?',
        html: `
            <div class="text-left">
                <p class="mb-4">¿Estás seguro de que deseas anular esta venta?</p>
                <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                    <p class="text-sm text-red-700">
                        <strong>⚠️ Advertencia:</strong> Esta acción restaurará el stock de los productos vendidos y no se puede deshacer.
                    </p>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, anular',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280'
    }).then((result) => {
        if (result.isConfirmed) {
            // Aquí implementarías la lógica para anular la venta
            Swal.fire({
                title: 'Procesando...',
                text: 'Anulando venta y restaurando stock',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Simulación - reemplazar con petición real
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Venta anulada',
                    text: 'La venta ha sido anulada y el stock restaurado',
                    confirmButtonColor: '#218786'
                }).then(() => {
                    location.reload();
                });
            }, 1500);
        }
    });
}

// Función para imprimir venta
function imprimirVenta(ventaId) {
    Swal.fire({
        title: 'Generar comprobante',
        text: 'Selecciona el tipo de comprobante a imprimir',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Boleta',
        
        confirmButtonColor: '#218786',
        cancelButtonColor: '#059669'
    }).then((result) => {
        if (result.isConfirmed) {
            // Generar boleta
            window.open(`/ventas/${ventaId}/boleta`, '_blank');
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            // Generar ticket
        }
    });
}

// Función para generar reporte
function generarReporte() {
    Swal.fire({
        title: 'Generar Reporte de Ventas',
        text: 'Esta funcionalidad estará disponible próximamente',
        icon: 'info',
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#218786'
    });
}

// Funciones de filtrado
function filtrarVentas() {
    // Implementar lógica de filtrado
    console.log('Filtrar ventas...');
}

function limpiarFiltros() {
    // Limpiar filtros
    const selects = document.querySelectorAll('select, input[type="date"]');
    selects.forEach(element => {
        element.value = element.tagName === 'SELECT' ? 'todos' : '';
    });
    
    // Mostrar todas las ventas
    const ventas = document.querySelectorAll('.venta-item');
    ventas.forEach(venta => {
        venta.style.display = 'block';
    });
}

// Cerrar modal con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('detailModal');
        if (!modal.classList.contains('hidden')) {
            closeModal();
        }
    }
});

// Animaciones de entrada
document.addEventListener('DOMContentLoaded', function() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });
    
    document.querySelectorAll('.sale-card').forEach(card => {
        card.style.animationPlayState = 'paused';
        observer.observe(card);
    });
});
</script>
@endpush

@endsection