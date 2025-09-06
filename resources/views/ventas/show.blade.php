@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-8 px-4">
    <!-- Título -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">🧾 Detalle de Venta #{{ $venta->id }}</h1>
        <a href="{{ route('ventas.index') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow flex items-center gap-2">
           ⬅ Volver
        </a>
    </div>

    <!-- Datos de la venta -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Info cliente -->
        <div class="bg-white shadow-lg rounded-2xl p-6 border-l-4 border-blue-500">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">👤 Información del Cliente</h2>
            <p><span class="font-bold text-gray-600">Nombre:</span> {{ $venta->cliente->nombre ?? 'Sin cliente' }}</p>
            <p><span class="font-bold text-gray-600">Fecha:</span> {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</p>
        </div>

        <!-- Info venta -->
        <div class="bg-white shadow-lg rounded-2xl p-6 border-l-4 border-green-500">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">💰 Información de la Venta</h2>
            <p><span class="font-bold text-gray-600">Total:</span> 
                <span class="text-green-600 font-bold">S/ {{ number_format($venta->total, 2) }}</span>
            </p>
            <p><span class="font-bold text-gray-600">Estado:</span>
                <span class="px-3 py-1 text-xs font-bold rounded-full 
                    {{ $venta->estado == 'pagado' ? 'bg-green-100 text-green-700' : ($venta->estado == 'pendiente' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                    {{ ucfirst($venta->estado) }}
                </span>
            </p>
        </div>
    </div>

    <!-- Detalles de productos -->
    <div class="bg-white shadow-lg rounded-2xl p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">📦 Productos Vendidos</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-gray-700 border">
                <thead class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">Repuesto</th>
                        <th class="px-4 py-3 text-left">Cantidad</th>
                        <th class="px-4 py-3 text-left">Precio Unitario</th>
                        <th class="px-4 py-3 text-left">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($venta->detalles as $detalle)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2">{{ $detalle->repuesto->nombre }}</td>
                            <td class="px-4 py-2">{{ $detalle->cantidad }}</td>
                            <td class="px-4 py-2">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                            <td class="px-4 py-2 font-semibold text-green-600">S/ {{ number_format($detalle->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
