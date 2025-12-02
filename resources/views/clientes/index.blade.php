@extends('layouts.app')

@section('title', 'Clientes')

@push('styles')
<style>
    .card-fade { animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="{ search: '', filter: 'todos' }">

    <!-- Stats Dashboard -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card-fade bg-white p-5 rounded-2xl shadow-sm border border-gray-200 hover:shadow-lg transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="p-3 bg-gradient-to-br from-[#218786] to-[#1a6d6c] rounded-xl shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-semibold text-gray-600">Total Clientes</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $clientes->count() }}</p>
        </div>
        
        <div class="card-fade bg-white p-5 rounded-2xl shadow-sm border border-gray-200 hover:shadow-lg transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-semibold text-gray-600">Con Email</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">
                {{ $clientes->filter(fn($c) => !empty($c->email))->count() }}
            </p>
        </div>
        
        <div class="card-fade bg-white p-5 rounded-2xl shadow-sm border border-gray-200 hover:shadow-lg transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-semibold text-gray-600">Con Teléfono</p>
            <p class="text-2xl font-bold text-green-600 mt-1">
                {{ $clientes->filter(fn($c) => !empty($c->telefono))->count() }}
            </p>
        </div>
        
        <div class="card-fade bg-white p-5 rounded-2xl shadow-sm border border-gray-200 hover:shadow-lg transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-semibold text-gray-600">Hoy</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">
                {{ $clientes->filter(fn($c) => $c->created_at->isToday())->count() }}
            </p>
        </div>
    </div>

    <!-- Barra de Búsqueda y Filtros -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            
            <!-- Búsqueda y Filtros -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1">
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input x-model="search" 
                           type="text" 
                           placeholder="Buscar por nombre o DNI..."
                           class="w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-transparent">
                </div>
                
                <select x-model="filter" 
                        class="px-4 py-3 text-sm font-semibold border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-transparent">
                    <option value="todos">Todos</option>
                    <option value="con_email">Con Email</option>
                    <option value="con_telefono">Con Teléfono</option>
                    <option value="sin_contacto">Sin Contacto</option>
                </select>
                
                <button @click="search = ''; filter = 'todos'" 
                        class="px-4 py-3 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all">
                    Limpiar
                </button>
            </div>

            <!-- Botones de Acción -->
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('clientes.create') }}" 
                   class="inline-flex items-center justify-center px-5 py-3 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-semibold rounded-xl hover:shadow-xl transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nuevo Cliente
                </a>
                
                <button onclick="exportarClientes()" 
                        class="inline-flex items-center justify-center px-5 py-3 bg-white border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Exportar
                </button>
            </div>
        </div>
    </div>

    <!-- Lista de Clientes -->
    <div class="space-y-3">
        @forelse ($clientes as $index => $cliente)
            <div class="card-fade bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all"
                 style="animation-delay: {{ $index * 0.05 }}s"
                 x-show="(search === '' || 
                          '{{ strtolower($cliente->nombre) }}'.includes(search.toLowerCase()) || 
                          '{{ $cliente->dni }}'.includes(search)) &&
                         (filter === 'todos' || 
                          (filter === 'con_email' && {{ $cliente->email ? 'true' : 'false' }}) ||
                          (filter === 'con_telefono' && {{ $cliente->telefono ? 'true' : 'false' }}) ||
                          (filter === 'sin_contacto' && {{ !$cliente->email || !$cliente->telefono ? 'true' : 'false' }}))"
                 x-data="{ open: false }">
                
                <!-- Header Card -->
                <div class="p-5 cursor-pointer hover:bg-gray-50 transition-colors" @click="open = !open">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4 flex-1 min-w-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-[#218786] to-[#1a6d6c] rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-gray-900 truncate">{{ $cliente->nombre }}</h3>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-gray-600 mt-1">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 011-1h2a2 2 0 011 1v2m-4 0a2 2 0 01-1 1h-2a2 2 0 01-1-1"/>
                                        </svg>
                                        {{ $cliente->dni }}
                                    </span>
                                    <span class="hidden sm:inline">•</span>
                                    <span class="hidden sm:inline">{{ $cliente->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2 flex-shrink-0">
                            @if($cliente->email)
                            <span class="hidden md:inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-700">
                                Email
                            </span>
                            @endif
                            
                            @if($cliente->telefono)
                            <span class="hidden md:inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-green-100 text-green-700">
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
                                        <p class="text-gray-500 text-xs mb-1">Email</p>
                                        <p class="font-semibold text-gray-900">{{ $cliente->email ?: 'No especificado' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-xs mb-1">Teléfono</p>
                                        <p class="font-semibold text-gray-900">{{ $cliente->telefono ?: 'No especificado' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-xs mb-1">Dirección</p>
                                        <p class="font-semibold text-gray-900">{{ $cliente->direccion ?: 'No especificada' }}</p>
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
                                        <p class="text-gray-500 text-xs mb-1">Registrado</p>
                                        <p class="font-semibold text-gray-900">{{ $cliente->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-xs mb-1">Última actualización</p>
                                        <p class="font-semibold text-gray-900">{{ $cliente->updated_at->diffForHumans() }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-xs mb-1">Ventas registradas</p>
                                        <p class="font-semibold text-[#218786]">{{ $cliente->ventas_count ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('clientes.edit', $cliente->id) }}" 
                               class="inline-flex items-center justify-center px-4 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-105 active:scale-95">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Editar
                            </a>

                            @if(Route::has('clientes.historial'))
                            <a href="{{ route('clientes.historial', $cliente->id) }}" 
                               class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white font-semibold rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-105 active:scale-95">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Historial
                            </a>
                            @endif

                            <button onclick="contactarCliente('{{ $cliente->telefono }}', '{{ $cliente->email }}')" 
                                    class="inline-flex items-center justify-center px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-105 active:scale-95">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                Contactar
                            </button>

                            <button onclick="confirmarEliminacion('{{ route('clientes.destroy', $cliente->id) }}', '{{ addslashes($cliente->nombre) }}')" 
                                    class="inline-flex items-center justify-center px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl transition-all shadow-md hover:shadow-lg hover:scale-105 active:scale-95">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <!-- Estado Vacío -->
            <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-gray-200">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Sin clientes registrados</h3>
                <p class="text-gray-600 mb-6">Comienza agregando tu primer cliente</p>
                <a href="{{ route('clientes.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-semibold rounded-xl hover:shadow-xl transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Registrar Primer Cliente
                </a>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
function contactarCliente(telefono, email) {
    const opciones = [];
    
    if (telefono) {
        opciones.push({
            text: `📞 Llamar a ${telefono}`,
            action: () => window.open(`tel:${telefono}`)
        });
        opciones.push({
            text: `💬 WhatsApp: ${telefono}`,
            action: () => window.open(`https://wa.me/51${telefono.replace(/\D/g, '')}`)
        });
    }
    
    if (email) {
        opciones.push({
            text: `✉️ Email: ${email}`,
            action: () => window.open(`mailto:${email}`)
        });
    }
    
    if (opciones.length === 0) {
        Swal.fire({
            icon: 'info',
            title: 'Sin información',
            text: 'Este cliente no tiene datos de contacto',
            confirmButtonColor: '#218786'
        });
        return;
    }
    
    if (opciones.length === 1) {
        opciones[0].action();
        return;
    }
    
    const buttonsHtml = opciones.map((op, i) => 
        `<button onclick="window.contactActions[${i}]()" class="w-full mb-2 px-4 py-3 bg-[#218786] hover:bg-[#1a6d6c] text-white font-semibold rounded-xl transition-all">${op.text}</button>`
    ).join('');
    
    window.contactActions = opciones.map(op => op.action);
    
    Swal.fire({
        title: 'Contactar Cliente',
        html: buttonsHtml,
        showConfirmButton: false,
        showCloseButton: true
    });
}

function confirmarEliminacion(url, nombre) {
    Swal.fire({
        title: '¿Eliminar cliente?',
        html: `<p class="mb-4">Cliente: <strong>"${nombre}"</strong></p><div class="bg-red-50 p-3 rounded-lg"><p class="text-sm text-red-700">⚠️ Esta acción no se puede deshacer</p></div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.innerHTML = `@csrf @method('DELETE')`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function exportarClientes() {
    Swal.fire({
        title: 'Exportar Clientes',
        text: 'Funcionalidad próximamente',
        icon: 'info',
        confirmButtonColor: '#218786'
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
});
</script>
@endpush
@endsection