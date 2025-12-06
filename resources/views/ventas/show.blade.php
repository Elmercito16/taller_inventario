@extends('layouts.app')

@section('title', 'Detalle de Venta')
@section('page-title', 'Detalle de Venta #' . $venta->id)
@section('page-description', 'Información completa de la transacción')

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
</style>
@endpush

@section('content')
<div class="space-y-6">

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
        <a href="{{ route('ventas.index') }}" class="hover:text-[#218786] transition-colors">Ventas</a>
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <span class="font-medium text-gray-900">Venta #{{ $venta->id }}</span>
    </nav>

    <!-- Header -->
    <div class="fade-in bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-[#218786] to-[#1a6d6c] rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Venta #{{ $venta->id }}</h1>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }} • {{ \Carbon\Carbon::parse($venta->fecha)->diffForHumans() }}
                    </p>
                </div>
            </div>
            
            <div class="flex gap-3 w-full md:w-auto">
                <a href="{{ route('ventas.boleta', $venta) }}" 
   target="_blank"
   class="flex-1 md:flex-none inline-flex items-center justify-center px-5 py-3 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-bold rounded-lg hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
    </svg>
    Imprimir Boleta
</a>

                
                <a href="{{ route('ventas.index') }}" 
                   class="flex-1 md:flex-none inline-flex items-center justify-center px-5 py-3 bg-white border-2 border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-50 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total -->
        <div class="fade-in bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total Venta</p>
            <p class="text-3xl font-bold text-green-600 mt-1">S/ {{ number_format($venta->total, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Monto total facturado</p>
        </div>

        <!-- Productos -->
        <div class="fade-in bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Productos</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">{{ $venta->detalles->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Tipos diferentes</p>
        </div>

        <!-- Cantidad -->
        <div class="fade-in bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Cantidad Total</p>
            <p class="text-3xl font-bold text-purple-600 mt-1">{{ $venta->detalles->sum('cantidad') }}</p>
            <p class="text-xs text-gray-500 mt-1">Unidades vendidas</p>
        </div>

        <!-- Estado -->
        <div class="fade-in bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="p-3 bg-amber-100 rounded-lg">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Estado</p>
            <div class="mt-2">
                @if($venta->estado === 'pagado')
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-green-100 text-green-700">
                        ✓ Pagado
                    </span>
                @elseif($venta->estado === 'pendiente')
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-yellow-100 text-yellow-700">
                        ⏱ Pendiente
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-red-100 text-red-700">
                        ✕ {{ ucfirst($venta->estado) }}
                    </span>
                @endif
            </div>
            <p class="text-xs text-gray-500 mt-2">Estado actual</p>
        </div>
    </div>

    <!-- Información Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Cliente -->
        <div class="fade-in bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6">
                <div class="p-3 bg-blue-100 rounded-lg mr-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Información del Cliente</h2>
                    <p class="text-sm text-gray-600">Datos del comprador</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-600">Cliente:</span>
                    <span class="text-sm font-bold text-gray-900">{{ $venta->cliente->nombre ?? 'Venta General' }}</span>
                </div>
                
                @if($venta->cliente)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-600">DNI:</span>
                        <span class="text-sm font-bold text-gray-900">{{ $venta->cliente->dni }}</span>
                    </div>
                    
                    @if($venta->cliente->telefono)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">Teléfono:</span>
                            <span class="text-sm font-bold text-gray-900">{{ $venta->cliente->telefono }}</span>
                        </div>
                    @endif
                    
                    @if($venta->cliente->email)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">Email:</span>
                            <span class="text-sm font-bold text-gray-900 truncate ml-2">{{ $venta->cliente->email }}</span>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Venta -->
        <div class="fade-in bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6">
                <div class="p-3 bg-green-100 rounded-lg mr-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Información de la Venta</h2>
                    <p class="text-sm text-gray-600">Detalles de la transacción</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-600">ID de Venta:</span>
                    <span class="text-sm font-bold text-gray-900">#{{ $venta->id }}</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-600">Fecha:</span>
                    <span class="text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-600">Hace:</span>
                    <span class="text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($venta->fecha)->diffForHumans() }}</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border-2 border-green-200">
                    <span class="text-sm font-semibold text-gray-700">Total:</span>
                    <span class="text-xl font-bold text-green-600">S/ {{ number_format($venta->total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos -->
    <div class="fade-in bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center mb-6">
            <div class="p-3 bg-orange-100 rounded-lg mr-3">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Productos Vendidos</h2>
                <p class="text-sm text-gray-600">{{ $venta->detalles->count() }} productos en esta transacción</p>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Precio Unit.</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($venta->detalles as $detalle)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-[#218786] to-[#1a6d6c] rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $detalle->repuesto->nombre }}</p>
                                        @if($detalle->repuesto->categoria)
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $detalle->repuesto->categoria->nombre }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-blue-100 text-blue-700">
                                    {{ $detalle->cantidad }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-gray-900">S/ {{ number_format($detalle->precio_unitario, 2) }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-green-600 text-lg">S/ {{ number_format($detalle->subtotal, 2) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gradient-to-r from-green-50 to-emerald-50 border-t-2 border-green-200">
                        <td colspan="3" class="px-6 py-4 text-right">
                            <span class="text-lg font-bold text-gray-900">TOTAL:</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-2xl font-bold text-green-600">S/ {{ number_format($venta->total, 2) }}</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
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
});

// Estilos de impresión
window.addEventListener('beforeprint', function() {
    document.querySelectorAll('.no-print').forEach(el => el.style.display = 'none');
});

window.addEventListener('afterprint', function() {
    document.querySelectorAll('.no-print').forEach(el => el.style.display = '');
});
</script>
@endpush
@endsection
