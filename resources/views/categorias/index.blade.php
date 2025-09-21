@extends('layouts.app')

@section('title', 'Categorías')
@section('page-title', 'Gestión de Categorías')
@section('page-description', 'Organiza y administra las categorías de tus repuestos')

@section('breadcrumbs')
<nav class="flex items-center space-x-2 text-sm text-gray-500">
    <a href="{{ route('dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
    <span class="font-medium text-gray-900">Categorías</span>
</nav>
@endsection

@push('styles')
<style>
    /* Animaciones personalizadas */
    .category-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        animation: slideUp 0.5s ease-out;
        animation-fill-mode: both;
    }
    
    .category-card:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .category-card:nth-child(1) { animation-delay: 0.1s; }
    .category-card:nth-child(2) { animation-delay: 0.2s; }
    .category-card:nth-child(3) { animation-delay: 0.3s; }
    .category-card:nth-child(4) { animation-delay: 0.4s; }
    .category-card:nth-child(5) { animation-delay: 0.5s; }
    .category-card:nth-child(6) { animation-delay: 0.6s; }
    
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
    
    /* Efectos de botones */
    .actions-toggle {
        transition: all 0.2s ease;
    }
    
    .actions-toggle:hover {
        transform: translateY(-1px);
    }
    
    .actions-menu {
        animation: slideDown 0.3s ease-out;
        animation-fill-mode: both;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Stats de categorías -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Categorías</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $categorias->count() }}</p>
                </div>
                <div class="p-3 bg-primary-100 rounded-full">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Con Descripción</p>
                    <p class="text-3xl font-bold text-green-600">
                        {{ $categorias->filter(function($cat) { return !empty($cat->descripcion); })->count() }}
                    </p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Sin Descripción</p>
                    <p class="text-3xl font-bold text-yellow-600">
                        {{ $categorias->filter(function($cat) { return empty($cat->descripcion); })->count() }}
                    </p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Más Reciente</p>
                    <p class="text-lg font-bold text-purple-600">
                        {{ $categorias->sortByDesc('created_at')->first()?->created_at?->format('d/m/Y') ?? 'N/A' }}
                    </p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de acciones -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <!-- Título y descripción -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Lista de Categorías</h2>
                <p class="text-sm text-gray-600">Administra las categorías para organizar tu inventario</p>
            </div>
            
            <!-- Acciones -->
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('categorias.create') }}" 
                   class="inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-medium rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nueva Categoría
                </a>
                
                <button class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Exportar
                </button>
            </div>
        </div>
    </div>

    <!-- Grid de categorías -->
    <div class="grid gap-6 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse($categorias as $categoria)
            <div class="category-card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden"
                 x-data="{ actionsOpen: false }">
                
                <!-- Header de la tarjeta -->
                <div class="p-6 pb-4">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 truncate mb-1">
                                {{ $categoria->nombre }}
                            </h3>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $categoria->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                        
                        <!-- Indicador de estado -->
                        <div class="ml-4">
                            @if($categoria->descripcion)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Completa
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Incompleta
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-4">
                        @if($categoria->descripcion)
                            <p class="text-gray-600 text-sm line-clamp-2">{{ $categoria->descripcion }}</p>
                        @else
                            <p class="text-gray-400 text-sm italic">Sin descripción disponible</p>
                        @endif
                    </div>

                    <!-- Estadísticas de la categoría -->
                    <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <span>{{ $categoria->repuestos_count ?? 0 }} repuestos</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ $categoria->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Botón de acciones -->
                <div class="px-6 pb-4">
                    <button @click="actionsOpen = !actionsOpen"
                            class="actions-toggle w-full flex items-center justify-between px-4 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-700 font-medium rounded-lg transition-all duration-200 border border-gray-200 hover:border-gray-300">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                            </svg>
                            Acciones
                        </span>
                        <svg class="w-4 h-4 transform transition-transform duration-200" 
                             :class="{ 'rotate-180': actionsOpen }" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>

                <!-- Menú de acciones -->
                <div x-show="actionsOpen" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="actions-menu px-6 pb-6">
                    <div class="space-y-2">
                        <!-- Ver detalles -->
                        <button onclick="verDetalles({{ $categoria->id }}, '{{ addslashes($categoria->nombre) }}', '{{ addslashes($categoria->descripcion ?? '') }}', '{{ $categoria->created_at->format('d/m/Y H:i') }}', '{{ $categoria->updated_at->format('d/m/Y H:i') }}')"
                                class="w-full flex items-center px-3 py-2 text-sm text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Ver Detalles
                        </button>

                        <!-- Editar -->
                        <a href="{{ route('categorias.edit', $categoria) }}" 
                           class="w-full flex items-center px-3 py-2 text-sm text-yellow-700 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-3 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar Categoría
                        </a>

                        <!-- Eliminar -->
                        <button onclick="confirmarEliminacion('{{ route('categorias.destroy', $categoria) }}', '{{ addslashes($categoria->nombre) }}')"
                                class="w-full flex items-center px-3 py-2 text-sm text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Eliminar Categoría
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <!-- Estado vacío mejorado -->
            <div class="col-span-full">
                <div class="text-center py-12">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay categorías registradas</h3>
                    <p class="text-gray-500 mb-6 max-w-md mx-auto">
                        Crea tu primera categoría para empezar a organizar tu inventario de repuestos de manera eficiente.
                    </p>
                    <a href="{{ route('categorias.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Crear primera categoría
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
// Función para ver detalles de la categoría
function verDetalles(id, nombre, descripcion, fechaCreacion, fechaActualizacion) {
    Swal.fire({
        title: '<strong>Detalles de la Categoría</strong>',
        html: `
            <div class="text-left space-y-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-900 mb-3">Información General</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nombre:</span>
                            <span class="font-medium text-gray-900">${nombre}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Descripción:</span>
                            <span class="font-medium text-gray-900">${descripcion || 'Sin descripción'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Fecha de creación:</span>
                            <span class="font-medium text-gray-900">${fechaCreacion}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Última actualización:</span>
                            <span class="font-medium text-gray-900">${fechaActualizacion}</span>
                        </div>
                    </div>
                </div>
            </div>
        `,
        width: 600,
        showCancelButton: false,
        confirmButtonText: 'Cerrar',
        confirmButtonColor: '#218786',
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'rounded-lg px-6 py-2.5'
        }
    });
}

