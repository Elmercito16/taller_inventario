@extends('layouts.app')

@section('title', 'Clientes')
@section('page-title', 'Gestión de Clientes')
@section('page-description', 'Administra la información de tus clientes registrados')

@section('breadcrumbs')
<nav class="flex items-center space-x-2 text-sm text-gray-500">
    <a href="{{ route('dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
    <span class="font-medium text-gray-900">Clientes</span>
</nav>
@endsection

@push('styles')
<style>
    /* Animaciones personalizadas */
    .cliente-card {
        animation: slideUp 0.5s ease-out;
        animation-fill-mode: both;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .cliente-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .cliente-card:nth-child(1) { animation-delay: 0.1s; }
    .cliente-card:nth-child(2) { animation-delay: 0.2s; }
    .cliente-card:nth-child(3) { animation-delay: 0.3s; }
    .cliente-card:nth-child(4) { animation-delay: 0.4s; }
    .cliente-card:nth-child(5) { animation-delay: 0.5s; }
    .cliente-card:nth-child(6) { animation-delay: 0.6s; }
    
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
    
    /* Efectos de expansión */
    .cliente-details {
        animation: expandDetails 0.4s ease-out;
        transform-origin: top;
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
    
    /* Estados de actividad */
    .cliente-activo { @apply border-green-200 bg-green-50; }
    .cliente-inactivo { @apply border-gray-200 bg-gray-50; }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="clientesManager()">
    <!-- Stats Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Clientes</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $clientes->count() }}</p>
                </div>
                <div class="p-3 bg-primary-100 rounded-full">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Con Email</p>
                    <p class="text-3xl font-bold text-blue-600">
                        {{ $clientes->filter(function($cliente) { return !empty($cliente->email); })->count() }}
                    </p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Con Teléfono</p>
                    <p class="text-3xl font-bold text-green-600">
                        {{ $clientes->filter(function($cliente) { return !empty($cliente->telefono); })->count() }}
                    </p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Registrados Hoy</p>
                    <p class="text-3xl font-bold text-purple-600">
                        {{ $clientes->filter(function($cliente) { 
                            return $cliente->created_at->isToday(); 
                        })->count() }}
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

    <!-- Barra de acciones y filtros -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <!-- Filtros y búsqueda -->
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input x-model="searchTerm" 
                           @input="filterClientes"
                           type="text" 
                           placeholder="Buscar por nombre o DNI..."
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-full sm:w-64">
                </div>
                
                <select x-model="filterType" @change="filterClientes" 
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="todos">Todos los clientes</option>
                    <option value="con_email">Con email</option>
                    <option value="con_telefono">Con teléfono</option>
                    <option value="sin_contacto">Sin contacto completo</option>
                </select>
                
                <button @click="clearFilters" 
                        class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Limpiar
                </button>
            </div>

            <!-- Acciones -->
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('clientes.create') }}" 
                   class="inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-medium rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nuevo Cliente
                </a>
                
                <button onclick="exportarClientes()" 
                        class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Exportar
                </button>
            </div>
        </div>
    </div>

    <!-- Lista de clientes -->
    <div class="space-y-4" id="clientes-container">
        @forelse ($clientes as $cliente)
            <div class="cliente-card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden cliente-item" 
                 data-nombre="{{ strtolower($cliente->nombre) }}" 
                 data-dni="{{ $cliente->dni }}"
                 data-email="{{ $cliente->email ? 'true' : 'false' }}"
                 data-telefono="{{ $cliente->telefono ? 'true' : 'false' }}">
                
                <!-- Header del cliente -->
                <div class="p-6 cursor-pointer hover:bg-gray-50 transition-colors" 
                     onclick="toggleClienteDetails({{ $cliente->id }})">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <!-- Avatar -->
                            <div class="w-12 h-12 bg-gradient-to-r from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                {{ strtoupper(substr($cliente->nombre, 0, 1)) }}
                            </div>
                            
                            <!-- Información básica -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $cliente->nombre }}</h3>
                                <div class="flex items-center space-x-3 text-sm text-gray-500 mt-1">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 011-1h2a2 2 0 011 1v2m-4 0a2 2 0 01-1 1h-2a2 2 0 01-1-1m0 0V4a2 2 0 011-1h2a2 2 0 011 1v2"/>
                                        </svg>
                                        {{ $cliente->dni }}
                                    </span>
                                    <span>•</span>
                                    <span>{{ $cliente->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Indicadores y flecha -->
                        <div class="flex items-center space-x-3">
                            <!-- Badges de contacto -->
                            <div class="flex space-x-2">
                                @if($cliente->email)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        Email
                                    </span>
                                @endif
                                
                                @if($cliente->telefono)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        Tel
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Flecha -->
                            <svg id="arrow-{{ $cliente->id }}" 
                                 class="w-5 h-5 text-gray-400 transform transition-transform duration-300" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Detalles expandibles -->
                <div id="details-{{ $cliente->id }}" class="hidden border-t border-gray-100">
                    <div class="p-6 bg-gray-50">
                        <!-- Información detallada -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-white rounded-lg p-4">
                                <h4 class="font-semibold text-gray-900 mb-3">Información de Contacto</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-gray-600">Email:</span>
                                        <span class="ml-2 font-medium">{{ $cliente->email ?: 'No especificado' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <span class="text-gray-600">Teléfono:</span>
                                        <span class="ml-2 font-medium">{{ $cliente->telefono ?: 'No especificado' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="text-gray-600">Dirección:</span>
                                        <span class="ml-2 font-medium">{{ $cliente->direccion ?: 'No especificada' }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-white rounded-lg p-4">
                                <h4 class="font-semibold text-gray-900 mb-3">Información del Sistema</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Registrado:</span>
                                        <span class="font-medium">{{ $cliente->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Actualizado:</span>
                                        <span class="font-medium">{{ $cliente->updated_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Ventas registradas:</span>
                                        <span class="font-medium text-primary-600">{{ $cliente->ventas_count ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('clientes.edit', $cliente->id) }}" 
                               class="inline-flex items-center px-3 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors duration-200 text-sm">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Editar
                            </a>

                            @if(Route::has('clientes.historial'))
                                <a href="{{ route('clientes.historial', $cliente->id) }}" 
                                   class="inline-flex items-center px-3 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors duration-200 text-sm">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Ver Historial
                                </a>
                            @endif

                            <button onclick="contactarCliente('{{ $cliente->telefono }}', '{{ $cliente->email }}')" 
                                    class="inline-flex items-center px-3 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200 text-sm">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                Contactar
                            </button>

                            <button onclick="confirmarEliminacion('{{ route('clientes.destroy', $cliente->id) }}', '{{ addslashes($cliente->nombre) }}')" 
                                    class="inline-flex items-center px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-200 text-sm">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <!-- Estado vacío -->
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No hay clientes registrados</h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto">
                    Registra tu primer cliente para comenzar a gestionar tu base de datos de clientes.
                </p>
                <a href="{{ route('clientes.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Registrar primer cliente
                </a>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
// Función para alternar detalles del cliente
function toggleClienteDetails(clienteId) {
    const details = document.getElementById(`details-${clienteId}`);
    const arrow = document.getElementById(`arrow-${clienteId}`);
    
    if (details.classList.contains('hidden')) {
        details.classList.remove('hidden');
        arrow.classList.add('rotate-180');
        details.classList.add('cliente-details');
    } else {
        details.classList.add('hidden');
        arrow.classList.remove('rotate-180');
        details.classList.remove('cliente-details');
    }
}

// Función para contactar cliente
function contactarCliente(telefono, email) {
    const opciones = [];
    
    if (telefono) {
        opciones.push({
            text: `📞 Llamar a ${telefono}`,
            action: () => window.open(`tel:${telefono}`)
        });
        opciones.push({
            text: `💬 WhatsApp a ${telefono}`,
            action: () => window.open(`https://wa.me/51${telefono.replace(/\D/g, '')}`)
        });
    }
    
    if (email) {
        opciones.push({
            text: `✉️ Enviar email a ${email}`,
            action: () => window.open(`mailto:${email}`)
        });
    }
    
    if (opciones.length === 0) {
        Swal.fire({
            icon: 'info',
            title: 'Sin información de contacto',
            text: 'Este cliente no tiene teléfono ni email registrado',
            confirmButtonColor: '#218786'
        });
        return;
    }
    
    if (opciones.length === 1) {
        opciones[0].action();
        return;
    }
    
    // Múltiples opciones
    const buttonsHtml = opciones.map((opcion, index) => 
        `<button onclick="contactActions[${index}]()" class="w-full mb-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">${opcion.text}</button>`
    ).join('');
    
    // Guardar acciones en variable global temporal
    window.contactActions = opciones.map(opcion => opcion.action);
    
    Swal.fire({
        title: 'Selecciona cómo contactar',
        html: buttonsHtml,
        showConfirmButton: false,
        showCloseButton: true,
        customClass: {
            popup: 'rounded-xl',
        }
    });
}

// Función para confirmar eliminación
function confirmarEliminacion(url, nombre) {
    Swal.fire({
        title: '¿Eliminar cliente?',
        html: `
            <div class="text-left">
                <p class="mb-2">Estás a punto de eliminar al cliente:</p>
                <p class="font-semibold text-gray-900 mb-4">"${nombre}"</p>
                <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                    <p class="text-sm text-red-700">
                        <strong>⚠️ Advertencia:</strong> Esta acción no se puede deshacer. 
                        El historial de ventas se mantendrá, pero la información del cliente se eliminará permanentemente.
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
                title: 'Eliminando cliente...',
                text: 'Por favor espera mientras procesamos la eliminación',
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

// Función para exportar clientes
function exportarClientes() {
    Swal.fire({
        title: 'Exportar Lista de Clientes',
        text: 'Esta funcionalidad estará disponible próximamente',
        icon: 'info',
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#218786'
    });
}

// Manager de Alpine.js para filtros
function clientesManager() {
    return {
        searchTerm: '',
        filterType: 'todos',
        
        filterClientes() {
            const clientes = document.querySelectorAll('.cliente-item');
            
            clientes.forEach(cliente => {
                const nombre = cliente.dataset.nombre;
                const dni = cliente.dataset.dni;
                const tieneEmail = cliente.dataset.email === 'true';
                const tieneTelefono = cliente.dataset.telefono === 'true';
                
                let mostrar = true;
                
                // Filtro por texto
                if (this.searchTerm.length > 0) {
                    const busqueda = this.searchTerm.toLowerCase();
                    mostrar = nombre.includes(busqueda) || dni.includes(busqueda);
                }
                
                // Filtro por tipo
                if (mostrar && this.filterType !== 'todos') {
                    switch (this.filterType) {
                        case 'con_email':
                            mostrar = tieneEmail;
                            break;
                        case 'con_telefono':
                            mostrar = tieneTelefono;
                            break;
                        case 'sin_contacto':
                            mostrar = !tieneEmail || !tieneTelefono;
                            break;
                    }
                }
                
                cliente.style.display = mostrar ? 'block' : 'none';
            });
        },
        
        clearFilters() {
            this.searchTerm = '';
            this.filterType = 'todos';
            this.filterClientes();
        }
    }
}

// Animaciones y efectos
document.addEventListener('DOMContentLoaded', function() {
    // Intersection Observer para animaciones
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
    
    document.querySelectorAll('.cliente-card').forEach(card => {
        card.style.animationPlayState = 'paused';
        observer.observe(card);
    });
});

// Atajos de teclado
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + N para nuevo cliente
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        window.location.href = '{{ route("clientes.create") }}';
    }
    
    // Escape para cerrar detalles expandidos
    if (e.key === 'Escape') {
        document.querySelectorAll('[id^="details-"]').forEach(detail => {
            if (!detail.classList.contains('hidden')) {
                detail.classList.add('hidden');
                const clienteId = detail.id.replace('details-', '');
                const arrow = document.getElementById(`arrow-${clienteId}`);
                if (arrow) arrow.classList.remove('rotate-180');
            }
        });
    }
    
    // Ctrl + F para buscar
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        const searchInput = document.querySelector('input[x-model="searchTerm"]');
        if (searchInput) searchInput.focus();
    }
});

// Funciones de utilidad adicionales
function copiarAlPortapapeles(texto, mensaje = 'Copiado al portapapeles') {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(texto).then(() => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: mensaje,
                showConfirmButton: false,
                timer: 2000
            });
        });
    }
}

// Auto-save de filtros en localStorage
document.addEventListener('DOMContentLoaded', function() {
    // Restaurar filtros guardados
    const savedSearchTerm = localStorage.getItem('clientes_search_term');
    const savedFilterType = localStorage.getItem('clientes_filter_type');
    
    if (savedSearchTerm) {
        const searchInput = document.querySelector('input[x-model="searchTerm"]');
        if (searchInput) searchInput.value = savedSearchTerm;
    }
    
    if (savedFilterType) {
        const filterSelect = document.querySelector('select[x-model="filterType"]');
        if (filterSelect) filterSelect.value = savedFilterType;
    }
    
    // Guardar filtros en cambios
    document.addEventListener('input', function(e) {
        if (e.target.matches('input[x-model="searchTerm"]')) {
            localStorage.setItem('clientes_search_term', e.target.value);
        }
    });
    
    document.addEventListener('change', function(e) {
        if (e.target.matches('select[x-model="filterType"]')) {
            localStorage.setItem('clientes_filter_type', e.target.value);
        }
    });
});
</script>
@endpush

@endsection