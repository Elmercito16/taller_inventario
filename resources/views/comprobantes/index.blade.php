@extends('layouts.app')

@section('title', 'Comprobantes Electrónicos')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="container mx-auto px-4">
        
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-800 flex items-center gap-3">
                        <span class="text-5xl">📄</span>
                        Comprobantes Electrónicos
                    </h1>
                    <p class="text-gray-600 mt-2 ml-16">Gestiona tus facturas y boletas electrónicas</p>
                </div>
                <a href="{{ route('ventas.index') }}" 
                   class="px-6 py-3 bg-gray-600 text-white font-bold rounded-xl hover:bg-gray-700 transition-all shadow-lg">
                    ← Volver a Ventas
                </a>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <form method="GET" action="{{ route('comprobantes.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                
                {{-- Tipo --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tipo</label>
                    <select name="tipo" class="w-full px-4 py-2 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786]">
                        <option value="">Todos</option>
                        <option value="01" {{ request('tipo') == '01' ? 'selected' : '' }}>Facturas</option>
                        <option value="03" {{ request('tipo') == '03' ? 'selected' : '' }}>Boletas</option>
                    </select>
                </div>

                {{-- Estado --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Estado</label>
                    <select name="estado" class="w-full px-4 py-2 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786]">
                        <option value="">Todos</option>
                        <option value="aceptado" {{ request('estado') == 'aceptado' ? 'selected' : '' }}>Aceptado</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="rechazado" {{ request('estado') == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                        <option value="observado" {{ request('estado') == 'observado' ? 'selected' : '' }}>Observado</option>
                        <option value="baja_pendiente" {{ request('estado') == 'baja_pendiente' ? 'selected' : '' }}>Baja en proceso</option>
                        <option value="anulado" {{ request('estado') == 'anulado' ? 'selected' : '' }}>Anulado</option>
                    </select>
                </div>

                {{-- Fecha Inicio --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Desde</label>
                    <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}"
                           class="w-full px-4 py-2 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786]">
                </div>

                {{-- Fecha Fin --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Hasta</label>
                    <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}"
                           class="w-full px-4 py-2 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786]">
                </div>

                {{-- Buscar --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Buscar</label>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Número, cliente..."
                           class="w-full px-4 py-2 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786]">
                </div>

                {{-- Botones --}}
                <div class="md:col-span-5 flex gap-2">
                    <button type="submit" class="px-6 py-2 bg-[#218786] text-white font-bold rounded-xl hover:bg-[#1a6d6c] transition-all">
                        🔍 Filtrar
                    </button>
                    <a href="{{ route('comprobantes.index') }}" class="px-6 py-2 bg-gray-400 text-white font-bold rounded-xl hover:bg-gray-500 transition-all">
                        🔄 Limpiar
                    </a>
                </div>
            </form>
        </div>

        {{-- Tabla de Comprobantes --}}
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            
            @if($comprobantes->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-bold">Número</th>
                                <th class="px-6 py-4 text-left text-sm font-bold">Tipo</th>
                                <th class="px-6 py-4 text-left text-sm font-bold">Cliente</th>
                                <th class="px-6 py-4 text-left text-sm font-bold">Fecha</th>
                                <th class="px-6 py-4 text-right text-sm font-bold">Total</th>
                                <th class="px-6 py-4 text-center text-sm font-bold">Estado</th>
                                <th class="px-6 py-4 text-center text-sm font-bold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($comprobantes as $comprobante)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-mono font-bold text-[#218786]">
                                        {{ $comprobante->numero_completo }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($comprobante->tipo_comprobante == '01')
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold">
                                            📄 Factura
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-bold">
                                            🧾 Boleta
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <div class="font-bold text-gray-900">{{ $comprobante->cliente_nombre }}</div>
                                        <div class="text-gray-500">{{ $comprobante->cliente_num_doc }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $comprobante->fecha_emision->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-bold text-lg text-gray-900">
                                        S/ {{ number_format($comprobante->total, 2) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $badge = [
                                            'aceptado' => ['bg-green-100 text-green-800', '✅ Aceptado'],
                                            'observado' => ['bg-emerald-100 text-emerald-800', '✅ Observado'],
                                            'pendiente' => ['bg-yellow-100 text-yellow-800', '⏳ Pendiente'],
                                            'rechazado' => ['bg-red-100 text-red-800', '❌ Rechazado'],
                                            'baja_pendiente' => ['bg-amber-100 text-amber-800', '⏳ Baja en proceso'],
                                            'anulado' => ['bg-gray-100 text-gray-800', '🚫 Anulado'],
                                        ][$comprobante->anulado ? 'anulado' : $comprobante->estado_sunat] ?? ['bg-gray-100 text-gray-700', ucfirst($comprobante->estado_sunat)];
                                    @endphp
                                    <span class="px-3 py-1 {{ $badge[0] }} rounded-full text-xs font-bold whitespace-nowrap">{{ $badge[1] }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('comprobantes.show', $comprobante) }}" 
                                           class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all text-xs font-bold"
                                           title="Ver detalle">
                                            👁️ Ver
                                        </a>
                                        
                                        @if($comprobante->xml_path)
                                        <a href="{{ route('comprobantes.descargar-xml', $comprobante) }}" 
                                           class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all text-xs font-bold"
                                           title="Descargar XML">
                                            📥 XML
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="px-6 py-4 bg-gray-50 border-t">
                    {{ $comprobantes->appends(request()->query())->links() }}
                </div>

            @else
                <div class="text-center py-16">
                    <div class="text-6xl mb-4">📭</div>
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">No hay comprobantes</h3>
                    <p class="text-gray-500">Aún no se han emitido comprobantes electrónicos</p>
                </div>
            @endif

        </div>

    </div>
</div>
@endsection
