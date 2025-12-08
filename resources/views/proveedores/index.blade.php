@extends('layouts.app')

@section('title', 'Proveedores')

@push('styles')
<style>
    .card-up { animation: slideUp 0.4s ease-out; }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .card-hover { transition: all 0.3s ease; }
    .card-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 24px -8px rgba(33, 135, 134, 0.2); }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="{ search: '' }">
    
    <!-- Header con Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card-up card-hover bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-500">Total</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $proveedores->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card-up card-hover bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-500">Nuevos (Mes)</p>
                    <p class="text-2xl font-bold text-[#218786] mt-1">{{ $proveedores->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#218786] to-[#1a6d6c] flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card-up card-hover bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-500">Con Teléfono</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $proveedores->whereNotNull('telefono')->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card-up card-hover bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-500">Con Email</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ $proveedores->whereNotNull('contacto')->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de Búsqueda y Acciones -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input x-model="search" 
                       type="text" 
                       class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-transparent" 
                       placeholder="Buscar por nombre, email o teléfono...">
            </div>
            
            <a href="{{ route('proveedores.create') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-xl hover:from-green-600 hover:to-green-700 transition-all shadow-lg hover:shadow-xl hover:scale-105 active:scale-95">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nuevo Proveedor
            </a>
        </div>
    </div>

    <!-- Lista de Proveedores -->
    <div class="space-y-3">
        @forelse($proveedores as $index => $proveedor)
            <div class="card-up bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all"
                 style="animation-delay: {{ $index * 0.05 }}s"
                 x-show="search === '' || 
                         '{{ strtolower($proveedor->nombre) }}'.includes(search.toLowerCase()) || 
                         '{{ strtolower($proveedor->contacto ?? '') }}'.includes(search.toLowerCase()) || 
                         '{{ $proveedor->telefono ?? '' }}'.includes(search)"
                 x-data="{ open: false }">
                
                <!-- Header Card -->
                <div class="p-5 cursor-pointer hover:bg-gray-50 transition-colors" @click="open = !open">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4 flex-1 min-w-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-gray-900 truncate">{{ $proveedor->nombre }}</h3>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-600 mt-1">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        {{ $proveedor->telefono ?? 'Sin teléfono' }}
                                    </span>
                                    
                                    @if($proveedor->contacto)
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="truncate max-w-[200px]">{{ $proveedor->contacto }}</span>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2 flex-shrink-0">
                            @if($proveedor->contacto)
                            <span class="hidden md:inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-100 text-blue-700">
                                Email
                            </span>
                            @endif
                            @if($proveedor->telefono)
                            <span class="hidden md:inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-green-100 text-green-700">
                                Tel
                            </span>
                            @endif
                            
                            <svg class="w-5 h-5 text-gray-400 transition-transform duration-300" 
                                 :class="{ 'rotate-180': open }"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Detalles Expandibles -->
                <div x-show="open" 
                     x-collapse
                     class="border-t border-gray-100">
                    <div class="p-5 bg-gray-50">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-5">
                            <!-- Info de Contacto -->
                            <div class="bg-white rounded-xl p-4 border border-gray-200">
                                <h4 class="font-bold text-gray-900 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    Contacto
                                </h4>
                                <div class="space-y-3 text-sm">
                                    <div>
                                        <p class="text-gray-500 text-xs mb-1">Teléfono</p>
                                        <p class="font-semibold text-gray-900">{{ $proveedor->telefono ?: 'No especificado' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-xs mb-1">Email</p>
                                        <p class="font-semibold text-gray-900">{{ $proveedor->contacto ?: 'No especificado' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-xs mb-1">Dirección</p>
                                        <p class="font-semibold text-gray-900">{{ $proveedor->direccion ?: 'No especificada' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Info del Sistema -->
                            <div class="bg-white rounded-xl p-4 border border-gray-200">
                                <h4 class="font-bold text-gray-900 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Sistema
                                </h4>
                                <div class="space-y-3 text-sm">
                                    <div>
                                        <p class="text-gray-500 text-xs mb-1">ID Proveedor</p>
                                        <p class="font-semibold text-gray-900">#{{ str_pad($proveedor->id, 4, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-xs mb-1">Registrado</p>
                                        <p class="font-semibold text-gray-900">{{ $proveedor->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-xs mb-1">Última actualización</p>
                                        <p class="font-semibold text-gray-900">{{ $proveedor->updated_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('proveedores.edit', $proveedor->id) }}" 
                               class="inline-flex items-center justify-center px-4 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-105 active:scale-95">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Editar
                            </a>
                            
                            <form action="{{ route('proveedores.destroy', $proveedor->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" 
                                        onclick="confirmDelete(this, '{{ addslashes($proveedor->nombre) }}')"
                                        class="inline-flex items-center justify-center px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-105 active:scale-95">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <!-- Estado Vacío -->
            <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-gray-200">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No hay proveedores registrados</h3>
                <p class="text-gray-600 mb-6">Comienza agregando tu primer proveedor</p>
                <a href="{{ route('proveedores.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-semibold rounded-xl hover:shadow-xl transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Registrar Primer Proveedor
                </a>
            </div>
        @endforelse
    </div>

     <!-- 👇 PAGINACIÓN AÑADIDA AQUÍ -->
    @if($proveedores->hasPages())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <!-- Info de resultados -->
                <div class="text-sm text-gray-600">
                    Mostrando 
                    <span class="font-bold text-[#218786]">{{ $proveedores->firstItem() }}</span>
                    a 
                    <span class="font-bold text-[#218786]">{{ $proveedores->lastItem() }}</span>
                    de 
                    <span class="font-bold text-[#218786]">{{ $proveedores->total() }}</span>
                    proveedores
                </div>

                <!-- Links de paginación -->
                <div class="flex items-center gap-2">
                    {{-- Botón Anterior --}}
                    @if ($proveedores->onFirstPage())
                        <span class="px-4 py-2 text-sm font-semibold text-gray-400 bg-gray-100 rounded-xl cursor-not-allowed">
                            ← Anterior
                        </span>
                    @else
                        <a href="{{ $proveedores->previousPageUrl() }}" 
                           class="px-4 py-2 text-sm font-semibold text-[#218786] bg-white border-2 border-[#218786] rounded-xl hover:bg-[#218786] hover:text-white transition-all">
                            ← Anterior
                        </a>
                    @endif

                    {{-- Números de página --}}
                    <div class="hidden sm:flex items-center gap-2">
                        @php
                            $start = max($proveedores->currentPage() - 2, 1);
                            $end = min($proveedores->currentPage() + 2, $proveedores->lastPage());
                        @endphp
                        
                        @if($start > 1)
                            <a href="{{ $proveedores->url(1) }}" 
                               class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:border-[#218786] hover:text-[#218786] transition-all">
                                1
                            </a>
                            @if($start > 2)
                                <span class="px-2 text-gray-500">...</span>
                            @endif
                        @endif

                        @for ($page = $start; $page <= $end; $page++)
                            @if ($page == $proveedores->currentPage())
                                <span class="px-4 py-2 text-sm font-bold text-white bg-gradient-to-r from-[#218786] to-[#1a6d6c] rounded-xl shadow-lg">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $proveedores->url($page) }}" 
                                   class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:border-[#218786] hover:text-[#218786] transition-all">
                                    {{ $page }}
                                </a>
                            @endif
                        @endfor

                        @if($end < $proveedores->lastPage())
                            @if($end < $proveedores->lastPage() - 1)
                                <span class="px-2 text-gray-500">...</span>
                            @endif
                            <a href="{{ $proveedores->url($proveedores->lastPage()) }}" 
                               class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:border-[#218786] hover:text-[#218786] transition-all">
                                {{ $proveedores->lastPage() }}
                            </a>
                        @endif
                    </div>

                    {{-- Botón Siguiente --}}
                    @if ($proveedores->hasMorePages())
                        <a href="{{ $proveedores->nextPageUrl() }}" 
                           class="px-4 py-2 text-sm font-semibold text-[#218786] bg-white border-2 border-[#218786] rounded-xl hover:bg-[#218786] hover:text-white transition-all">
                            Siguiente →
                        </a>
                    @else
                        <span class="px-4 py-2 text-sm font-semibold text-gray-400 bg-gray-100 rounded-xl cursor-not-allowed">
                            Siguiente →
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endif
    <!-- 👆 HASTA AQUÍ -->
</div>

@push('scripts')
<script>
function confirmDelete(button, nombre) {
    const form = button.closest('form');
    
    Swal.fire({
        title: '¿Eliminar proveedor?',
        html: `Estás a punto de eliminar a:<br><strong class="text-lg">"${nombre}"</strong><br><br><span class="text-sm text-red-600">Esta acción no se puede deshacer.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#218786',
            timer: 3000
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            confirmButtonColor: '#218786'
        });
    @endif
});
</script>
@endpush
@endsection