@extends('layouts.app')

@section('title', 'Gestión de Proveedores')
@section('page-title', 'Gestión de Proveedores')
@section('page-description', 'Administra y gestiona tus proveedores de manera eficiente')

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
    <span class="font-medium text-gray-900">Proveedores</span>
</nav>
@endsection

@push('styles')
{{-- Estilos para las animaciones de las tarjetas (igual que en Clientes) --}}
<style>
    .proveedor-card {
        animation: slideUp 0.5s ease-out;
        animation-fill-mode: both;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .proveedor-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .proveedor-details {
        animation: expandDetails 0.4s ease-out;
        transform-origin: top;
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
    
    @keyframes expandDetails {
        from {
            opacity: 0;
            transform: scaleY(0);
        }
        to {
            opacity: 1;
            transform: scaleY(1);
        }
    }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="proveedorManager()">
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-600">Total Proveedores</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $proveedores->count() }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-600">Nuevos (Mes)</p>
                    <p class="text-2xl font-bold text-primary-600 mt-1">{{ $proveedores->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count() }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-600">Con Teléfono</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $proveedores->whereNotNull('telefono')->count() }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-600">Ubicaciones</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ $proveedores->pluck('direccion')->unique()->count() }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="relative w-full md:w-80">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input x-model="searchTerm" 
                       @input="filterProveedores"
                       type="text" 
                       id="searchInput"
                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-full" 
                       placeholder="Buscar por nombre, contacto o teléfono...">
            </div>
            
            <a href="{{ route('proveedores.create') }}" 
               class="inline-flex items-center justify-center px-4 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md w-full md:w-auto">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Agregar Proveedor
            </a>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($proveedores as $proveedor)
            <div class="proveedor-card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden"
                 data-nombre="{{ strtolower($proveedor->nombre) }}" 
                 data-contacto="{{ strtolower($proveedor->contacto ?? '') }}"
                 data-telefono="{{ $proveedor->telefono ?? '' }}">
                
                <div class="p-5 cursor-pointer hover:bg-gray-50 transition-colors"
                     onclick="toggleDetails({{ $proveedor->id }})">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">{{ $proveedor->nombre }}</h3>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3 text-sm text-gray-500 mt-1">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        {{ $proveedor->telefono ?? 'Sin teléfono' }}
                                    </span>
                                    
                                    @if($proveedor->contacto)
                                    <span class="hidden sm:inline-block">•</span>
                                    <span class="flex items-center mt-1 sm:mt-0">
                                         <svg class="w-4 h-4 mr-1 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        <span class="truncate" style="max-width: 200px;">{{ $proveedor->contacto }}</span>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            @if($proveedor->contacto)
                            <span class="hidden xl:inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Contacto
                            </span>
                            @endif
                            @if($proveedor->telefono)
                            <span class="hidden xl:inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                 <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                Tel
                            </span>
                            @endif
                            
                            <svg id="arrow-{{ $proveedor->id }}" class="w-5 h-5 text-gray-400 transform transition-transform duration-300 flex-shrink-0" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div id="details-{{ $proveedor->id }}" class="hidden border-t border-gray-100">
                    <div class="p-5 bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <h4 class="font-semibold text-gray-900 mb-3">Información de Contacto</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <span class="text-gray-600">Teléfono:</span>
                                        <span class="ml-2 font-medium">{{ $proveedor->telefono ?: 'No especificado' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        <span class="text-gray-600">Contacto/Email:</span>
                                        <span class="ml-2 font-medium">{{ $proveedor->contacto ?: 'No especificado' }}</span>
                                    </div>
                                    <div class="flex items-start">
                                        <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span class="text-gray-600">Dirección:</span>
                                        <span class="ml-2 font-medium">{{ $proveedor->direccion ?: 'No especificada' }}</span>
                                    </div>
                                </div>
                            </div>
                             <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <h4 class="font-semibold text-gray-900 mb-3">Información del Sistema</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">ID Proveedor:</span>
                                        <span class="font-medium">#{{ str_pad($proveedor->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Registrado:</span>
                                        <span class="font-medium">{{ $proveedor->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Actualizado:</span>
                                        <span class="font-medium">{{ $proveedor->updated_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('proveedores.edit', $proveedor->id) }}" 
                               class="inline-flex items-center px-3 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors duration-200 text-sm">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Editar
                            </a>
                            
                            <form action="{{ route('proveedores.destroy', $proveedor->id) }}" method="POST" class="inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" 
                                        class="inline-flex items-center px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-200 text-sm"
                                        onclick="confirmDelete(this, '{{ addslashes($proveedor->nombre) }}')">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No hay proveedores registrados</h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto">
                    Comienza agregando tu primer proveedor para gestionar tus contactos.
                </p>
                <a href="{{ route('proveedores.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Registrar primer proveedor
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
{{-- SweetAlert2 se carga desde app.blade.php y maneja los mensajes de sesión --}}

<script>
// Función para alternar detalles
function toggleDetails(id) {
    const details = document.getElementById(`details-${id}`);
    const arrow = document.getElementById(`arrow-${id}`);
    
    if (details.classList.contains('hidden')) {
        details.classList.remove('hidden');
        arrow.classList.add('rotate-180');
        details.classList.add('proveedor-details');
    } else {
        details.classList.add('hidden');
        arrow.classList.remove('rotate-180');
        details.classList.remove('proveedor-details');
    }
}

// Manager de Alpine.js para filtros
function proveedorManager() {
    return {
        searchTerm: '',
        
        filterProveedores() {
            const proveedores = document.querySelectorAll('.proveedor-card'); 
            
            proveedores.forEach(proveedor => {
                const nombre = proveedor.dataset.nombre;
                const contacto = proveedor.dataset.contacto;
                const telefono = proveedor.dataset.telefono;
                
                const busqueda = this.searchTerm.toLowerCase();
                
                const match = nombre.includes(busqueda) || 
                              contacto.includes(busqueda) || 
                              telefono.includes(busqueda);
                
                proveedor.style.display = match ? 'block' : 'none';
            });
        }
    }
}

// Función de borrado (SweetAlert)
function confirmDelete(button, nombre) {
    const form = button.closest('form');
    
    Swal.fire({
        title: '¿Eliminar proveedor?',
        html: `Estás a punto de eliminar a:<br><strong class="font-semibold text-gray-900">"${nombre}"</strong><br><br><span class="text-sm text-red-600">Esta acción no se puede deshacer.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#218786', // Color primario
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
};

document.addEventListener('DOMContentLoaded', function() {
    // Atajos de teclado
    const searchInput = document.getElementById('searchInput');
    
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'f')) { 
            e.preventDefault();
            searchInput?.focus();
        }
        
        if (e.key === 'Escape' && document.activeElement === searchInput) {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
    });
});
</script>
@endpush