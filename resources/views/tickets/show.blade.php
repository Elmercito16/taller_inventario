@extends('layouts.app')

@section('title', 'Ticket ' . $ticket->numero_formateado)
@section('breadcrumb')
    <a href="{{ route('tickets.index') }}" class="hover:text-gray-900">Tickets</a>
    <span class="text-gray-300">/</span>
    <span class="text-gray-900">{{ $ticket->numero_formateado }}</span>
@endsection
@section('page-title')
    Ticket {{ $ticket->numero_formateado }}
    @if($ticket->estaAnulado())<span class="badge badge-danger align-middle ml-2 text-xs">Anulado</span>@endif
@endsection
@section('page-description', $ticket->fecha?->format('d/m/Y H:i') . ' · ' . ($ticket->usuario->nombre ?? ''))

@section('content')
<div class="max-w-2xl mx-auto space-y-4">

    {{-- Acciones principales --}}
    @unless($ticket->estaAnulado())
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('tickets.imprimir', $ticket) }}" target="_blank" class="btn-primary btn-lg !py-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Imprimir ticket
            </a>
            <a href="{{ route('tickets.pdf', $ticket) }}" target="_blank" class="btn-secondary btn-lg !py-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Boleta PDF
            </a>
        </div>
    @endunless

    <div class="card-flush">
        <div class="p-5 border-b border-gray-100">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Cliente</p>
            <p class="mt-1 font-medium text-gray-900">{{ $ticket->cliente_nombre ?: 'Cliente general' }}</p>
            @if($ticket->cliente_documento)
                <p class="text-sm text-gray-500">{{ $ticket->tipo_documento }} {{ $ticket->cliente_documento }}</p>
            @endif
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th class="num">Cant.</th>
                    <th class="num">P. unit.</th>
                    <th class="num">Importe</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ticket->items as $item)
                    <tr>
                        <td class="text-gray-900">{{ $item['descripcion'] }}</td>
                        <td class="num">{{ $ticket->cantidadTexto($item['cantidad']) }}</td>
                        <td class="num">S/ {{ number_format($item['precio'], 2) }}</td>
                        <td class="num font-medium text-gray-900">S/ {{ number_format($item['importe'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right font-semibold text-gray-900">Total</td>
                    <td class="num text-lg font-semibold text-gray-900">S/ {{ number_format($ticket->total, 2) }}</td>
                </tr>
            </tfoot>
        </table>
        <p class="px-5 pb-4 text-right text-xs text-gray-500">SON: {{ $ticket->total_letras }}</p>
    </div>

    <div class="flex flex-wrap justify-between gap-3">
        <a href="{{ route('tickets.create') }}" class="btn-primary">+ Nuevo ticket</a>
        @if(! $ticket->estaAnulado() && in_array(auth()->user()->rol, ['admin', 'encargado']))
            <form method="POST" action="{{ route('tickets.anular', $ticket) }}"
                  data-confirm="¿Anular el ticket {{ $ticket->numero_formateado }}?" data-confirm-text="Anular" data-confirm-danger>
                @csrf
                <button class="btn-danger-soft">Anular ticket</button>
            </form>
        @endif
    </div>
</div>
@endsection
