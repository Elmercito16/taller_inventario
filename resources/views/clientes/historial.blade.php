@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">
        🧾 Historial de Compras de {{ $cliente->nombre }}
    </h1>

    <!-- Información del cliente -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <p><strong>DNI:</strong> {{ $cliente->dni }}</p>
        <p><strong>Teléfono:</strong> {{ $cliente->telefono ?? '-' }}</p>
        <p><strong>Correo:</strong> {{ $cliente->email ?? '-' }}</p>
        <p><strong>Dirección:</strong> {{ $cliente->direccion ?? '-' }}</p>
    </div>

    <!-- Listado de ventas -->
    <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
        <table class="min-w-full text-left divide-y divide-gray-200">
            <thead class="bg-gray-50 text-gray-700 uppercase text-sm">
                <tr>
                    <th class="px-4 py-3"># Venta</th>
                    <th class="px-4 py-3">Fecha</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($ventas as $venta)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono">#{{ $venta->id }}</td>
                        <td class="px-4 py-3">{{ $venta->fecha->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            @if ($venta->estado === 'pagado')
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">Pagado</span>
                            @else
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">Anulado</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-800">
                            S/ {{ number_format($venta->total, 2) }}
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('ventas.show', $venta->id) }}" 
                               class="bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700 transition">
                               Ver Detalle
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                            Este cliente aún no tiene compras registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Botón volver -->
    <div class="mt-6">
        <a href="{{ route('clientes.index') }}" 
           class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
           ← Volver a Clientes
        </a>
    </div>
</div>
@endsection
