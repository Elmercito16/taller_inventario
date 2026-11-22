@extends('layouts.app')

@php
    $rangos = ['hoy' => 'Hoy', 'semana' => 'Esta semana', 'mes' => 'Este mes', 'anio' => 'Este año', 'personalizado' => 'Personalizado'];
    $ticketPromedio = $cantidadVentas > 0 ? $totalIngresos / $cantidadVentas : 0;
    $maxProducto = max(1, (float) collect($topProductos)->max('total_generado'));
    $maxCategoria = max(1, (float) collect($topCategorias)->max('total_generado'));
@endphp

@section('title', 'Reportes')
@section('page-title', 'Reportes')
@section('page-description', 'Rendimiento del negocio · ' . $fechaInicio->format('d/m/Y') . ' al ' . $fechaFin->format('d/m/Y'))

@section('content')
<div class="space-y-6">

    <form method="GET" class="card !p-4" x-data="{ rango: @js($rango) }">
        <div class="flex flex-col lg:flex-row lg:items-end gap-3">
            <div>
                <p class="form-label">Periodo</p>
                <div class="tabs">
                    @foreach($rangos as $valor => $texto)
                        <label class="tab cursor-pointer" :class="rango === '{{ $valor }}' && 'tab-active'">
                            <input type="radio" name="rango" value="{{ $valor }}" x-model="rango" class="sr-only" @change="rango !== 'personalizado' && $el.form.submit()">
                            {{ $texto }}
                        </label>
                    @endforeach
                </div>
            </div>
            <template x-if="rango === 'personalizado'">
                <div class="flex flex-col sm:flex-row gap-3 sm:items-end">
                    <div>
                        <label class="form-label">Desde</label>
                        <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio', $fechaInicio->toDateString()) }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Hasta</label>
                        <input type="date" name="fecha_fin" value="{{ request('fecha_fin', $fechaFin->toDateString()) }}" class="form-input" required>
                    </div>
                    <button class="btn-secondary">Aplicar</button>
                </div>
            </template>
        </div>
    </form>

    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="stat-card">
            <p class="stat-label">Ventas</p>
            <p class="stat-value">S/ {{ number_format($totalIngresos, 2) }}</p>
            <p class="stat-foot">{{ $cantidadVentas }} {{ $cantidadVentas === 1 ? 'venta pagada' : 'ventas pagadas' }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Ticket promedio</p>
            <p class="stat-value">S/ {{ number_format($ticketPromedio, 2) }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Ganancia bruta</p>
            <p class="stat-value {{ $gananciaEstimada < 0 ? '!text-red-600' : '' }}">S/ {{ number_format($gananciaEstimada, 2) }}</p>
            <p class="stat-foot">Venta menos costo de compra</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Margen</p>
            <p class="stat-value">{{ number_format($margenPorcentaje, 1) }}%</p>
            <p class="stat-foot">Sobre ventas con costo registrado</p>
        </div>
    </div>

    @if($itemsSinCosto > 0)
        <div class="alert alert-warning">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            <p>{{ $itemsSinCosto }} línea(s) vendidas no tienen costo de compra y no suman a la ganancia. Registra el costo en cada producto o ingresa la mercadería desde Compras.</p>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Productos más vendidos</h2>
                <p class="card-subtitle">Por monto vendido</p>
            </div>
            <div class="space-y-4">
                @forelse($topProductos as $producto)
                    <div>
                        <div class="flex items-baseline justify-between gap-3 text-sm">
                            <p class="truncate text-gray-900">{{ $producto['nombre'] }} <span class="text-xs text-gray-400">{{ $producto['codigo'] }}</span></p>
                            <p class="shrink-0 font-medium text-gray-900 tabular-nums">S/ {{ number_format($producto['total_generado'], 2) }}</p>
                        </div>
                        <div class="mt-1.5 flex items-center gap-3">
                            <div class="h-1.5 flex-1 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full rounded-full bg-primary-500" style="width: {{ number_format($producto['total_generado'] / $maxProducto * 100, 1, '.', '') }}%"></div>
                            </div>
                            <span class="w-20 text-right text-xs text-gray-500 tabular-nums">{{ \App\Support\Catalogos::cantidad($producto['cantidad_vendida']) }} vendidos</span>
                        </div>
                    </div>
                @empty
                    <p class="empty-state !py-6">Sin ventas en el periodo.</p>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Ventas por categoría</h2>
                <p class="card-subtitle">Por monto vendido</p>
            </div>
            <div class="space-y-4">
                @forelse($topCategorias as $categoria)
                    <div>
                        <div class="flex items-baseline justify-between gap-3 text-sm">
                            <p class="truncate text-gray-900">{{ $categoria['nombre'] }}</p>
                            <p class="shrink-0 font-medium text-gray-900 tabular-nums">S/ {{ number_format($categoria['total_generado'], 2) }}</p>
                        </div>
                        <div class="mt-1.5 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full rounded-full bg-sky-500" style="width: {{ number_format($categoria['total_generado'] / $maxCategoria * 100, 1, '.', '') }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="empty-state !py-6">Sin ventas en el periodo.</p>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Ingresos por método de pago</h2>
            </div>
            <div class="space-y-3">
                @foreach($metodosPago as $metodo => $monto)
                    @php $pct = $totalIngresos > 0 ? $monto / $totalIngresos * 100 : 0; @endphp
                    <div class="flex items-center gap-3 text-sm">
                        <p class="w-28 text-gray-600">{{ $metodo }}</p>
                        <div class="h-1.5 flex-1 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full rounded-full bg-primary-500" style="width: {{ number_format($pct, 1, '.', '') }}%"></div>
                        </div>
                        <p class="w-24 text-right font-medium text-gray-900 tabular-nums">S/ {{ number_format($monto, 2) }}</p>
                        <p class="w-10 text-right text-xs text-gray-500 tabular-nums">{{ number_format($pct, 0) }}%</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card-flush">
            <div class="card-head">
                <h2 class="card-title">Mejores clientes</h2>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th class="num">Compras</th>
                        <th class="num">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topClientes as $cliente)
                        <tr>
                            <td class="text-gray-900">{{ $cliente['nombre'] }}</td>
                            <td class="num">{{ $cliente['cantidad_compras'] }}</td>
                            <td class="num font-medium text-gray-900">S/ {{ number_format($cliente['total_gastado'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3"><p class="empty-state !py-6">Sin ventas a clientes registrados en el periodo.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
