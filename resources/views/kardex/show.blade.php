@extends('layouts.app')

@php
    $puedeAjustar = in_array(auth()->user()->rol, ['admin', 'encargado']);
    $u = $repuesto->unidad_abreviatura;
@endphp

@section('title', 'Kardex · ' . $repuesto->nombre)
@section('breadcrumb')
    <a href="{{ route('kardex.index') }}" class="hover:text-gray-900">Kardex</a>
    <span class="text-gray-300">/</span>
    <span class="text-gray-900">{{ $repuesto->codigo }}</span>
@endsection
@section('page-title', $repuesto->nombre)
@section('page-description', $repuesto->codigo . ($repuesto->categoria ? ' · ' . $repuesto->categoria->nombre : '') . ' · Unidad: ' . $repuesto->unidad_nombre)

@section('page-actions')
    <a href="{{ request()->fullUrlWithQuery(['exportar' => 'csv']) }}" class="btn-secondary">Exportar CSV</a>
    <a href="{{ route('repuestos.edit', $repuesto) }}" class="btn-secondary">Editar producto</a>
    @if($puedeAjustar)
        <button type="button" class="btn-primary" onclick="window.dispatchEvent(new CustomEvent('abrir-ajuste'))">Ajustar inventario</button>
    @endif
@endsection

@section('content')
<div class="space-y-6" x-data="{ ajuste: {{ $errors->any() ? 'true' : 'false' }} }" @abrir-ajuste.window="ajuste = true">

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <p class="stat-label">Stock actual</p>
            <p class="stat-value {{ $repuesto->tieneStockBajo() ? '!text-red-600' : '' }}">{{ $repuesto->stock_formateado }}</p>
            <p class="stat-foot">Mínimo: {{ \App\Support\Catalogos::cantidad($repuesto->minimo_stock) }} {{ $u }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Costo de compra</p>
            <p class="stat-value">{{ $repuesto->precio_compra !== null ? 'S/ ' . number_format($repuesto->precio_compra, 2) : '—' }}</p>
            <p class="stat-foot">Precio de venta: S/ {{ number_format($repuesto->precio_unitario, 2) }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Valor del stock (a costo)</p>
            <p class="stat-value">{{ $repuesto->precio_compra !== null ? 'S/ ' . number_format($repuesto->cantidad * $repuesto->precio_compra, 2) : '—' }}</p>
            <p class="stat-foot">A precio de venta: S/ {{ number_format($repuesto->cantidad * $repuesto->precio_unitario, 2) }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Movimientos (filtro)</p>
            <p class="stat-value">
                <span class="text-emerald-600">+{{ \App\Support\Catalogos::cantidad($totales->entradas ?? 0) }}</span>
                <span class="text-gray-300">/</span>
                <span class="text-red-600">−{{ \App\Support\Catalogos::cantidad($totales->salidas ?? 0) }}</span>
            </p>
            <p class="stat-foot">Entradas / salidas en {{ $u }}</p>
        </div>
    </div>

    <form method="GET" class="card !p-4">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
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
            <div class="flex gap-2">
                <a href="{{ route('kardex.show', $repuesto) }}" class="btn-secondary flex-1">Limpiar</a>
                <button class="btn-primary flex-1">Filtrar</button>
            </div>
        </div>
    </form>

    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Motivo / detalle</th>
                        <th class="num">Entrada</th>
                        <th class="num">Salida</th>
                        <th class="num">Saldo</th>
                        <th class="num hidden md:table-cell">Costo unit.</th>
                        <th class="hidden lg:table-cell">Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimientos as $mov)
                        <tr>
                            <td class="whitespace-nowrap text-gray-500">{{ $mov->fecha?->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="font-medium text-gray-900">{{ $mov->motivo_nombre }}</span>
                                @if($mov->url_referencia)
                                    · <a href="{{ $mov->url_referencia }}" class="link text-xs">{{ $mov->referencia_texto }}</a>
                                @endif
                                @if($mov->descripcion)
                                    <span class="block text-xs text-gray-400">{{ $mov->descripcion }}</span>
                                @endif
                            </td>
                            <td class="text-right font-semibold text-emerald-600">{{ $mov->esEntrada() ? $mov->cantidadTexto($mov->cantidad) : '' }}</td>
                            <td class="text-right font-semibold text-red-600">{{ $mov->esEntrada() ? '' : $mov->cantidadTexto($mov->cantidad) }}</td>
                            <td class="text-right font-bold text-gray-900">{{ $mov->cantidadTexto($mov->stock_nuevo) }}</td>
                            <td class="text-right hidden md:table-cell text-gray-500">{{ $mov->costo_unitario !== null ? 'S/ ' . number_format($mov->costo_unitario, 2) : '—' }}</td>
                            <td class="hidden lg:table-cell text-gray-500">{{ $mov->usuario->nombre ?? 'Sistema' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="empty-state">Este producto aún no tiene movimientos.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($movimientos->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $movimientos->links() }}</div>
        @endif
    </div>

    {{-- Modal de ajuste --}}
    @if($puedeAjustar)
    <div x-show="ajuste" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="ajuste = false">
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="ajuste = false"></div>
        <form method="POST" action="{{ route('kardex.ajustar', $repuesto) }}" class="relative w-full max-w-md card space-y-4"
              x-data="{ tipo: @js(old('tipo_ajuste', 'conteo')) }">
            @csrf
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Ajustar inventario</h3>
                <p class="text-sm text-gray-500">Stock actual: <strong>{{ $repuesto->stock_formateado }}</strong></p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger text-sm">{{ $errors->first() }}</div>
            @endif

            <div class="grid grid-cols-3 gap-2">
                @foreach(['conteo' => 'Conteo físico', 'entrada' => 'Entrada', 'salida' => 'Salida / merma'] as $valor => $texto)
                    <label class="choice !p-3 text-center text-sm" :class="tipo === '{{ $valor }}' && 'choice-active'">
                        <input type="radio" name="tipo_ajuste" value="{{ $valor }}" x-model="tipo" class="sr-only">
                        <span class="font-semibold text-gray-900">{{ $texto }}</span>
                    </label>
                @endforeach
            </div>

            <div>
                <label class="form-label" x-text="tipo === 'conteo' ? 'Cantidad contada ({{ $u }})' : 'Cantidad ({{ $u }})'"></label>
                <input type="number" name="cantidad" value="{{ old('cantidad') }}" class="form-input" required
                       step="{{ $repuesto->permiteDecimales() ? '0.001' : '1' }}" min="0">
                <p class="form-help" x-show="tipo === 'conteo'">El sistema registrará la diferencia con el stock actual.</p>
            </div>

            <div>
                <label class="form-label">Motivo</label>
                <input type="text" name="observacion" value="{{ old('observacion') }}" class="form-input" maxlength="255" required
                       list="motivos-ajuste" placeholder="Ej.: Producto vencido">
                <datalist id="motivos-ajuste">
                    <option value="Conteo físico de inventario">
                    <option value="Producto vencido">
                    <option value="Producto dañado / merma">
                    <option value="Pérdida o robo">
                    <option value="Consumo interno">
                    <option value="Devolución de cliente">
                    <option value="Regalo / muestra">
                </datalist>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" class="btn-secondary" @click="ajuste = false">Cancelar</button>
                <button type="submit" class="btn-primary">Registrar ajuste</button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection
