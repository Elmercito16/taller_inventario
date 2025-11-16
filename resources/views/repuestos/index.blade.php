@extends('layouts.app')

@section('title', 'Lista de Repuestos')
@section('page-title', 'Gestión de Repuestos')
@section('page-description', 'Administra el inventario de repuestos de tu taller')

@section('breadcrumbs')
{{-- ... (sin cambios) ... --}}
<nav class="flex items-center space-x-2 text-sm text-gray-500">
    <a href="{{ route('dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
    <span class="font-medium text-gray-900">Repuestos</span>
</nav>
@endsection

@push('styles')
{{-- ... (sin cambios) ... --}}
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
    
    .animate-slide-up {
        animation: slideUp 0.4s ease-out;
    }
    
    .animate-modal-in {
        animation: modalIn 0.3s ease-out;
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
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Repuestos</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $repuestos->total() }}</p>
                </div>
                {{-- ... (ícono sin cambios) ... --}}
                <div class="p-3 bg-primary-100 rounded-full">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Stock Bajo</p>
                    {{-- ¡CAMBIO! Usamos la nueva variable del controlador --}}
                    <p class="text-3xl font-bold text-red-600">
                        {{ $stockBajoCount }}
                    </p>
                </div>
                {{-- ... (ícono sin cambios) ... --}}
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    {{-- ¡CAMBIO! Quitamos "(Página)" del título --}}
                    <p class="text-sm font-medium text-gray-600">Valor Total</p>
                    <p class="text-3xl font-bold text-green-600">
                        {{-- ¡CAMBIO! Usamos la nueva variable del controlador --}}
                        S/ {{ number_format($valorTotal, 2) }}
                    </p>
                </div>
                {{-- ... (ícono sin cambios) ... --}}
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
                    {{-- ¡CAMBIO! Quitamos "(Página)" del título --}}
                    <p class="text-sm font-medium text-gray-600">Categorías</p>
                    <p class="text-3xl font-bold text-purple-600">
                        {{-- ¡CAMBIO! Usamos la nueva variable del controlador --}}
                        {{ $categoriasCount }}
                    </p>
                </div>
                {{-- ... (ícono sin cambios) ... --}}
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Acciones -->
    {{-- ... (sin cambios en esta sección) ... --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('repuestos.index') }}" method="GET">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                
                <!-- Acciones (Botón Agregar) -->
                <div class="flex-shrink-0">
                    <a href="{{ route('repuestos.create') }}" 
                       class="inline-flex items-center justify-center w-full lg:w-auto px-4 py-2.5 bg-[#1b8c72ff] text-white font-medium rounded-lg hover:bg-[#16735fff] transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Agregar Repuesto
                    </a>
                </div>

                <!-- Filtros (Categoría y Buscador) -->
                <div class="flex flex-col sm:flex-row flex-grow gap-3">
                    
                    <!-- Filtro Categorías -->
                    <div class="flex-grow sm:flex-grow-0 sm:w-64">
                        <select name="categoria_id" 
                                class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 bg-white"
                                onchange="this.form.submit()">
                            <option value="">-- Todas las categorías --</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Buscador -->
                    <div class="relative flex-grow">
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
                            class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 bg-white"
                        >
                        @if(request('q'))
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <!-- Botón para limpiar 'q' pero mantener 'categoria_id' -->
                                <a href="{{ route('repuestos.index', ['categoria_id' => request('categoria_id')]) }}" 
                                   class="text-gray-400 hover:text-gray-600 transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Botón Buscar -->
                    <button type="submit" 
                            class="px-5 py-2.5 bg-[#1b8c72ff] text-white rounded-lg hover:bg-[#16735fff] transition-colors duration-200 border border-[#1b8c72ff]">
                        Buscar
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Resultados de búsqueda -->
    {{-- ... (sin cambios en esta sección) ... --}}
    @if(request('q') || request('categoria_id'))
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-blue-800">
                        Mostrando {{ $repuestos->total() }} {{ Str::plural('resultado', $repuestos->total()) }}
                        @if(request('q'))
                            para: <strong>"{{ request('q') }}"</strong>
                        @endif
                        @if(request('categoria_id'))
                            en la categoría: <strong>{{ $categorias->find(request('categoria_id'))->nombre ?? '' }}</strong>
                        @endif
                    </p>
                </div>
                <a href="{{ route('repuestos.index') }}" 
                   class="text-blue-600 hover:text-blue-800 font-medium text-sm flex-shrink-0 ml-4">
                    Limpiar filtros
                </a>
            </div>
        </div>
    @endif

    <!-- Grid de repuestos -->
    {{-- ... (sin cambios en el @forelse y los modales) ... --}}
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse ($repuestos as $repuesto)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden card-hover animate-slide-up" 
                 style="animation-delay: {{ $loop->index * 50 }}ms">
                
                <!-- Imagen -->
                <div class="aspect-w-16 aspect-h-12 bg-gray-100 relative overflow-hidden">
                    @if($repuesto->imagen)
                        <img src="{{ asset('storage/' . $repuesto->imagen) }}" 
                             alt="Imagen {{ $repuesto->nombre }}" 
                             class="w-full h-48 object-cover transition-transform duration-300 hover:scale-105">
                    @else
                        <div class="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                    
                    <!-- Badge de stock -->
                    <div class="absolute top-2 right-2">
                        @if($repuesto->cantidad <= 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Agotado
                            </span>
                        @elseif($repuesto->cantidad <= $repuesto->minimo_stock)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Stock Bajo
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Disponible
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Contenido -->
                <div class="p-5">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 truncate mb-1">
                                {{ $repuesto->nombre }}
                            </h3>
                            <p class="text-sm text-gray-500 truncate">
                                {{ $repuesto->codigo }} • {{ $repuesto->marca }}
                            </p>
                        </div>
                    </div>

                    <!-- Precio y Stock -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-2xl font-bold text-primary-600">
                            S/ {{ number_format($repuesto->precio_unitario, 2) }}
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Stock</p>
                            <p class="text-lg font-semibold {{ $repuesto->cantidad <= $repuesto->minimo_stock ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $repuesto->cantidad }}
                            </p>
                        </div>
                    </div>

                    <!-- Categoría -->
                    @if($repuesto->categoria)
                        <div class="mb-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $repuesto->categoria->nombre }}
                            </span>
                        </div>
                    @endif

                    <!-- Acciones -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <button onclick="openModal({{ $repuesto->id }})"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Ver detalles
                        </button>
                        
                        <div class="flex items-center space-x-2">
                            <button onclick="agregarCantidad({{ $repuesto->id }}, '{{ addslashes($repuesto->nombre) }}')"
                                    class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200"
                                    title="Agregar stock">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </button>
                            
                            <a href="{{ route('repuestos.edit', $repuesto->id) }}"
                               class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors duration-200"
                               title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal mejorado -->
            <div id="modal-{{ $repuesto->id }}" 
                 class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden items-center justify-center z-50 p-4"
                 onclick="if(event.target === this) closeModal({{ $repuesto->id }})">
                <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto animate-modal-in">
                    
                    <!-- Header del modal -->
                    <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-2xl">
                        <h2 class="text-xl font-bold text-gray-900">Detalles del Repuesto</h2>
                        <button onclick="closeModal({{ $repuesto->id }})" 
                                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Contenido del modal -->
                    <div class="p-6">
                        <div class="grid lg:grid-cols-2 gap-8">
                            <!-- Imagen -->
                            <div class="space-y-4">
                                <div class="aspect-w-1 aspect-h-1 bg-gray-100 rounded-xl overflow-hidden">
                                    @if($repuesto->imagen)
                                        <img src="{{ asset('storage/' . $repuesto->imagen) }}" 
                                             alt="Imagen {{ $repuesto->nombre }}" 
                                             class="w-full h-80 object-cover">
                                    @else
                                        <div class="w-full h-80 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Información rápida -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-primary-50 rounded-lg p-4 text-center">
                                        <p class="text-primary-600 text-2xl font-bold">{{ $repuesto->cantidad }}</p>
                                        <p class="text-primary-700 text-sm font-medium">En Stock</p>
                                    </div>
                                    <div class="bg-green-50 rounded-lg p-4 text-center">
                                        <p class="text-green-600 text-2xl font-bold">S/ {{ number_format($repuesto->precio_unitario, 2) }}</p>
                                        <p class="text-green-700 text-sm font-medium">Precio Unit.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Detalles -->
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $repuesto->nombre }}</h3>
                                    @if($repuesto->descripcion)
                                        <p class="text-gray-600 mb-4">{{ $repuesto->descripcion }}</p>
                                    @endif
                                </div>

                                <!-- Información detallada -->
                                <div class="grid grid-cols-1 gap-4">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 mb-3">Información General</h4>
                                        <dl class="grid grid-cols-1 gap-2 text-sm">
                                            <div class="flex justify-between">
                                                <dt class="text-gray-500">Código:</dt>
                                                <dd class="font-medium text-gray-900">{{ $repuesto->codigo }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-gray-500">Marca:</dt>
                                                <dd class="font-medium text-gray-900">{{ $repuesto->marca ?: 'N/A' }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-gray-500">Categoría:</dt>
                                                <dd class="font-medium text-gray-900">{{ $repuesto->categoria->nombre ?? 'Sin categoría' }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-gray-500">Proveedor:</dt>
                                                <dd class="font-medium text-gray-900">{{ $repuesto->proveedor->nombre ?? 'Sin proveedor' }}</dd>
                                            </div>
                                        </dl>
                                    </div>

                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 mb-3">Inventario</h4>
                                        <dl class="grid grid-cols-1 gap-2 text-sm">
                                            <div class="flex justify-between">
                                                <dt class="text-gray-500">Stock actual:</dt>
                                                <dd class="font-medium {{ $repuesto->cantidad <= $repuesto->minimo_stock ? 'text-red-600' : 'text-gray-900' }}">
                                                    {{ $repuesto->cantidad }} unidades
                                                </dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-gray-500">Stock mínimo:</dt>
                                                <dd class="font-medium text-gray-900">{{ $repuesto->minimo_stock }} unidades</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-gray-500">Valor total:</dt>
                                                <dd class="font-medium text-gray-900">
                                                    S/ {{ number_format($repuesto->cantidad * $repuesto->precio_unitario, 2) }}
                                                </dd>
                                            </div>
                                            @if($repuesto->fecha_ingreso)
                                            <div class="flex justify-between">
                                                <dt class="text-gray-500">Fecha ingreso:</dt>
                                                <dd class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($repuesto->fecha_ingreso)->format('d/m/Y') }}</dd>
                                            </div>
                                            @endif
                                        </dl>
                                    </div>
                                </div>

                                <!-- Acciones del modal -->
                                <div class="flex flex-wrap gap-3 pt-6 border-t border-gray-200">
                                    <button onclick="agregarCantidad({{ $repuesto->id }}, '{{ addslashes($repuesto->nombre) }}')"
                                            class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white font-medium rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Agregar Stock
                                    </button>

                                    <a href="{{ route('repuestos.edit', $repuesto->id) }}" 
                                       class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white font-medium rounded-lg hover:from-yellow-600 hover:to-yellow-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Editar
                                    </a>

                                    <form action="{{ route('repuestos.destroy', $repuesto->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" 
                                                onclick="confirmarEliminacion(this.closest('form'), '{{ addslashes($repuesto->nombre) }}')"
                                                class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white font-medium rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Eliminar
                                        </button>
                                    </form>

                                    <button onclick="window.print()"
                                            class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                        Imprimir
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- ... (sin cambios en esta sección) ... --}}
            <div class="col-span-full">
                <div class="text-center py-12">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        @if(request('q') || request('categoria_id'))
                            No se encontraron repuestos
                        @else
                            No hay repuestos registrados
                        @endif
                    </h3>
                    <p class="text-gray-500 mb-6 max-w-md mx-auto">
                        @if(request('q') || request('categoria_id'))
                            No se encontraron repuestos que coincidan con tus filtros. Intenta con otros términos o categorías.
                        @else
                            Comienza agregando tu primer repuesto al inventario para empezar a gestionar tu stock.
                        @endif
                    </p>
                    @if(request('q') || request('categoria_id'))
                        <a href="{{ route('repuestos.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 mr-3">
                            Limpiar filtros
                        </a>
                    @endif
                    <a href="{{ route('repuestos.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Agregar repuesto
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Paginación mejorada -->
    @if($repuestos->hasPages())
        {{-- ... (sin cambios en esta sección) ... --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Mostrando {{ $repuestos->firstItem() }} - {{ $repuestos->lastItem() }} 
                    de {{ $repuestos->total() }} repuestos
                </div>
                <div class="flex items-center space-x-2">
                    {{ $repuestos->links() }}
                </div>
            </div>
        </div>
    @endif

  {{-- Alertas de Inventario --}}
@if($stockBajo->count() > 0 || $agotados->count() > 0)
    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-6">
        <div class="flex items-center mb-4">
            <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <h3 class="text-lg font-semibold text-yellow-800">Alertas de Inventario</h3>
        </div>
        
        {{-- Repuestos Agotados --}}
        @if($agotados->count() > 0)
            <div class="mb-4">
                <h4 class="font-semibold text-red-700 mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    Repuestos Agotados ({{ $agotados->count() }})
                </h4>
                <div class="space-y-2">
                    @foreach($agotados as $repuesto)
                        <div class="flex justify-between items-center bg-white rounded-lg p-3 shadow-sm border-l-4 border-red-500">
                            <div>
                                <span class="font-medium text-gray-800">{{ $repuesto->nombre }}</span>
                                @if($repuesto->codigo)
                                    <span class="text-sm text-gray-500 ml-2">({{ $repuesto->codigo }})</span>
                                @endif
                            </div>
                            <span class="text-red-600 font-bold">Stock: 0</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Repuestos con Stock Bajo --}}
        @if($stockBajo->count() > 0)
            <div>
                <h4 class="font-semibold text-yellow-700 mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    Stock Bajo ({{ $stockBajo->count() }})
                </h4>
                <div class="space-y-2">
                    @foreach($stockBajo as $repuesto)
                        <div class="flex justify-between items-center bg-white rounded-lg p-3 shadow-sm border-l-4 border-yellow-500">
                            <div>
                                <span class="font-medium text-gray-800">{{ $repuesto->nombre }}</span>
                                @if($repuesto->codigo)
                                    <span class="text-sm text-gray-500 ml-2">({{ $repuesto->codigo }})</span>
                                @endif
                            </div>
                            <div class="text-right">
                                <span class="text-yellow-600 font-bold">Stock: {{ $repuesto->cantidad }}</span>
                                <span class="text-xs text-gray-500 block">Mínimo: {{ $repuesto->minimo_stock }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endif

@push('scripts')
{{-- ... (sin cambios en los scripts) ... --}}
<script>
// Función mejorada para agregar stock
function agregarCantidad(id, nombre) {
    Swal.fire({
        title: '<strong>Agregar Stock</strong>',
        html: `
            <div class="text-left mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Producto:</label>
                <p class="text-base font-semibold text-gray-900">${nombre}</p>
            </div>
            <div class="text-left">
                <label for="cantidad-input" class="block text-sm font-medium text-gray-700 mb-2">Cantidad a agregar:</label>
                <input type="number" id="cantidad-input" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="1" step="1" placeholder="Ingrese la cantidad">
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-plus mr-2"></i>Agregar Stock',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#218786',
        cancelButtonColor: '#6b7280',
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'rounded-lg px-6 py-2.5',
            cancelButton: 'rounded-lg px-6 py-2.5'
        },
        preConfirm: () => {
            const cantidad = document.getElementById('cantidad-input').value;
            if (!cantidad || cantidad < 1) {
                Swal.showValidationMessage('Por favor ingrese una cantidad válida');
                return false;
            }
            return cantidad;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Procesando...',
                html: 'Agregando stock al inventario',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Crear y enviar formulario
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
                <p class="mb-2">Estás a punto de eliminar el repuesto:</p>
                <p class="font-semibold text-gray-900 mb-4">${nombre}</p>
                <p class="text-sm text-red-600">⚠️ Esta acción no se puede deshacer</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'rounded-lg px-6 py-2.5',
            cancelButton: 'rounded-lg px-6 py-2.5'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Eliminando...',
                html: 'Procesando eliminación',
                allowOutsideClick: false,
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
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
    
    // Animación de entrada
    setTimeout(() => {
        modal.querySelector('.animate-modal-in').style.opacity = '1';
    }, 10);
}

function closeModal(id) {
    const modal = document.getElementById('modal-' + id);
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
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

// Auto-focus en campos de búsqueda
document.addEventListener('DOMContentLoaded', function() {
    // Focus en búsqueda con Ctrl/Cmd + K
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            document.querySelector('input[name="q"]').focus();
        }
    });
});

// Lazy loading para imágenes
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img[src]');
    if ('IntersectionObserver' in window) {
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
    }
});
</script>
@endpush
@endsection