@extends('layouts.app')

@section('content')
<div class="px-4 py-6 w-full">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6 text-center sm:text-left">
        Clientes Registrados
    </h1>

    <!-- Botón crear cliente -->
    <div class="mb-4 flex justify-center sm:justify-start">
        <a href="{{ route('clientes.create') }}" 
           class="inline-block bg-blue-600 text-white px-5 py-2 rounded-lg shadow hover:bg-blue-700 transition">
            + Nuevo Cliente
        </a>
    </div>

    <!-- Contenedor responsivo centrado -->
    <div class="overflow-x-auto bg-white shadow-lg rounded-lg mx-auto max-w-full sm:max-w-5xl">
        <div class="space-y-4">
            @forelse ($clientes as $cliente)
                <div class="bg-gray-50 shadow-sm rounded-lg">
                    <!-- Título del cliente (Nombre y flecha) -->
                    <div class="flex justify-between items-center p-4 cursor-pointer" 
                         onclick="toggleDetails('cliente-{{ $cliente->id }}')">
                        <div class="font-semibold text-gray-800">{{ $cliente->nombre }}</div>
                        <div class="text-gray-500">
                            <svg id="arrow-icon-{{ $cliente->id }}" class="w-5 h-5 transform rotate-0 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Información oculta que se desplegará -->
                    <div id="cliente-{{ $cliente->id }}" class="hidden bg-white p-4 text-sm sm:text-base">
                        <p><strong>DNI:</strong> {{ $cliente->dni }}</p>
                        <p><strong>Teléfono:</strong> {{ $cliente->telefono ?? '-' }}</p>
                        <p><strong>Correo:</strong> {{ $cliente->email ?? '-' }}</p>
                        <p><strong>Dirección:</strong> {{ $cliente->direccion ?? '-' }}</p>

                        <div class="flex gap-2 mt-4">
                            <!-- Editar -->
                            <a href="{{ route('clientes.edit', $cliente->id) }}" 
                               class="bg-yellow-400 text-white px-3 py-1 rounded-lg hover:bg-yellow-500 transition text-xs sm:text-sm">
                               Editar
                            </a>

                            <!-- Eliminar -->
                            <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" 
                                  onsubmit="return confirm('¿Seguro que deseas eliminar este cliente?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600 transition text-xs sm:text-sm">
                                        Eliminar
                                </button>
                            </form>

                            <!-- Historial -->
                            <a href="{{ route('clientes.historial', $cliente->id) }}" 
                               class="bg-indigo-500 text-white px-3 py-1 rounded-lg hover:bg-indigo-600 transition text-xs sm:text-sm">
                               Historial
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-4 text-center text-gray-500">No hay clientes registrados</div>
            @endforelse
        </div>
    </div>
</div>

<script>
    function toggleDetails(clienteId) {
        const details = document.getElementById(clienteId);
        const arrow = document.getElementById('arrow-icon-' + clienteId.split('-')[1]);

        if (details.classList.contains('hidden')) {
            details.classList.remove('hidden');
            arrow.classList.add('rotate-180');
        } else {
            details.classList.add('hidden');
            arrow.classList.remove('rotate-180');
        }
    }
</script>

@endsection
