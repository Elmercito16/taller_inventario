@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-8 px-4">

    <!-- Título -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">📋 Listado de Ventas</h1>
        <a href="{{ route('ventas.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg shadow-md flex items-center gap-2 transition">
            ➕ Nueva Venta
        </a>
    </div>

    <!-- Tabla -->
    <div class="overflow-x-auto bg-white shadow-lg rounded-2xl">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">#</th>
                    <th class="px-6 py-3 text-left">Cliente</th>
                    <th class="px-6 py-3 text-left">Fecha</th>
                    <th class="px-6 py-3 text-left">Total</th>
                    <th class="px-6 py-3 text-left">Estado</th>
                    <th class="px-6 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($ventas as $venta)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-semibold text-gray-800">{{ $venta->id }}</td>
                        <td class="px-6 py-4">{{ $venta->cliente->nombre ?? 'Sin Cliente' }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 font-semibold text-green-600">S/ {{ number_format($venta->total, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs font-bold rounded-full 
                                {{ $venta->estado == 'pagado' ? 'bg-green-100 text-green-700' : ($venta->estado == 'pendiente' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($venta->estado) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 flex justify-center gap-3">
                            <!-- Botón Ver -->
                            <a href="{{ route('ventas.show', $venta) }}" 
                               class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-xs shadow">
                               🔍 Ver
                            </a>

                            <!-- Botón Eliminar -->
                            <form action="{{ route('ventas.destroy', $venta) }}" method="POST" onsubmit="return confirm('¿Eliminar esta venta?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-xs shadow">
                                    🗑 Eliminar
                                </button>
                            </form>

                            <!-- Botón Anular (solo si no está anulada) -->
                            @if ($venta->estado !== 'anulado')
                                <form action="{{ route('ventas.anular', $venta->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas anular esta venta?')">
                                    @csrf
                                    <button type="submit" 
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg text-xs shadow">
                                        ❌ Anular
                                    </button>
                                </form>
                            @else
                                <span class="px-3 py-1 bg-gray-400 text-white rounded-lg text-xs">Anulado</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-6 text-gray-500">
                            🚫 No hay ventas registradas aún.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
