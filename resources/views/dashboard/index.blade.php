@extends('layouts.app')

@section('title', 'Inicio')
@section('page-title', 'Hola, ' . explode(' ', auth()->user()->nombre)[0])
@section('page-description', ucfirst(now()->locale('es')->isoFormat('dddd D [de] MMMM, YYYY')) . ' · Resumen de tu negocio')

@section('content')
<div class="space-y-6">

    {{-- Accesos rápidos --}}
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('ventas.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nueva venta
        </a>
        <a href="{{ route('repuestos.create') }}" class="btn-secondary">Agregar producto</a>
        @if(in_array(auth()->user()->rol, ['admin', 'encargado']))
            <a href="{{ route('compras.create') }}" class="btn-secondary">Registrar compra</a>
        @endif
        <a href="{{ route('clientes.create') }}" class="btn-secondary">Nuevo cliente</a>
        @if($comprobantesConProblemas > 0)
            <a href="{{ route('comprobantes.index') }}" class="btn-secondary !text-amber-700 !border-amber-200 !bg-amber-50 hover:!bg-amber-100">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                {{ $comprobantesConProblemas }} comprobante(s) por revisar
            </a>
        @endif
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="stat-icon bg-primary-100 text-primary-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="stat-label">Ventas de hoy</p>
            <p class="stat-value">S/ {{ number_format($ventasHoy, 2) }}</p>
            <p class="stat-foot">{{ $cantidadHoy }} {{ $cantidadHoy === 1 ? 'venta' : 'ventas' }}</p>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-sky-100 text-sky-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <p class="stat-label">Ventas del mes</p>
            <p class="stat-value">S/ {{ number_format($ventasMes, 2) }}</p>
            <p class="stat-foot">
                @if($variacionMes !== null)
                    <span class="{{ $variacionMes >= 0 ? 'text-emerald-600' : 'text-red-600' }} font-semibold">
                        {{ $variacionMes >= 0 ? '▲' : '▼' }} {{ number_format(abs($variacionMes), 1) }}%
                    </span>
                    vs. mes anterior
                @else
                    {{ $cantidadMes }} ventas este mes
                @endif
            </p>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-violet-100 text-violet-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <p class="stat-label">Ticket promedio</p>
            <p class="stat-value">S/ {{ number_format($ticketPromedio, 2) }}</p>
            <p class="stat-foot">{{ $cantidadMes }} ventas este mes</p>
        </div>

        <div class="stat-card">
            <div class="stat-icon {{ ($inventario->stock_bajo + $inventario->agotados) > 0 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <p class="stat-label">Inventario</p>
            <p class="stat-value">S/ {{ number_format($inventario->valor ?? 0, 2) }}</p>
            <p class="stat-foot">
                {{ (int) $inventario->total_items }} productos ·
                <span class="{{ $inventario->agotados > 0 ? 'text-red-600 font-semibold' : '' }}">{{ (int) $inventario->agotados }} agotados</span> ·
                <span class="{{ $inventario->stock_bajo > 0 ? 'text-amber-600 font-semibold' : '' }}">{{ (int) $inventario->stock_bajo }} stock bajo</span>
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Ventas últimos 7 días --}}
        <div class="card xl:col-span-2">
            <div class="card-header flex items-center justify-between">
                <div>
                    <h2 class="card-title">Ventas de los últimos 7 días</h2>
                    <p class="card-subtitle">Total: S/ {{ number_format($ultimos7Dias->sum('total'), 2) }}</p>
                </div>
                <a href="{{ route('reportes.index') }}" class="link">Ver reportes →</a>
            </div>

            <div class="flex items-end gap-2 sm:gap-4 h-56 pt-6">
                @foreach($ultimos7Dias as $dia)
                    @php $altura = $dia['total'] > 0 ? max(4, ($dia['total'] / $maxDia) * 100) : 0; @endphp
                    <div class="flex-1 h-full flex flex-col items-center justify-end gap-2 group">
                        <span class="text-[11px] font-medium text-gray-500 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                            S/ {{ number_format($dia['total'], 0) }}
                        </span>
                        <div class="w-full max-w-[48px] rounded-t-lg bg-gray-100 relative overflow-hidden" style="height: 100%">
                            <div class="absolute bottom-0 inset-x-0 rounded-t-lg bg-gradient-to-t from-primary-600 to-primary-400 transition-all duration-500 {{ $loop->last ? '' : 'opacity-80' }}"
                                 style="height: {{ number_format($altura, 2, '.', '') }}%"></div>
                        </div>
                        <div class="text-center leading-tight">
                            <p class="text-xs font-semibold {{ $loop->last ? 'text-primary-700' : 'text-gray-700' }}">{{ $loop->last ? 'Hoy' : $dia['etiqueta'] }}</p>
                            <p class="text-[10px] text-gray-400">{{ $dia['fecha'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Top productos del mes --}}
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Más vendidos del mes</h2>
            </div>
            @forelse($topProductos as $i => $producto)
                <div class="flex items-center gap-3 py-2.5 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold {{ $i === 0 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600' }}">{{ $i + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $producto->nombre }}</p>
                        <p class="text-xs text-gray-500">{{ \App\Support\Catalogos::cantidad($producto->unidades) }} vendidos</p>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">S/ {{ number_format($producto->total, 2) }}</span>
                </div>
            @empty
                <div class="empty-state">Aún no hay ventas este mes.</div>
            @endforelse
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Últimas ventas --}}
        <div class="card p-0 overflow-hidden xl:col-span-2">
            <div class="px-6 pt-5 pb-3 flex items-center justify-between">
                <h2 class="card-title">Últimas ventas</h2>
                <a href="{{ route('ventas.index') }}" class="link">Ver todas →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Venta</th>
                            <th>Cliente</th>
                            <th class="hidden sm:table-cell">Fecha</th>
                            <th class="text-right">Total</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ultimasVentas as $venta)
                            <tr class="cursor-pointer" onclick="window.location='{{ route('ventas.show', $venta) }}'">
                                <td class="font-medium text-gray-900">#{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</td>
                                <td class="truncate max-w-[160px]">{{ $venta->cliente->nombre ?? 'Cliente general' }}</td>
                                <td class="hidden sm:table-cell text-gray-500">{{ $venta->fecha?->format('d/m H:i') }}</td>
                                <td class="text-right font-semibold text-gray-900">S/ {{ number_format($venta->total, 2) }}</td>
                                <td class="text-center">
                                    <span class="badge {{ ['pagado' => 'badge-success', 'pendiente' => 'badge-warning', 'anulado' => 'badge-danger'][$venta->estado] ?? 'badge-gray' }}">
                                        {{ ucfirst($venta->estado) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><div class="empty-state">Todavía no registras ventas.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Alertas de stock --}}
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h2 class="card-title">Alertas de stock</h2>
                <a href="{{ route('repuestos.index') }}" class="link">Inventario →</a>
            </div>
            @forelse($alertasStock as $repuesto)
                <div class="flex items-center gap-3 py-2.5 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $repuesto->nombre }}</p>
                        <p class="text-xs text-gray-500">{{ $repuesto->codigo }} · mín. {{ $repuesto->minimo_stock }}</p>
                    </div>
                    <span class="badge {{ $repuesto->cantidad <= 0 ? 'badge-danger' : 'badge-warning' }}">
                        {{ $repuesto->cantidad <= 0 ? 'Agotado' : $repuesto->stock_formateado }}
                    </span>
                    <a href="{{ route('repuestos.cantidad', $repuesto->id) }}" class="btn-icon" title="Agregar stock">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </a>
                </div>
            @empty
                <div class="empty-state">
                    <svg class="w-8 h-8 mx-auto text-emerald-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Todo el inventario tiene stock suficiente.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
