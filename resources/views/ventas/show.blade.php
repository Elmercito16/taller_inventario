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
    {{-- 👇 AÑADIR AQUÍ LA SECCIÓN DE COMPROBANTE ELECTRÓNICO --}}
    
    {{-- Sección de Comprobante Electrónico --}}
    @if($venta->estado === 'completada' || $venta->estado === 'pagado')
    <div class="fade-in bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-8 py-6">
            <h2 class="text-2xl font-bold text-white flex items-center">
                <svg class="w-7 h-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Comprobante Electrónico
            </h2>
        </div>
        
        <div class="p-8">
            @if($venta->tieneComprobanteElectronico())
                {{-- Ya tiene comprobante --}}
                @php $comprobante = $venta->comprobante; @endphp
                
                <div class="flex items-center justify-between mb-6 pb-6 border-b-2 border-gray-100">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">
                            {{ $comprobante->tipo_comprobante == '01' ? '📄 Factura Electrónica' : '🧾 Boleta de Venta' }}
                        </h3>
                        <p class="text-3xl font-mono font-bold text-purple-600 mt-2">
                            {{ $comprobante->numero_completo }}
                        </p>
                    </div>
                    
                    <div class="text-right">
                        @if($comprobante->estado_sunat == 'aceptado' && !$comprobante->anulado)
                            <span class="px-6 py-3 bg-green-100 text-green-800 rounded-full font-bold text-lg inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Aceptado por SUNAT
                            </span>
                        @elseif($comprobante->anulado)
                            <span class="px-6 py-3 bg-gray-100 text-gray-800 rounded-full font-bold text-lg inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                                </svg>
                                Anulado
                            </span>
                        @else
                            <span class="px-6 py-3 bg-yellow-100 text-yellow-800 rounded-full font-bold text-lg inline-flex items-center">
                                <svg class="w-5 h-5 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                {{ ucfirst($comprobante->estado_sunat) }}
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200">
                        <p class="text-sm font-bold text-gray-600 mb-1">Fecha de Emisión</p>
                        <p class="text-lg font-bold text-gray-900">{{ $comprobante->fecha_emision->format('d/m/Y H:i') }}</p>
                    </div>
                    
                    <div class="p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200">
                        <p class="text-sm font-bold text-gray-600 mb-1">Cliente</p>
                        <p class="text-lg font-bold text-gray-900">{{ $comprobante->cliente_nombre }}</p>
                        <p class="text-sm text-gray-600">{{ $comprobante->cliente_num_doc }}</p>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('comprobantes.show', $comprobante) }}" 
                       class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-bold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Ver Comprobante
                    </a>
                    
                    @if($comprobante->xml_path)
                    <a href="{{ route('comprobantes.descargar-xml', $comprobante) }}" 
                       class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-bold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Descargar XML
                    </a>
                    @endif
                </div>
                
            @else
                {{-- No tiene comprobante - Mostrar formulario para emitir --}}
                
                @if(auth()->user()->empresa->tieneFacturacionActiva())
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-purple-100 rounded-full mb-4">
                            <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">
                            Esta venta aún no tiene comprobante electrónico
                        </h3>
                        <p class="text-gray-600">
                            Selecciona el tipo de comprobante y emítelo ahora mismo
                        </p>
                    </div>
                    
                    <form action="{{ route('comprobantes.emitir', $venta) }}" method="POST" class="max-w-2xl mx-auto">
                        @csrf
                        
                        <div class="mb-6">
                            <label class="block text-lg font-bold text-gray-700 mb-4 text-center">
                                Tipo de Comprobante <span class="text-red-500">*</span>
                            </label>
                            
                            <div class="space-y-3">
                                @if($venta->cliente && $venta->cliente->tieneRuc())
                                    {{-- Cliente con RUC: puede emitir factura o boleta --}}
                                    <label class="flex items-center p-5 border-2 border-gray-300 rounded-xl cursor-pointer hover:border-purple-500 hover:bg-purple-50 transition-all">
                                        <input type="radio" name="tipo_comprobante" value="01" 
                                               class="w-6 h-6 text-purple-600" checked required>
                                        <div class="ml-4 flex-1">
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-xl text-gray-900">📄 Factura Electrónica</span>
                                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold">Recomendado</span>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">Cliente tiene RUC registrado</p>
                                        </div>
                                    </label>
                                    
                                    <label class="flex items-center p-5 border-2 border-gray-300 rounded-xl cursor-pointer hover:border-purple-500 hover:bg-purple-50 transition-all">
                                        <input type="radio" name="tipo_comprobante" value="03" 
                                               class="w-6 h-6 text-purple-600" required>
                                        <div class="ml-4 flex-1">
                                            <span class="font-bold text-xl text-gray-900">🧾 Boleta de Venta</span>
                                            <p class="text-sm text-gray-600 mt-1">También disponible para este cliente</p>
                                        </div>
                                    </label>
                                @else
                                    {{-- Cliente sin RUC o sin cliente: solo boleta --}}
                                    <label class="flex items-center p-5 border-2 border-purple-500 bg-purple-50 rounded-xl cursor-pointer">
                                        <input type="radio" name="tipo_comprobante" value="03" 
                                               class="w-6 h-6 text-purple-600" checked required>
                                        <div class="ml-4 flex-1">
                                            <span class="font-bold text-xl text-gray-900">🧾 Boleta de Venta</span>
                                            <p class="text-sm text-gray-600 mt-1">Cliente con DNI - Solo boleta disponible</p>
                                        </div>
                                    </label>
                                    
                                    <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg">
                                        <div class="flex">
                                            <svg class="w-5 h-5 text-yellow-600 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <p class="text-sm text-yellow-800">
                                                <strong>Nota:</strong> Para emitir facturas, el cliente debe tener RUC registrado. Puedes editarlo desde la sección de clientes.
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            @error('tipo_comprobante')
                                <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <button type="submit" 
                                class="w-full px-8 py-4 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-bold text-lg rounded-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all flex items-center justify-center"
                                onclick="return confirm('¿Confirmas la emisión de este comprobante electrónico?\n\nEsta acción enviará el documento a SUNAT y no podrá deshacerse.')">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Emitir Comprobante Electrónico
                        </button>
                    </form>
                    
                @else
                    {{-- Facturación no configurada --}}
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-yellow-100 rounded-full mb-4">
                            <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">
                            Facturación Electrónica No Configurada
                        </h3>
                        <p class="text-gray-600 mb-8 max-w-md mx-auto">
                            Para emitir comprobantes electrónicos debes configurar primero los datos de tu empresa y el certificado digital
                        </p>
                        <a href="{{ route('empresa.index') }}" 
                           class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-bold text-lg rounded-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Configurar Facturación Ahora
                        </a>
                    </div>
                @endif
                
            @endif
        </div>
    </div>
    @endif
    {{-- 👆 FIN DE LA SECCIÓN DE COMPROBANTE ELECTRÓNICO --}}
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
