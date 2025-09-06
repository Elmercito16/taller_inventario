@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Clientes Registrados</h1>

    <!-- Botón crear cliente -->
    <div class="mb-4">
        <a href="{{ route('clientes.create') }}" 
           class="inline-block bg-blue-600 text-white px-5 py-2 rounded-lg shadow hover:bg-blue-700 transition">
            + Nuevo Cliente
        </a>
    </div>

    <!-- Tabla de clientes -->
    <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
        <table class="min-w-full text-left divide-y divide-gray-200">
            <thead class="bg-gray-50 text-gray-700 uppercase text-sm">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">DNI</th>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Teléfono</th>
                    <th class="px-4 py-3">Dirección</th>
                    <th class="px-4 py-3">Correo</th>
                    <th class="px-4 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($clientes as $cliente)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $cliente->id }}</td>
                        <td class="px-4 py-3 font-mono">{{ $cliente->dni }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $cliente->nombre }}</td>
                        <td class="px-4 py-3">{{ $cliente->telefono }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $cliente->direccion ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $cliente->email ?? '-' }}</td>
                        <td class="px-4 py-3 flex space-x-2">
                            <!-- Editar -->
                            <a href="{{ route('clientes.edit', $cliente->id) }}" 
                               class="bg-yellow-400 text-white px-3 py-1 rounded-lg hover:bg-yellow-500 transition">
                               Editar
                            </a>

                            <!-- Eliminar -->
                            <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este cliente?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600 transition">
                                        Eliminar
                                </button>
                            </form>

                            <!-- Historial -->
                            <a href="{{ route('clientes.historial', $cliente->id) }}" 
                               class="bg-indigo-500 text-white px-3 py-1 rounded-lg hover:bg-indigo-600 transition">
                               Historial
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-4 text-center text-gray-500">No hay clientes registrados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
