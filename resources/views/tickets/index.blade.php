@extends('layouts.app')

@section('title', 'Tickets')
@section('page-title', 'Tickets emitidos')
@section('page-description', 'Ventas rápidas registradas a mano (sin control de inventario)')

@section('page-actions')
    <a href="{{ route('tickets.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nuevo ticket
    </a>
@endsection

@section('content')
@php $filtrando = collect(request()->only(['q', 'desde', 'hasta']))->filter()->isNotEmpty(); @endphp
<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="stat-card">
            <p class="stat-label">Vendido hoy</p>
            <p class="stat-value">S/ {{ number_format($resumen['hoy'], 2) }}</p>
            <p class="stat-foot">{{ $resumen['cantidad_hoy'] }} {{ $resumen['cantidad_hoy'] === 1 ? 'ticket' : 'tickets' }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Vendido este mes</p>
            <p class="stat-value">S/ {{ number_format($resumen['mes'], 2) }}</p>
        </div>
        <a href="{{ route('tickets.create') }}" class="stat-card flex items-center justify-center gap-2 border-dashed !border-primary-300 text-primary-700 hover:bg-primary-50/40 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="font-medium">Nuevo ticket</span>
        </a>
    </div>

    <div class="card-flush">
        <form method="GET" class="toolbar">
            <div class="flex-1">
                <label class="form-label">Buscar</label>
                <div class="input-search">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-input" placeholder="N° de ticket, cliente, DNI o RUC">
                </div>
            </div>
            <div class="lg:w-40">
                <label class="form-label">Desde</label>
                <input type="date" name="desde" value="{{ request('desde') }}" class="form-input">
            </div>
            <div class="lg:w-40">
                <label class="form-label">Hasta</label>
                <input type="date" name="hasta" value="{{ request('hasta') }}" class="form-input">
            </div>
            <div class="flex gap-2">
                <button class="btn-secondary">Filtrar</button>
                @if($filtrando)<a href="{{ route('tickets.index') }}" class="btn-ghost">Limpiar</a>@endif
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ticket</th>
                        <th>Cliente</th>
                        <th class="num hidden sm:table-cell">Ítems</th>
                        <th class="num">Total</th>
                        <th class="w-px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr class="{{ $ticket->estaAnulado() ? 'opacity-60' : '' }}">
                            <td class="whitespace-nowrap">
                                <a href="{{ route('tickets.show', $ticket) }}" class="font-medium text-gray-900 hover:text-primary-700">{{ $ticket->numero_formateado }}</a>
                                @if($ticket->estaAnulado())<span class="badge badge-danger ml-1">Anulado</span>@endif
                                <p class="text-xs text-gray-500">{{ $ticket->fecha?->format('d/m/Y H:i') }}</p>
                            </td>
                            <td class="max-w-[240px]">
                                <p class="text-gray-900 truncate">{{ $ticket->cliente_nombre ?: 'Cliente general' }}</p>
                                @if($ticket->cliente_documento)<p class="text-xs text-gray-500">{{ $ticket->tipo_documento }} {{ $ticket->cliente_documento }}</p>@endif
                            </td>
                            <td class="num hidden sm:table-cell">{{ count($ticket->items) }}</td>
                            <td class="num font-medium text-gray-900">S/ {{ number_format($ticket->total, 2) }}</td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('tickets.imprimir', $ticket) }}" target="_blank" class="btn-icon" title="Imprimir ticket">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    </a>
                                    <a href="{{ route('tickets.pdf', $ticket) }}" target="_blank" class="btn-icon" title="Boleta PDF">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                    <a href="{{ route('tickets.show', $ticket) }}" class="btn-icon" title="Ver"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    @if($filtrando)
                                        <p class="font-medium text-gray-900">Sin resultados</p>
                                    @else
                                        <p class="font-medium text-gray-900">Aún no hay tickets</p>
                                        <p class="mt-1">Registra tu primera venta rápida.</p>
                                        <a href="{{ route('tickets.create') }}" class="btn-primary mt-4">Nuevo ticket</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
            <div class="card-foot">{{ $tickets->links() }}</div>
        @endif
    </div>
</div>
@endsection
