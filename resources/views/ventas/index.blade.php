@extends('layouts.app')

@section('title', 'Ventas')
@section('page-title', 'Gestión de Ventas')
@section('page-description', 'Administra y monitorea todas las transacciones')

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
    
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="ventasManager()">

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
        <span class="font-medium text-gray-900">Ventas</span>
    </nav>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $totalVentas = $ventas->count();
            $ventasHoy = $ventas->filter(fn($v) => \Carbon\Carbon::parse($v->fecha)->isToday())->count();
            $ingresoTotal = $ventas->where('estado', 'pagado')->sum('total');
            $ventasPendientes = $ventas->where('estado', 'pendiente')->count();
        @endphp

        <!-- Total Ventas -->
        <div class="fade-in bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total Ventas</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalVentas }}</p>
            <p class="text-xs text-gray-500 mt-1">Transacciones totales</p>
        </div>

        <!-- Ventas Hoy -->
        <div class="fade-in bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Ventas Hoy</p>
            <p class="text-3xl font-bold text-purple-600 mt-1">{{ $ventasHoy }}</p>
            <p class="text-xs text-gray-500 mt-1">Del día actual</p>
        </div>

        <!-- Ingresos -->
        <div class="fade-in bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Ingresos</p>
            <p class="text-3xl font-bold text-green-600 mt-1">S/ {{ number_format($ingresoTotal, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Ventas completadas</p>
        </div>

        <!-- Pendientes -->
        <div class="fade-in bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="p-3 bg-amber-100 rounded-lg">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Pendientes</p>
            <p class="text-3xl font-bold text-amber-600 mt-1">{{ $ventasPendientes }}</p>
            <p class="text-xs text-gray-500 mt-1">Por completar</p>
        </div>
    </div>

    <!-- Filtros y Nueva Venta -->
    <div class="fade-in bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <!-- Filtros -->
            <div class="flex flex-col sm:flex-row gap-3 flex-1">
                <select x-model="filtroEstado" 
                        @change="aplicarFiltros()"
                        class="px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all">
                    <option value="todos">📋 Todos los estados</option>
                    <option value="pendiente">⏱️ Pendientes</option>
                    <option value="pagado">✅ Pagadas</option>
                    <option value="anulado">❌ Anuladas</option>
                </select>
                
                <input type="date" 
                       x-model="filtroFecha" 
                       @change="aplicarFiltros()"
                       class="px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all">
                
                <button @click="limpiarFiltros()" 
                        class="px-4 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
                    🔄 Limpiar
                </button>
            </div>

            <!-- Nueva Venta -->
            <a href="{{ route('ventas.create') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-bold rounded-lg hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nueva Venta
            </a>
        </div>
    </div>

    <!-- Lista de Ventas -->
    <div class="space-y-4">
        @forelse ($ventas as $index => $venta)
            <div class="fade-in bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all venta-item" 
                 data-estado="{{ $venta->estado }}" 
                 data-fecha="{{ \Carbon\Carbon::parse($venta->fecha)->format('Y-m-d') }}"
                 style="animation-delay: {{ $index * 0.05 }}s">
                
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <!-- Info Principal -->
                    <div class="flex-1">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-[#218786] to-[#1a6d6c] rounded-xl flex items-center justify-center shadow-md">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Venta #{{ $venta->id }}</h3>
                                    <p class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}
                                        <span class="text-gray-400">•</span>
                                        {{ \Carbon\Carbon::parse($venta->fecha)->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Badge Estado -->
                            @if($venta->estado === 'pagado')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-green-100 text-green-700 border border-green-200">
                                    ✓ Pagado
                                </span>
                            @elseif($venta->estado === 'pendiente')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">
                                    ⏱ Pendiente
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-red-100 text-red-700 border border-red-200">
                                    ✕ Anulado
                                </span>
                            @endif
                        </div>

                        <!-- Detalles -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">
                                    {{ $venta->cliente->nombre ?? 'Cliente General' }}
                                </span>
                            </div>
                            
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <span class="text-sm text-gray-600">
                                    {{ $venta->detalles->count() }} productos • {{ $venta->detalles->sum('cantidad') }} unidades
                                </span>
                            </div>
                            
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-lg font-bold text-green-600">
                                    S/ {{ number_format($venta->total, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="flex items-center gap-2">
                        <a href="{{ route('ventas.show', $venta) }}"
                           class="inline-flex items-center px-4 py-2 text-sm font-bold text-[#218786] bg-[#21878610] rounded-lg hover:bg-[#21878620] transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Ver
                        </a>

                        @if($venta->estado === 'pendiente')
                            <button onclick="cambiarEstado({{ $venta->id }}, 'pagado')" 
                                    class="inline-flex items-center px-4 py-2 text-sm font-bold text-green-600 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Pagado
                            </button>
                        @endif

                        <!-- Botón para abrir modal de acciones -->
                        <button @click="openAccionesModal({{ $venta->id }}, '{{ $venta->estado }}')"
                                class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <!-- Estado Vacío -->
            <div class="fade-in text-center py-20">
                <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">No hay ventas registradas</h3>
                <p class="text-gray-600 max-w-md mx-auto mb-6">
                    Comienza registrando tu primera venta para gestionar las transacciones de tu negocio
                </p>
                <a href="{{ route('ventas.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-bold rounded-lg hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Crear Primera Venta
                </a>
            </div>
        @endforelse
    </div>

    <!-- Modal de Acciones -->
    <div x-show="showAccionesModal" 
         x-cloak
         @click.self="closeAccionesModal()"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.stop
             x-show="showAccionesModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-[#218786] to-[#1a6d6c] p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Acciones</h3>
                            <p class="text-sm text-white/80" x-text="'Venta #' + selectedVentaId"></p>
                        </div>
                    </div>
                    <button @click="closeAccionesModal()" 
                            class="text-white/80 hover:text-white hover:bg-white/20 p-2 rounded-lg transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-3">
                <!-- Imprimir Boleta -->
                <!-- Imprimir Boleta -->
<a :href="`/ventas/${selectedVentaId}/boleta`" 
   target="_blank"
   class="flex items-center w-full p-4 bg-gradient-to-r from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 rounded-xl transition-all group border-2 border-blue-200 hover:border-blue-300">
    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
    </div>
    <div class="flex-1">
        <h4 class="font-bold text-gray-900 text-lg">Imprimir Boleta</h4>
        <p class="text-sm text-gray-600">Genera e imprime el comprobante</p>
    </div>
    <svg class="w-6 h-6 text-blue-500 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
</a>


                <!-- Anular Venta -->
                <button x-show="selectedVentaEstado !== 'anulado'" 
                        @click="confirmarAnularVenta()"
                        class="flex items-center w-full p-4 bg-gradient-to-r from-red-50 to-red-100 hover:from-red-100 hover:to-red-200 rounded-xl transition-all group border-2 border-red-200 hover:border-red-300">
                    <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900 text-lg">Anular Venta</h4>
                        <p class="text-sm text-gray-600">Cancela la venta y restaura stock</p>
                    </div>
                    <svg class="w-6 h-6 text-red-500 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            <!-- Footer -->
            <div class="p-6 bg-gray-50 border-t border-gray-200">
                <button @click="closeAccionesModal()" 
                        class="w-full px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold rounded-lg transition-colors">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('ventasManager', () => ({
        filtroEstado: 'todos',
        filtroFecha: '',
        showAccionesModal: false,
        selectedVentaId: null,
        selectedVentaEstado: null,
        
        aplicarFiltros() {
            const ventas = document.querySelectorAll('.venta-item');
            
            ventas.forEach(venta => {
                const estado = venta.dataset.estado;
                const fecha = venta.dataset.fecha;
                
                let mostrar = true;
                
                if (this.filtroEstado !== 'todos' && estado !== this.filtroEstado) {
                    mostrar = false;
                }
                
                if (this.filtroFecha && fecha !== this.filtroFecha) {
                    mostrar = false;
                }
                
                venta.style.display = mostrar ? 'block' : 'none';
            });
        },
        
        limpiarFiltros() {
            this.filtroEstado = 'todos';
            this.filtroFecha = '';
            this.aplicarFiltros();
        },
        
        openAccionesModal(ventaId, estado) {
            this.selectedVentaId = ventaId;
            this.selectedVentaEstado = estado;
            this.showAccionesModal = true;
            document.body.style.overflow = 'hidden';
        },
        
        closeAccionesModal() {
            this.showAccionesModal = false;
            document.body.style.overflow = '';
        },
        
        confirmarAnularVenta() {
            this.closeAccionesModal();
            anularVenta(this.selectedVentaId);
        }
    }));
});

// Cambiar estado
function cambiarEstado(ventaId, nuevoEstado) {
    Swal.fire({
        title: '¿Confirmar cambio?',
        text: `¿Marcar esta venta como ${nuevoEstado}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, cambiar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#218786',
        cancelButtonColor: '#6b7280'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Procesando...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(`/ventas/${ventaId}/estado`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ estado: nuevoEstado })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Actualizado!',
                        text: 'Estado cambiado exitosamente',
                        confirmButtonColor: '#218786'
                    }).then(() => location.reload());
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo actualizar el estado',
                    confirmButtonColor: '#218786'
                });
            });
        }
    });
}

// Anular venta
function anularVenta(ventaId) {
    Swal.fire({
        title: '¿Anular venta?',
        html: `
            <div class="text-left">
                <p class="mb-4">Esta acción restaurará el stock de los productos.</p>
                <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                    <p class="text-sm text-red-700">
                        <strong>⚠️ Advertencia:</strong> Esta acción no se puede deshacer.
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
            Swal.fire({
                title: 'Procesando...',
                text: 'Anulando venta y restaurando stock',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(`/ventas/${ventaId}/anular`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Venta anulada',
                        text: 'Stock restaurado correctamente',
                        confirmButtonColor: '#218786'
                    }).then(() => location.reload());
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo anular la venta',
                    confirmButtonColor: '#218786'
                });
            });
        }
    });
}

// Cerrar modal con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        Alpine.store('ventas')?.closeAccionesModal?.();
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
            timerProgressBar: true
        });
    @endif
});
</script>
@endpush
@endsection
