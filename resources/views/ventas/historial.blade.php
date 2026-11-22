@extends('layouts.app')

@php
    $pagadas = $cliente->ventas()->where('estado', 'pagado');
    $totalComprado = (float) (clone $pagadas)->sum('total');
    $numCompras = (clone $pagadas)->count();
    $ultima = $cliente->ventas()->orderByDesc('fecha')->first();
    $filtro = request('filter', 'all');
    $periodos = [
        'all' => 'Todo el historial', 'today' => 'Hoy', 'yesterday' => 'Ayer', 'last_7_days' => 'Últimos 7 días',
        'last_30_days' => 'Últimos 30 días', 'this_month' => 'Este mes', 'last_month' => 'Mes pasado',
        'this_year' => 'Este año', 'last_year' => 'Año pasado', 'custom' => 'Rango personalizado',
    ];
    $estados = ['pagado' => ['Pagada', 'badge-success'], 'pendiente' => ['Pendiente', 'badge-warning'], 'anulado' => ['Anulada', 'badge-danger']];
@endphp

@section('title', 'Historial de ' . $cliente->nombre)
@section('breadcrumb')
    <a href="{{ route('clientes.index') }}" class="hover:text-gray-900">Clientes</a>
    <span class="text-gray-300">/</span>
    <span class="text-gray-900">Historial</span>
@endsection
@section('page-title', $cliente->nombre)
@section('page-description', $cliente->documento_formateado . ($cliente->telefono ? ' · ' . $cliente->telefono : ''))

@section('page-actions')
    <a href="{{ route('clientes.edit', $cliente) }}" class="btn-secondary">Editar cliente</a>
    <a href="{{ route('ventas.create') }}" class="btn-primary">Nueva venta</a>
@endsection

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="stat-card">
            <p class="stat-label">Total comprado</p>
            <p class="stat-value">S/ {{ number_format($totalComprado, 2) }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Compras</p>
            <p class="stat-value">{{ $numCompras }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Ticket promedio</p>
            <p class="stat-value">S/ {{ number_format($numCompras ? $totalComprado / $numCompras : 0, 2) }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Última compra</p>
            <p class="stat-value !text-lg">{{ $ultima?->fecha?->format('d/m/Y') ?? '—' }}</p>
            <p class="stat-foot">{{ $ultima?->fecha?->diffForHumans() }}</p>
        </div>
    </div>

    <div class="card-flush">
        <form method="GET" class="toolbar" x-data="{ filtro: @js($filtro) }">
            <div class="lg:w-56">
                <label class="form-label">Periodo</label>
                <select name="filter" x-model="filtro" class="form-input" @change="filtro !== 'custom' && $el.form.submit()">
                    @foreach($periodos as $valor => $texto)
                        <option value="{{ $valor }}">{{ $texto }}</option>
                    @endforeach
                </select>
            </div>
            <template x-if="filtro === 'custom'">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div>
                        <label class="form-label">Desde</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Hasta</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-input" required>
                    </div>
                </div>
            </template>
            <div x-show="filtro === 'custom'">
                <button class="btn-secondary">Aplicar</button>
            </div>
            <p class="text-sm text-gray-500 lg:ml-auto lg:self-center">{{ $ventas->total() }} {{ $ventas->total() === 1 ? 'venta' : 'ventas' }} en el periodo</p>
        </form>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Venta</th>
                        <th>Productos</th>
                        <th class="num">Total</th>
                        <th class="center">Estado</th>
                        <th class="w-px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $venta)
                        <tr>
                            <td class="whitespace-nowrap">
                                <a href="{{ route('ventas.show', $venta) }}" class="font-medium text-gray-900 hover:text-primary-700">#{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</a>
                                <p class="text-xs text-gray-500">{{ $venta->fecha?->format('d/m/Y H:i') }}</p>
                            </td>
                            <td class="max-w-md">
                                <p class="text-gray-700 truncate">
                                    {{ $venta->detalles->map(fn ($d) => ($d->repuesto->nombre ?? 'Producto eliminado') . ' ×' . $d->cantidad_formateada)->implode(', ') }}
                                </p>
                            </td>
                            <td class="num font-medium text-gray-900 whitespace-nowrap">S/ {{ number_format($venta->total, 2) }}</td>
                            <td class="center"><span class="badge {{ $estados[$venta->estado][1] ?? 'badge-gray' }}">{{ $estados[$venta->estado][0] ?? ucfirst($venta->estado) }}</span></td>
                            <td><a href="{{ route('ventas.show', $venta) }}" class="btn-icon" title="Ver detalle"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <p class="font-medium text-gray-900">Sin compras en este periodo</p>
                                    <p class="mt-1">Prueba con otro rango de fechas.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ventas->hasPages())
            <div class="card-foot">{{ $ventas->links() }}</div>
        @endif
    </div>
</div>
@endsection
