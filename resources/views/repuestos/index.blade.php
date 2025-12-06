@extends('layouts.app')

@section('title', 'Lista de Repuestos')
@section('page-title', 'Gestión de Repuestos')
@section('page-description', 'Administra el inventario de repuestos de tu taller')

@section('breadcrumbs')
<nav class="flex items-center space-x-2 text-sm text-gray-500">
    <a href="{{ route('dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
    <span class="font-medium text-gray-900">Repuestos</span>
</nav>
@endsection

@push('styles')
<style>
    /* Animaciones personalizadas */
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .animate-slide-up {
        animation: slideUp 0.4s ease-out;
    }
    
    .animate-modal-in {
        animation: modalIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    .animate-fade-in {
        animation: fadeIn 0.2s ease-out;
    }
    
    /* Estados de stock */
    .stock-critical { @apply bg-gradient-to-r from-red-500 to-red-600 text-white; }
    .stock-warning { @apply bg-gradient-to-r from-yellow-500 to-yellow-600 text-white; }
    .stock-good { @apply bg-gradient-to-r from-primary-500 to-primary-600 text-white; }
    
    /* Efectos hover mejorados */
    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    /* Modal backdrop mejorado */
    .modal-backdrop {
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    
    /* Scrollbar personalizado para modales */
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Responsive touch targets */
    @media (max-width: 768px) {
        .touch-target {
            min-height: 44px;
            min-width: 44px;
        }
    }
    
    /* Print styles */
    @media print {
        .no-print { display: none !important; }
        .modal-backdrop { backdrop-filter: none !important; }
    }
</style>
@endpush

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Stats Cards - Responsive -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-2 sm:mb-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Total Repuestos</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $repuestos->total() }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-primary-100 rounded-full self-start sm:self-auto">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-2 sm:mb-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Stock Bajo</p>
                    <p class="text-2xl sm:text-3xl font-bold text-red-600">{{ $stockBajoCount }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-red-100 rounded-full self-start sm:self-auto">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-2 sm:mb-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Valor Total</p>
                    <p class="text-2xl sm:text-3xl font-bold text-green-600">S/ {{ number_format($valorTotal, 2) }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-green-100 rounded-full self-start sm:self-auto">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-2 sm:mb-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Categorías</p>
                    <p class="text-2xl sm:text-3xl font-bold text-purple-600">{{ $categoriasCount }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-purple-100 rounded-full self-start sm:self-auto">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Acciones - Responsive Mejorado -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
        <form action="{{ route('repuestos.index') }}" method="GET">
            <div class="flex flex-col gap-4">
                
                <!-- Botón Agregar - Responsive -->
                <div class="flex justify-between items-center">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">Filtrar Repuestos</h3>
                    <a href="{{ route('repuestos.create') }}" 
                       class="inline-flex items-center justify-center px-3 sm:px-4 py-2 sm:py-2.5 bg-[#218786] text-white text-sm font-medium rounded-lg hover:bg-[#1a6d6c] transition-all duration-200 shadow-sm hover:shadow-md touch-target">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="hidden sm:inline">Agregar Repuesto</span>
                    </a>
                </div>

                <!-- Filtros Grid Responsive -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    
                    <!-- Filtro Categorías -->
                    <div class="relative">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Categoría</label>
                        <select name="categoria_id" 
                                class="block w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] bg-white transition-colors"
                                onchange="this.form.submit()">
                            <option value="">Todas las categorías</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Buscador con icono -->
                    <div class="relative sm:col-span-2 lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Buscar</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="q" 
                                value="{{ request('q') }}"
                                placeholder="Buscar por nombre, código o marca..."
                                class="block w-full pl-10 pr-20 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] bg-white transition-colors"
                            >
                            <div class="absolute inset-y-0 right-0 flex items-center gap-1 pr-2">
                                @if(request('q'))
                                    <a href="{{ route('repuestos.index', ['categoria_id' => request('categoria_id')]) }}" 
                                       class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-md transition-colors touch-target">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </a>
                                @endif
                                <button type="submit" 
                                        class="px-3 py-1.5 bg-[#218786] text-white text-sm rounded-md hover:bg-[#1a6d6c] transition-colors touch-target">
                                    <span class="hidden sm:inline">Buscar</span>
                                    <svg class="w-4 h-4 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Resultados de búsqueda - Responsive -->
    @if(request('q') || request('categoria_id'))
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4 animate-fade-in">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div class="flex items-start sm:items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5 sm:mt-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-blue-800">
                        Mostrando <strong>{{ $repuestos->total() }}</strong> {{ Str::plural('resultado', $repuestos->total()) }}
                        @if(request('q'))
                            para: <strong>"{{ request('q') }}"</strong>
                        @endif
                        @if(request('categoria_id'))
                            en: <strong>{{ $categorias->find(request('categoria_id'))->nombre ?? '' }}</strong>
                        @endif
                    </p>
                </div>
                <a href="{{ route('repuestos.index') }}" 
                   class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors touch-target">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Limpiar filtros
                </a>
            </div>
        </div>
    @endif

    <!-- Grid de repuestos - Responsive -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
        @forelse ($repuestos as $repuesto)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden card-hover animate-slide-up" 
                 style="animation-delay: {{ $loop->index * 50 }}ms">
                
                <!-- Imagen -->
                <div class="relative aspect-video bg-gray-100 overflow-hidden">
                    @if($repuesto->imagen)
                        <img src="{{ asset('storage/' . $repuesto->imagen) }}" 
                             alt="Imagen {{ $repuesto->nombre }}" 
                             class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                    
                    <!-- Badge de stock -->
                    <div class="absolute top-2 right-2">
                        @if($repuesto->cantidad <= 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 shadow-sm">
                                Agotado
                            </span>
                        @elseif($repuesto->cantidad <= $repuesto->minimo_stock)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 shadow-sm">
                                Stock Bajo
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 shadow-sm">
                                Disponible
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Contenido -->
                <div class="p-4 sm:p-5">
                    <!-- Header -->
                    <div class="mb-3">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 truncate mb-1">
                            {{ $repuesto->nombre }}
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-500 truncate">
                            {{ $repuesto->codigo }} • {{ $repuesto->marca }}
                        </p>
                    </div>

                    <!-- Precio y Stock -->
                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                        <div class="text-xl sm:text-2xl font-bold text-[#218786]">
                            S/ {{ number_format($repuesto->precio_unitario, 2) }}
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">Stock</p>
                            <p class="text-base sm:text-lg font-semibold {{ $repuesto->cantidad <= $repuesto->minimo_stock ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $repuesto->cantidad }}
                            </p>
                        </div>
                    </div>

                    <!-- Categoría -->
                    @if($repuesto->categoria)
                        <div class="mb-3 sm:mb-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $repuesto->categoria->nombre }}
                            </span>
                        </div>
                    @endif

                    <!-- Acciones - Responsive -->
                    <div class="flex items-center justify-between pt-3 sm:pt-4 border-t border-gray-100 gap-2">
                        <button onclick="openModal({{ $repuesto->id }})"
                                class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-[#218786] bg-[#218786]/10 rounded-lg hover:bg-[#218786]/20 transition-colors duration-200 touch-target">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span class="hidden sm:inline">Ver</span>
                        </button>
                        
                        <div class="flex items-center gap-2">
                            <button onclick="agregarCantidad({{ $repuesto->id }}, '{{ addslashes($repuesto->nombre) }}')"
                                    class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200 touch-target"
                                    title="Agregar stock">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </button>
                            
                            <a href="{{ route('repuestos.edit', $repuesto->id) }}"
                               class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors duration-200 touch-target"
                               title="Editar">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Mejorado UX/UI 2025 - Responsive -->
            <div id="modal-{{ $repuesto->id }}" 
                 class="fixed inset-0 bg-gray-900/75 modal-backdrop hidden items-center justify-center z-50 p-3 sm:p-4 animate-fade-in no-print"
                 onclick="if(event.target === this) closeModal({{ $repuesto->id }})">
                <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[95vh] sm:max-h-[90vh] overflow-hidden animate-modal-in flex flex-col">
                    
                    <!-- Header del modal - Sticky -->
                    <div class="sticky top-0 bg-white border-b border-gray-200 px-4 sm:px-6 py-3 sm:py-4 flex items-center justify-between z-10 flex-shrink-0">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-900">Detalles del Repuesto</h2>
                        <button onclick="closeModal({{ $repuesto->id }})" 
                                class="p-1.5 sm:p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors touch-target">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Contenido del modal - Scrollable -->
                    <div class="flex-1 overflow-y-auto custom-scrollbar p-4 sm:p-6">
                        <div class="grid lg:grid-cols-2 gap-6 sm:gap-8">
                            <!-- Columna Izquierda: Imagen e Info Rápida -->
                            <div class="space-y-4">
                                <!-- Imagen Grande -->
                                <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden">
                                    @if($repuesto->imagen)
                                        <img src="{{ asset('storage/' . $repuesto->imagen) }}" 
                                             alt="Imagen {{ $repuesto->nombre }}" 
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                            <svg class="w-16 h-16 sm:w-20 sm:h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Cards de Info Rápida - Responsive -->
                                <div class="grid grid-cols-2 gap-3 sm:gap-4">
                                    <div class="bg-[#218786]/10 rounded-xl p-4 text-center">
                                        <p class="text-[#218786] text-2xl sm:text-3xl font-bold">{{ $repuesto->cantidad }}</p>
                                        <p class="text-[#218786] text-xs sm:text-sm font-medium mt-1">En Stock</p>
                                    </div>
                                    <div class="bg-green-50 rounded-xl p-4 text-center">
                                        <p class="text-green-600 text-2xl sm:text-3xl font-bold">S/ {{ number_format($repuesto->precio_unitario, 2) }}</p>
                                        <p class="text-green-700 text-xs sm:text-sm font-medium mt-1">Precio Unit.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Columna Derecha: Detalles -->
                            <div class="space-y-4 sm:space-y-6">
                                <!-- Título y Descripción -->
                                <div>
                                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">{{ $repuesto->nombre }}</h3>
                                    @if($repuesto->descripcion)
                                        <p class="text-sm sm:text-base text-gray-600">{{ $repuesto->descripcion }}</p>
                                    @endif
                                </div>

                                <!-- Información General -->
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <h4 class="font-semibold text-gray-900 mb-3 text-sm sm:text-base flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Información General
                                    </h4>
                                    <dl class="grid grid-cols-1 gap-2 text-xs sm:text-sm">
                                        <div class="flex justify-between py-1.5 border-b border-gray-200">
                                            <dt class="text-gray-500">Código:</dt>
                                            <dd class="font-medium text-gray-900">{{ $repuesto->codigo }}</dd>
                                        </div>
                                        <div class="flex justify-between py-1.5 border-b border-gray-200">
                                            <dt class="text-gray-500">Marca:</dt>
                                            <dd class="font-medium text-gray-900">{{ $repuesto->marca ?: 'N/A' }}</dd>
                                        </div>
                                        <div class="flex justify-between py-1.5 border-b border-gray-200">
                                            <dt class="text-gray-500">Categoría:</dt>
                                            <dd class="font-medium text-gray-900">{{ $repuesto->categoria->nombre ?? 'Sin categoría' }}</dd>
                                        </div>
                                        <div class="flex justify-between py-1.5">
                                            <dt class="text-gray-500">Proveedor:</dt>
                                            <dd class="font-medium text-gray-900">{{ $repuesto->proveedor->nombre ?? 'Sin proveedor' }}</dd>
                                        </div>
                                    </dl>
                                </div>

                                <!-- Inventario -->
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <h4 class="font-semibold text-gray-900 mb-3 text-sm sm:text-base flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        Inventario
                                    </h4>
                                    <dl class="grid grid-cols-1 gap-2 text-xs sm:text-sm">
                                        <div class="flex justify-between py-1.5 border-b border-gray-200">
                                            <dt class="text-gray-500">Stock actual:</dt>
                                            <dd class="font-medium {{ $repuesto->cantidad <= $repuesto->minimo_stock ? 'text-red-600' : 'text-gray-900' }}">
                                                {{ $repuesto->cantidad }} unidades
                                            </dd>
                                        </div>
                                        <div class="flex justify-between py-1.5 border-b border-gray-200">
                                            <dt class="text-gray-500">Stock mínimo:</dt>
                                            <dd class="font-medium text-gray-900">{{ $repuesto->minimo_stock }} unidades</dd>
                                        </div>
                                        <div class="flex justify-between py-1.5 border-b border-gray-200">
                                            <dt class="text-gray-500">Valor total:</dt>
                                            <dd class="font-medium text-gray-900">
                                                S/ {{ number_format($repuesto->cantidad * $repuesto->precio_unitario, 2) }}
                                            </dd>
                                        </div>
                                        @if($repuesto->fecha_ingreso)
                                        <div class="flex justify-between py-1.5">
                                            <dt class="text-gray-500">Fecha ingreso:</dt>
                                            <dd class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($repuesto->fecha_ingreso)->format('d/m/Y') }}</dd>
                                        </div>
                                        @endif
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer con Acciones - Sticky -->
                    <div class="sticky bottom-0 bg-white border-t border-gray-200 px-4 sm:px-6 py-3 sm:py-4 flex-shrink-0">
                        <div class="flex flex-wrap gap-2 sm:gap-3">
                            <button onclick="agregarCantidad({{ $repuesto->id }}, '{{ addslashes($repuesto->nombre) }}')"
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-3 sm:px-4 py-2 sm:py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white text-sm font-medium rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-sm hover:shadow-md touch-target">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <span class="hidden sm:inline">Agregar Stock</span>
                            </button>

                            <a href="{{ route('repuestos.edit', $repuesto->id) }}" 
                               class="flex-1 sm:flex-none inline-flex items-center justify-center px-3 sm:px-4 py-2 sm:py-2.5 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white text-sm font-medium rounded-lg hover:from-yellow-600 hover:to-yellow-700 transition-all duration-200 shadow-sm hover:shadow-md touch-target">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span class="hidden sm:inline">Editar</span>
                            </a>

                            <form action="{{ route('repuestos.destroy', $repuesto->id) }}" method="POST" class="flex-1 sm:flex-none inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="button" 
                                        onclick="confirmarEliminacion(this.closest('form'), '{{ addslashes($repuesto->nombre) }}')"
                                        class="w-full inline-flex items-center justify-center px-3 sm:px-4 py-2 sm:py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white text-sm font-medium rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-sm hover:shadow-md touch-target">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    <span class="hidden sm:inline">Eliminar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <!-- Estado vacío - Responsive -->
            <div class="col-span-full">
                <div class="text-center py-12 sm:py-16">
                    <div class="mx-auto w-20 h-20 sm:w-24 sm:h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">
                        @if(request('q') || request('categoria_id'))
                            No se encontraron repuestos
                        @else
                            No hay repuestos registrados
                        @endif
                    </h3>
                    <p class="text-sm text-gray-500 mb-6 max-w-md mx-auto px-4">
                        @if(request('q') || request('categoria_id'))
                            No se encontraron repuestos que coincidan con tus filtros.
                        @else
                            Comienza agregando tu primer repuesto al inventario.
                        @endif
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center items-center px-4">
                        @if(request('q') || request('categoria_id'))
                            <a href="{{ route('repuestos.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors touch-target">
                                Limpiar filtros
                            </a>
                        @endif
                        <a href="{{ route('repuestos.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white text-sm rounded-lg hover:from-[#1a6d6c] hover:to-[#155856] transition-all shadow-sm hover:shadow-md touch-target">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Agregar repuesto
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Paginación Personalizada UX/UI 2025 - Responsive -->
    @if($repuestos->hasPages())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-4 sm:px-6 py-3 sm:py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0">
                <!-- Info de paginación -->
                <div class="text-xs sm:text-sm text-gray-700 text-center sm:text-left">
                    Mostrando <span class="font-medium">{{ $repuestos->firstItem() }}</span> - <span class="font-medium">{{ $repuestos->lastItem() }}</span> 
                    de <span class="font-medium">{{ $repuestos->total() }}</span> repuestos
                </div>
                
                <!-- Controles de paginación -->
                <div class="flex items-center justify-center gap-1 sm:gap-2">
                    {{-- Botón Primera Página --}}
                    @if ($repuestos->onFirstPage())
                        <span class="px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $repuestos->url(1) }}" class="px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#218786]/10 hover:text-[#218786] hover:border-[#218786] transition-colors touch-target">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    {{-- Botón Anterior --}}
                    @if ($repuestos->onFirstPage())
                        <span class="px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $repuestos->previousPageUrl() }}" class="px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#218786]/10 hover:text-[#218786] hover:border-[#218786] transition-colors touch-target">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    {{-- Números de página --}}
                    <div class="hidden sm:flex items-center gap-1">
                        @foreach ($repuestos->getUrlRange(max(1, $repuestos->currentPage() - 1), min($repuestos->lastPage(), $repuestos->currentPage() + 1)) as $page => $url)
                            @if ($page == $repuestos->currentPage())
                                <span class="px-3 py-2 text-sm font-semibold text-white bg-[#218786] rounded-lg shadow-sm">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#218786]/10 hover:text-[#218786] hover:border-[#218786] transition-colors">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    </div>

                    {{-- Indicador móvil de página actual --}}
                    <div class="sm:hidden px-3 py-1.5 text-xs font-medium text-white bg-[#218786] rounded-lg">
                        {{ $repuestos->currentPage() }} / {{ $repuestos->lastPage() }}
                    </div>

                    {{-- Botón Siguiente --}}
                    @if ($repuestos->hasMorePages())
                        <a href="{{ $repuestos->nextPageUrl() }}" class="px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#218786]/10 hover:text-[#218786] hover:border-[#218786] transition-colors touch-target">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @else
                        <span class="px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    @endif

                    {{-- Botón Última Página --}}
                    @if ($repuestos->hasMorePages())
                        <a href="{{ $repuestos->url($repuestos->lastPage()) }}" class="px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#218786]/10 hover:text-[#218786] hover:border-[#218786] transition-colors touch-target">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @else
                        <span class="px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                            </svg>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Alertas de Inventario - Responsive -->
    @if($stockBajo->count() > 0 || $agotados->count() > 0)
        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-4 sm:p-6">
            <div class="flex items-center mb-4">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <h3 class="text-base sm:text-lg font-semibold text-yellow-800">Alertas de Inventario</h3>
            </div>
            
            {{-- Repuestos Agotados --}}
            @if($agotados->count() > 0)
                <div class="mb-4">
                    <h4 class="font-semibold text-red-700 mb-2 flex items-center text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        Repuestos Agotados ({{ $agotados->count() }})
                    </h4>
                    <div class="space-y-2">
                        @foreach($agotados as $repuesto)
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center bg-white rounded-lg p-3 shadow-sm border-l-4 border-red-500 gap-2">
                                <div class="min-w-0 flex-1">
                                    <span class="font-medium text-gray-800 text-sm sm:text-base block truncate">{{ $repuesto->nombre }}</span>
                                    @if($repuesto->codigo)
                                        <span class="text-xs sm:text-sm text-gray-500">({{ $repuesto->codigo }})</span>
                                    @endif
                                </div>
                                <span class="text-red-600 font-bold text-sm whitespace-nowrap">Stock: 0</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Repuestos con Stock Bajo --}}
            @if($stockBajo->count() > 0)
                <div>
                    <h4 class="font-semibold text-yellow-700 mb-2 flex items-center text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Stock Bajo ({{ $stockBajo->count() }})
                    </h4>
                    <div class="space-y-2">
                        @foreach($stockBajo as $repuesto)
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center bg-white rounded-lg p-3 shadow-sm border-l-4 border-yellow-500 gap-2">
                                <div class="min-w-0 flex-1">
                                    <span class="font-medium text-gray-800 text-sm sm:text-base block truncate">{{ $repuesto->nombre }}</span>
                                    @if($repuesto->codigo)
                                        <span class="text-xs sm:text-sm text-gray-500">({{ $repuesto->codigo }})</span>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="text-yellow-600 font-bold text-sm block">Stock: {{ $repuesto->cantidad }}</span>
                                    <span class="text-xs text-gray-500">Mínimo: {{ $repuesto->minimo_stock }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>

@push('scripts')
<script>
// Función mejorada para agregar stock con validación
function agregarCantidad(id, nombre) {
    Swal.fire({
        title: '<strong class="text-xl">Agregar Stock</strong>',
        html: `
            <div class="text-left mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Producto:</label>
                <p class="text-base font-semibold text-gray-900 bg-gray-50 p-3 rounded-lg">${nombre}</p>
            </div>
            <div class="text-left">
                <label for="cantidad-input" class="block text-sm font-medium text-gray-700 mb-2">Cantidad a agregar:</label>
                <input type="number" id="cantidad-input" 
                       class="w-full px-4 py-3 text-base border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-colors" 
                       min="1" step="1" placeholder="Ingrese la cantidad" autofocus>
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-plus mr-2"></i>Agregar Stock',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#218786',
        cancelButtonColor: '#6b7280',
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'rounded-lg px-6 py-3 text-base font-semibold',
            cancelButton: 'rounded-lg px-6 py-3 text-base font-semibold',
            htmlContainer: 'text-left'
        },
        didOpen: () => {
            document.getElementById('cantidad-input').focus();
        },
        preConfirm: () => {
            const cantidad = document.getElementById('cantidad-input').value;
            if (!cantidad || cantidad < 1) {
                Swal.showValidationMessage('Por favor ingrese una cantidad válida (mínimo 1)');
                return false;
            }
            return cantidad;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Procesando...',
                html: 'Agregando stock al inventario',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/repuestos/${id}/cantidad`;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            const cantidad = document.createElement('input');
            cantidad.type = 'hidden';
            cantidad.name = 'cantidad';
            cantidad.value = result.value;
            form.appendChild(cantidad);

            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Función mejorada para confirmar eliminación
function confirmarEliminacion(form, nombre) {
    Swal.fire({
        title: '¿Estás seguro?',
        html: `
            <div class="text-left">
                <p class="mb-2 text-gray-700">Estás a punto de eliminar el repuesto:</p>
                <p class="font-semibold text-gray-900 bg-gray-50 p-3 rounded-lg mb-4">${nombre}</p>
                <div class="flex items-start p-3 bg-red-50 rounded-lg border border-red-200">
                    <svg class="w-5 h-5 text-red-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <p class="text-sm text-red-700">Esta acción no se puede deshacer y se eliminarán todos los datos asociados.</p>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-trash mr-2"></i>Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'rounded-lg px-6 py-3 text-base font-semibold',
            cancelButton: 'rounded-lg px-6 py-3 text-base font-semibold',
            htmlContainer: 'text-left'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Eliminando...',
                html: 'Procesando eliminación del repuesto',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            form.submit();
        }
    });
}

// Funciones para modales
function openModal(id) {
    const modal = document.getElementById('modal-' + id);
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        
        // Prevenir scroll en iOS
        document.body.style.position = 'fixed';
        document.body.style.width = '100%';
    }
}

function closeModal(id) {
    const modal = document.getElementById('modal-' + id);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
        
        // Restaurar scroll en iOS
        document.body.style.position = '';
        document.body.style.width = '';
    }
}

// Cerrar modal con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('[id^="modal-"]');
        modals.forEach(modal => {
            if (!modal.classList.contains('hidden')) {
                const modalId = modal.id.replace('modal-', '');
                closeModal(modalId);
            }
        });
    }
});

// Atajo de teclado para búsqueda (Ctrl/Cmd + K)
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const searchInput = document.querySelector('input[name="q"]');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    }
});

// Lazy loading para imágenes
if ('IntersectionObserver' in window) {
    document.addEventListener('DOMContentLoaded', function() {
        const images = document.querySelectorAll('img[src]');
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.classList.add('loaded');
                    observer.unobserve(img);
                }
            });
        });
        images.forEach(img => imageObserver.observe(img));
    });
}

// Prevenir zoom en dispositivos móviles al enfocar inputs
if (/iPhone|iPad|iPod|Android/i.test(navigator.userAgent)) {
    document.addEventListener('DOMContentLoaded', function() {
        const viewportMeta = document.querySelector('meta[name="viewport"]');
        if (viewportMeta) {
            const originalContent = viewportMeta.content;
            document.querySelectorAll('input, select, textarea').forEach(element => {
                element.addEventListener('focus', function() {
                    viewportMeta.content = 'width=device-width, initial-scale=1, maximum-scale=1';
                });
                element.addEventListener('blur', function() {
                    viewportMeta.content = originalContent;
                });
            });
        }
    });
}
</script>
@endpush
@endsection