// Función mejorada para confirmar eliminación
function confirmarEliminacion(url, nombre) {
    Swal.fire({
        title: '¿Estás seguro?',
        html: `
            <div class="text-left">
                <p class="mb-2">Estás a punto de eliminar la categoría:</p>
                <p class="font-semibold text-gray-900 mb-4">"${nombre}"</p>
                <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                    <p class="text-sm text-red-700">
                        <strong>⚠️ Advertencia:</strong> Esta acción no se puede deshacer. 
                        Si hay repuestos asociados a esta categoría, se desasociarán automáticamente.
                    </p>
                </div>
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
                title: 'Eliminando categoría...',
                html: 'Por favor espera mientras procesamos la eliminación',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Crear y enviar formulario de eliminación
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Cerrar menús de acciones al hacer clic fuera
document.addEventListener('click', function(event) {
    const categoryCards = document.querySelectorAll('[x-data]');
    categoryCards.forEach(card => {
        if (!card.contains(event.target)) {
            // Usar Alpine.js para cerrar el menú
            const alpineData = Alpine.getScope(card);
            if (alpineData && alpineData.actionsOpen) {
                alpineData.actionsOpen = false;
            }
        }
    });
});

// Atajos de teclado
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + N para nueva categoría
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        window.location.href = '{{ route("categorias.create") }}';
    }
    
    // Escape para cerrar todos los menús
    if (e.key === 'Escape') {
        const categoryCards = document.querySelectorAll('[x-data]');
        categoryCards.forEach(card => {
            const alpineData = Alpine.getScope(card);
            if (alpineData && alpineData.actionsOpen) {
                alpineData.actionsOpen = false;
            }
        });
    }
});

// Animación de entrada para las tarjetas
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
    
    document.querySelectorAll('.category-card').forEach(card => {
        card.style.animationPlayState = 'paused';
        observer.observe(card);
    });
});

// Efecto de hover mejorado para las tarjetas
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.category-card');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            // Efecto parallax sutil en la tarjeta
            this.style.transform = 'translateY(-4px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});

// Función para exportar categorías (placeholder)
function exportarCategorias() {
    Swal.fire({
        title: 'Exportar Categorías',
        text: 'Selecciona el formato de exportación',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'PDF',
        cancelButtonText: 'Excel',
        confirmButtonColor: '#218786',
        cancelButtonColor: '#059669',
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'rounded-lg px-6 py-2.5',
            cancelButton: 'rounded-lg px-6 py-2.5'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Exportar como PDF
            window.location.href = '{{ route("categorias.export") }}?format=pdf';
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            // Exportar como Excel
            window.location.href = '{{ route("categorias.export") }}?format=excel';
        }
    });
}

// Función de búsqueda en tiempo real (si se implementa)
function filtrarCategorias(termino) {
    const cards = document.querySelectorAll('.category-card');
    
    cards.forEach(card => {
        const nombre = card.querySelector('h3').textContent.toLowerCase();
        const descripcion = card.querySelector('p').textContent.toLowerCase();
        
        if (nombre.includes(termino.toLowerCase()) || descripcion.includes(termino.toLowerCase())) {
            card.style.display = 'block';
            card.style.animation = 'slideUp 0.3s ease-out';
        } else {
            card.style.display = 'none';
        }
    });
}

// Tooltips para elementos interactivos
document.addEventListener('DOMContentLoaded', function() {
    // Agregar tooltips simples a los iconos de estado
    const badges = document.querySelectorAll('.badge-tooltip');
    badges.forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'absolute z-50 px-2 py-1 bg-gray-900 text-white text-xs rounded shadow-lg';
            tooltip.textContent = this.dataset.tooltip;
            tooltip.style.top = this.offsetTop - 30 + 'px';
            tooltip.style.left = this.offsetLeft + 'px';
            this.parentElement.appendChild(tooltip);
            
            setTimeout(() => tooltip.remove(), 3000);
        });
    });
});
</script>
@endpush

@endsection