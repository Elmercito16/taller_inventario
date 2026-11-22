@extends('layouts.app')

@section('title', 'Kardex')
@section('page-title', 'Kardex de inventario')
@section('page-description', 'Todos los movimientos de stock: compras, ventas, ajustes y anulaciones')

@section('content')
<div class="space-y-6">

    <form method="GET" class="card !p-4">
        <div class="grid grid-cols-2 lg:grid-cols-6 gap-3 items-end">
            <div class="col-span-2">
                <label class="form-label">Producto</label>
                <select name="repuesto_id" class="form-input">
                    <option value="">Todos los productos</option>
                    @foreach($productos as $producto)
                        <option value="{{ $producto->id }}" @selected(request('repuesto_id') == $producto->id)>{{ $producto->codigo }} · {{ $producto->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-input">
                    <option value="">Entradas y salidas</option>
                    <option value="entrada" @selected(request('tipo') === 'entrada')>Solo entradas</option>
                    <option value="salida" @selected(request('tipo') === 'salida')>Solo salidas</option>
                </select>
            </div>
            <div>
                <label class="form-label">Motivo</label>
                <select name="motivo" class="form-input">
                    <option value="">Todos</option>
                    @foreach($motivos as $valor => $nombre)
                        <option value="{{ $valor }}" @selected(request('motivo') === $valor)>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Desde</label>
                <input type="date" name="desde" value="{{ request('desde') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Hasta</label>
                <input type="date" name="hasta" value="{{ request('hasta') }}" class="form-input">
            </div>
        </div>
        <div class="flex flex-wrap justify-between items-center gap-3 mt-4">
            <p class="text-sm text-gray-500">
                <span class="text-emerald-600 font-semibold">{{ (int) ($resumen->entradas ?? 0) }} entradas</span> ·
                <span class="text-red-600 font-semibold">{{ (int) ($resumen->salidas ?? 0) }} salidas</span>
            </p>
            <div class="flex gap-2">
                <a href="{{ route('kardex.index') }}" class="btn-secondary">Limpiar</a>
                <a href="{{ request()->fullUrlWithQuery(['exportar' => 'csv']) }}" class="btn-secondary">Exportar Excel (CSV)</a>
                <button class="btn-primary">Filtrar</button>
            </div>
        </div>
    </form>

    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Motivo</th>
                        <th class="num">Cantidad</th>
                        <th class="num hidden md:table-cell">Saldo</th>
                        <th class="hidden lg:table-cell">Referencia</th>
                        <th class="hidden xl:table-cell">Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimientos as $mov)
                        <tr>
                            <td class="whitespace-nowrap text-gray-500">{{ $mov->fecha?->format('d/m/Y H:i') }}</td>
                            <td class="max-w-[220px]">
                                @if($mov->repuesto)
                                    <a href="{{ route('kardex.show', $mov->repuesto) }}" class="font-medium text-gray-900 hover:text-primary-700 truncate block">{{ $mov->repuesto->nombre }}</a>
                                    <span class="text-xs text-gray-500">{{ $mov->repuesto->codigo }}</span>
                                @else
                                    <span class="text-gray-400">Producto eliminado</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-sm">{{ $mov->motivo_nombre }}</span>
                                @if($mov->descripcion)
                                    <span class="block text-xs text-gray-400 truncate max-w-[220px]" title="{{ $mov->descripcion }}">{{ $mov->descripcion }}</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap font-semibold {{ $mov->esEntrada() ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $mov->esEntrada() ? '+' : '−' }}{{ $mov->cantidadTexto($mov->cantidad) }} {{ $mov->repuesto->unidad_abreviatura ?? '' }}
                            </td>
                            <td class="text-right hidden md:table-cell whitespace-nowrap text-gray-500">
                                {{ $mov->cantidadTexto($mov->stock_anterior) }} → <strong class="text-gray-900">{{ $mov->cantidadTexto($mov->stock_nuevo) }}</strong>
                            </td>
                            <td class="hidden lg:table-cell">
                                @if($mov->url_referencia)
                                    <a href="{{ $mov->url_referencia }}" class="link text-xs">{{ $mov->referencia_texto }}</a>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="hidden xl:table-cell text-gray-500">{{ $mov->usuario->nombre ?? 'Sistema' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="empty-state">No hay movimientos con estos filtros.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($movimientos->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $movimientos->links() }}</div>
        @endif
    </div>
</div>
@endsection
