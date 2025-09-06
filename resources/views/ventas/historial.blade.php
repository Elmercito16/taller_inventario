@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            🧾 Historial de compras — {{ $cliente->nombre }}
        </h1>
        <a href="{{ route('clientes.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition">
            ⬅ Volver
        </a>
    </div>

    {{-- Resumen --}}
    <div class="grid md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-sm text-gray-500">Total de ventas</p>
            <p class="text-2xl font-bold text-gray-800">{{ $ventas->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-sm text-gray-500">Monto acumulado</p>
            <p class="text-2xl font-bold text-green-600">
                S/ {{ number_format($ventas->sum('total'), 2) }}
            </p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-sm text-gray-500">Última compra</p>
            <p class="text-lg font-semibold text-gray-800">
                {{ optional($ventas->first())->fecha ? \Carbon\Carbon::parse($ventas->first()->fecha)->format('d/m/Y H:i') : '—' }}
            </p>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="overflow-x-auto bg-white shadow-lg rounded-2xl">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-50 text-gray-700 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">#</th>
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
                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-green-600">
                            S/ {{ number_format($venta->total, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs font-bold rounded-full 
                                {{ $venta->estado === 'pagado' ? 'bg-green-100 text-green-700' : ($venta->estado === 'pendiente' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($venta->estado) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('ventas.show', $venta->id) }}"
                               class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-xs shadow">
                                🔍 Ver detalle
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-6 text-gray-500">
                            Este cliente aún no tiene ventas registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
