@extends('layouts.app')

@section('title', 'Detalle de Venta')
@section('page-title', 'Detalle de Venta #' . $venta->id)
@section('page-description', 'Información completa de la transacción de venta')

@section('breadcrumbs')
<nav class="flex items-center space-x-2 text-sm text-gray-500">
    <a href="{{ route('dashboard') }}" class="hover:text-primary-600 transition-colors flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
        </svg>
        Dashboard
    </a>
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
    <a href="{{ route('ventas.index') }}" class="hover:text-primary-600 transition-colors">Ventas</a>
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
    <span class="font-medium text-gray-900">Venta #{{ $venta->id }}</span>
</nav>
@endsection

@push('styles')
<style>
    .hover-lift {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .hover-lift:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    
    .section-card {
        animation: slideUp 0.5s ease-out;
        animation-fill-mode: both;
    }
    
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
</style>
@endpush

@section('content')
{{-- ✅ CORRECCIÓN 1: Añadido un ID único al contenedor --}}
<div class="space-y-6" id="show-venta-content">
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 bg-gradient-to-r from-primary-500 to-primary-600">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Detalle de Venta #{{ $venta->id }}
                    </h1>
                    <p class="text-base text-gray-600">
                        Información completa de la transacción
                    </p>
                </div>
            </div>
            <a href="{{ route('ventas.index') }}" 
               class="flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors w-full sm:w-auto justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total de Venta</p>
                    <p class="text-2xl font-bold text-green-600">S/ {{ number_format($venta->total, 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Monto facturado</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Productos</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $venta->detalles->count() }}</p>
                    <p class="text-xs text-gray-500 mt-1">Tipos diferentes</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Cantidad Total</p>
                    <p class="text-2xl font-bold text-primary-600">{{ $venta->detalles->sum('cantidad') }}</p>
                    <p class="text-xs text-gray-500 mt-1">Artículos vendidos</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Estado</p>
                    <div class="mt-1">
                        @if($venta->estado === 'pagado')
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-md">Pagado</span>
                        @elseif($venta->estado === 'pendiente')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-md">Pendiente</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-md">{{ ucfirst($venta->estado) }}</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Estado actual</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Información del Cliente</h2>
                    <p class="text-sm text-gray-600">Datos del comprador</p>
                </div>
            </div>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-600">Cliente:</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $venta->cliente->nombre ?? 'Venta General' }}</span>
                </div>
                @if($venta->cliente)
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-600">DNI:</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $venta->cliente->dni }}</span>
                    </div>
                    @if($venta->cliente->telefono)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-600">Teléfono:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $venta->cliente->telefono }}</span>
                        </div>
                    @endif
                    @if($venta->cliente->email)
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm font-medium text-gray-600">Email:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $venta->cliente->email }}</span>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Información de la Venta</h2>
                    <p class="text-sm text-gray-600">Detalles de la transacción</p>
                </div>
            </div>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-600">ID de Venta:</span>
                    <span class="text-sm font-semibold text-gray-900">#{{ $venta->id }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-600">Fecha:</span>
                    <span class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-600">Hace:</span>
                    <span class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($venta->fecha)->diffForHumans() }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-600">Total:</span>
                    <span class="text-sm font-semibold text-green-600 text-lg">S/ {{ number_format($venta->total, 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm font-medium text-gray-600">Estado:</span>
                    <span>
                        @if($venta->estado === 'pagado')
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-md">Pagado</span>
                        @elseif($venta->estado === 'pendiente')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-md">Pendiente</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-md">{{ ucfirst($venta->estado) }}</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Productos Vendidos</h2>
                    <p class="text-sm text-gray-600">{{ $venta->detalles->count() }} productos en esta venta</p>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Precio Unit.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($venta->detalles as $detalle)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3 bg-primary-100">
                                        <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $detalle->repuesto->nombre }}</p>
                                        @if($detalle->repuesto->categoria)
                                            <p class="text-xs text-gray-500">{{ $detalle->repuesto->categoria->nombre }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $detalle->cantidad }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                S/ {{ number_format($detalle->precio_unitario, 2) }}
                            </td>
                            <td class="px-6 py-4 font-bold text-green-600">
                                S/ {{ number_format($detalle->subtotal, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right font-semibold text-gray-900">TOTAL:</td>
                        <td class="px-6 py-3 font-bold text-green-600 text-lg">S/ {{ number_format($venta->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Acciones Disponibles</h3>
                <p class="text-sm text-gray-600">Opciones para esta venta</p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <button class="flex items-center justify-center px-4 py-2 text-white font-medium rounded-lg transition-all duration-200 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H9.5a2 2 0 01-2-2V5a2 2 0 012-2H17"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17h.01M7 13h.01M7 9h.01"/>
                    </svg>
                    Imprimir Factura
                </button>
                
                <button class="flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Más Opciones
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ✅ CORRECCIÓN 2: El selector ahora busca solo dentro de "#show-venta-content"
    const cards = document.querySelectorAll('#show-venta-content .bg-white');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.05}s`;
        card.classList.add('section-card');
    });
});
</script>
@endpush

@endsection