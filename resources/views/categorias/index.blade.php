@extends('layouts.app')

@section('title', 'Categorías')
@section('page-title', 'Gestión de Categorías')
@section('page-description', 'Organiza y administra las categorías de tus repuestos')

@push('styles')
<style>
    .category-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        animation: slideUp 0.5s ease-out both;
    }
    
    .category-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 25px -5px rgba(33, 135, 134, 0.1), 0 10px 10px -5px rgba(33, 135, 134, 0.04);
    }
    
    .category-card:nth-child(n) { animation-delay: calc(0.05s * var(--index)); }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .stat-card {
        animation: slideUp 0.6s ease-out both;
    }
    
    .stat-card:nth-child(1) { animation-delay: 0s; }
    .stat-card:nth-child(2) { animation-delay: 0.1s; }
    .stat-card:nth-child(3) { animation-delay: 0.2s; }
    .stat-card:nth-child(4) { animation-delay: 0.3s; }
    
    .badge-shimmer {
        background: linear-gradient(90deg, currentColor 25%, rgba(255,255,255,0.5) 50%, currentColor 75%);
        background-size: 200% 100%;
        animation: shimmer 2s infinite;
    }
    
    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
    .search-input:focus {
        box-shadow: 0 0 0 3px rgba(33, 135, 134, 0.1);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Estadísticas Mejoradas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Categorías -->
        <div class="stat-card bg-gradient-to-br from-[#218786] to-[#1a6d6c] rounded-xl shadow-lg p-6 text-white overflow-hidden relative">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-medium text-white/80">Total Categorías</p>
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                </div>
                <p class="text-4xl font-bold">{{ $categorias->count() }}</p>
            </div>
        </div>
        
        <!-- Con Descripción -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium text-gray-600">Completas</p>
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-green-600">
                {{ $categorias->filter(fn($cat) => !empty($cat->descripcion))->count() }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Con descripción</p>
        </div>
        
        <!-- Sin Descripción -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium text-gray-600">Incompletas</p>
                <div class="p-2 bg-amber-100 rounded-lg">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-amber-600">
                {{ $categorias->filter(fn($cat) => empty($cat->descripcion))->count() }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Sin descripción</p>
        </div>
        
        <!-- Total Productos -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium text-gray-600">Total Productos</p>
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-purple-600">
                {{ $categorias->sum('repuestos_count') }}
            </p>
            <p class="text-xs text-gray-500 mt-1">En todas las categorías</p>
        </div>
    </div>

    <!-- Barra de Acciones Mejorada -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <!-- Título -->
            <div class="flex-1">
                <h2 class="text-xl font-bold text-gray-900">Lista de Categorías</h2>
                <p class="text-sm text-gray-600 mt-1">{{ $categorias->count() }} categorías registradas</p>
            </div>
            
            <!-- Barra de búsqueda -->
            <div class="flex-1 max-w-md">
                <div class="relative">
                    <input type="text" 
                           id="searchInput"
                           placeholder="Buscar categorías..." 
                           class="search-input w-full px-4 py-2.5 pl-11 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all"
                           @input="filtrarCategorias($event.target.value)">
                    <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="flex gap-3">
                <a href="{{ route('categorias.create') }}" 
                   class="inline-flex items-center justify-center px-5 py-2.5 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-semibold rounded-lg hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nueva Categoría
                </a>
            </div>
        </div>
    </div>

    <!-- Grid de Categorías Mejorado -->
    <div id="categoriasGrid" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse($categorias as $index => $categoria)
            <div class="category-card bg-white rounded-xl shadow-sm border border-gray-200 hover:border-[#218786] overflow-hidden group"
                 style="--index: {{ $index }}"
                 x-data="{ actionsOpen: false }"
                 data-nombre="{{ strtolower($categoria->nombre) }}"
                 data-descripcion="{{ strtolower($categoria->descripcion ?? '') }}">
                
                <!-- Header con gradiente -->
                <div class="h-2 bg-gradient-to-r from-[#218786] to-[#1a6d6c]"></div>
                
                <div class="p-6">
                    <!-- Título y badge -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-bold text-gray-900 truncate mb-1 group-hover:text-[#218786] transition-colors">
                                {{ $categoria->nombre }}
                            </h3>
                            <div class="flex items-center text-xs text-gray-500">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $categoria->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                        
                        @if($categoria->descripcion)
                            <span class="ml-2 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Completa
                            </span>
                        @else
                            <span class="ml-2 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Pendiente
                            </span>
                        @endif
                    </div>

                    <!-- Descripción -->
                    <div class="mb-4">
                        @if($categoria->descripcion)
                            <p class="text-sm text-gray-600 line-clamp-2 leading-relaxed">{{ $categoria->descripcion }}</p>
                        @else
                            <p class="text-sm text-gray-400 italic">Sin descripción disponible</p>
                        @endif
                    </div>

                    <!-- Stats -->
                    <div class="flex items-center justify-between py-3 border-t border-gray-100">
                        <div class="flex items-center text-sm">
                            <div class="flex items-center text-gray-600">
                                <svg class="w-4 h-4 mr-1.5 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <span class="font-semibold text-gray-900">{{ $categoria->repuestos_count ?? 0 }}</span>
                                <span class="ml-1">productos</span>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $categoria->updated_at->diffForHumans() }}
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex gap-2 mt-4">
                        <button @click="actionsOpen = !actionsOpen"
                                class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                            </svg>
                            Acciones
                            <svg class="w-4 h-4 ml-1 transform transition-transform duration-200" 
                                 :class="{ 'rotate-180': actionsOpen }" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Menú de acciones -->
                    <div x-show="actionsOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="mt-3 space-y-2"
                         x-cloak>
                        
                        <!-- Ver detalles -->
                        <button onclick='verDetalles(@json($categoria))'
                                class="w-full flex items-center px-3 py-2 text-sm text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Ver Detalles
                        </button>

                        <!-- Editar -->
                        <a href="{{ route('categorias.edit', $categoria) }}" 
                           class="w-full flex items-center px-3 py-2 text-sm text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar
                        </a>

                        <!-- Eliminar -->
                        <button onclick="confirmarEliminacion('{{ route('categorias.destroy', $categoria) }}', '{{ addslashes($categoria->nombre) }}', {{ $categoria->repuestos_count ?? 0 }})"
                                class="w-full flex items-center px-3 py-2 text-sm text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <!-- Estado vacío -->
            <div class="col-span-full">
                <div class="text-center py-16 bg-white rounded-xl border-2 border-dashed border-gray-300">
                    <div class="mx-auto w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No hay categorías registradas</h3>
                    <p class="text-gray-500 mb-8 max-w-md mx-auto">
                        Crea tu primera categoría para organizar tu inventario de repuestos de manera eficiente.
                    </p>
                    <a href="{{ route('categorias.create') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-semibold rounded-lg hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Crear Primera Categoría
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Mensaje de sin resultados (oculto por defecto) -->
    <div id="noResults" class="hidden col-span-full text-center py-12">
        <svg class="mx-auto w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">No se encontraron resultados</h3>
        <p class="text-gray-500">Intenta con otros términos de búsqueda</p>
    </div>
</div>

@push('scripts')
<script>
// Ver detalles mejorado
function verDetalles(categoria) {
    Swal.fire({
        title: `<div class="flex items-center">
            <div class="w-12 h-12 bg-gradient-to-br from-[#218786] to-[#1a6d6c] rounded-full flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <span>${categoria.nombre}</span>
        </div>`,
        html: `
            <div class="text-left space-y-4">
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-5 rounded-xl border border-gray-200">
                    <div class="space-y-3">
                        <div class="flex justify-between items-start py-2 border-b border-gray-200">
                            <span class="text-sm font-medium text-gray-600">Nombre:</span>
                            <span class="font-semibold text-gray-900 text-right">${categoria.nombre}</span>
                        </div>
                        <div class="flex justify-between items-start py-2 border-b border-gray-200">
                            <span class="text-sm font-medium text-gray-600">Descripción:</span>
                            <span class="text-gray-900 text-right max-w-xs">${categoria.descripcion || '<em class="text-gray-400">Sin descripción</em>'}</span>
                        </div>
                        <div class="flex justify-between items-start py-2 border-b border-gray-200">
                            <span class="text-sm font-medium text-gray-600">Productos:</span>
                            <span class="font-bold text-[#218786]">${categoria.repuestos_count || 0} items</span>
                        </div>
                        <div class="flex justify-between items-start py-2 border-b border-gray-200">
                            <span class="text-sm font-medium text-gray-600">Creada:</span>
                            <span class="text-gray-900">${new Date(categoria.created_at).toLocaleDateString('es-PE')}</span>
                        </div>
                        <div class="flex justify-between items-start py-2">
                            <span class="text-sm font-medium text-gray-600">Actualizada:</span>
                            <span class="text-gray-900">${new Date(categoria.updated_at).toLocaleDateString('es-PE')}</span>
                        </div>
                    </div>
                </div>
            </div>
        `,
        width: 600,
        showCancelButton: false,
        confirmButtonText: '<svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>Cerrar',
        confirmButtonColor: '#218786',
        customClass: {
            popup: 'rounded-2xl',
            title: 'text-left',
            confirmButton: 'rounded-lg px-6 py-2.5 font-semibold'
        }
    });
}

// Confirmar eliminación mejorado
function confirmarEliminacion(url, nombre, productosCount) {
    const tieneProductos = productosCount > 0;
    
    Swal.fire({
        title: '¿Eliminar categoría?',
        html: `
            <div class="text-left space-y-4">
                <p class="text-gray-600">Estás a punto de eliminar la categoría:</p>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <p class="font-bold text-gray-900 text-lg">"${nombre}"</p>
                    ${tieneProductos ? `<p class="text-sm text-gray-600 mt-2">📦 Contiene <strong>${productosCount}</strong> producto${productosCount > 1 ? 's' : ''}</p>` : ''}
                </div>
                <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-red-800">⚠️ Advertencia</p>
                            <p class="text-sm text-red-700 mt-1">
                                Esta acción no se puede deshacer. ${tieneProductos ? 'Los productos asociados quedarán sin categoría.' : ''}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'rounded-lg px-6 py-2.5 font-semibold',
            cancelButton: 'rounded-lg px-6 py-2.5 font-semibold'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Eliminando...',
                html: '<div class="flex justify-center"><div class="animate-spin w-10 h-10 border-4 border-[#218786] border-t-transparent rounded-full"></div></div>',
                allowOutsideClick: false,
                showConfirmButton: false
            });
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Búsqueda mejorada
function filtrarCategorias(termino) {
    const cards = document.querySelectorAll('.category-card');
    const grid = document.getElementById('categoriasGrid');
    const noResults = document.getElementById('noResults');
    let visibleCount = 0;
    
    cards.forEach(card => {
        const nombre = card.dataset.nombre;
        const descripcion = card.dataset.descripcion;
        
        if (nombre.includes(termino.toLowerCase()) || descripcion.includes(termino.toLowerCase())) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    if (visibleCount === 0 && termino.length > 0) {
        grid.classList.add('hidden');
        noResults.classList.remove('hidden');
    } else {
        grid.classList.remove('hidden');
        noResults.classList.add('hidden');
    }
}

// Atajos de teclado
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.getElementById('searchInput').focus();
    }
    
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        window.location.href = '{{ route("categorias.create") }}';
    }
});

// Tooltip para atajos
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.setAttribute('title', 'Atajo: Ctrl/Cmd + K');
    }
});
</script>
@endpush
@endsection
