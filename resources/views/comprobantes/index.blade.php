@extends('layouts.app')

@section('title', 'Comprobantes electrónicos')
@section('page-title', 'Comprobantes electrónicos')
@section('page-description', 'Facturas y boletas enviadas a SUNAT')

@section('page-actions')
    <a href="{{ route('ventas.index') }}" class="btn-secondary">Emitir desde una venta</a>
@endsection

@section('content')
@php $filtrando = collect(request()->only(['buscar', 'tipo', 'estado', 'fecha_inicio', 'fecha_fin']))->filter()->isNotEmpty(); @endphp
<div class="card-flush">
    <form method="GET" class="toolbar">
        <div class="flex-1 min-w-0">
            <label class="form-label">Buscar</label>
            <div class="input-search">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-input" placeholder="Número, cliente o documento">
            </div>
        </div>
        <div class="lg:w-36">
            <label class="form-label">Tipo</label>
            <select name="tipo" class="form-input">
                <option value="">Todos</option>
                <option value="01" @selected(request('tipo') === '01')>Facturas</option>
                <option value="03" @selected(request('tipo') === '03')>Boletas</option>
            </select>
        </div>
        <div class="lg:w-44">
            <label class="form-label">Estado SUNAT</label>
            <select name="estado" class="form-input">
                <option value="">Todos</option>
                @foreach(['aceptado' => 'Aceptado', 'observado' => 'Observado', 'pendiente' => 'Pendiente', 'rechazado' => 'Rechazado', 'baja_pendiente' => 'Baja en proceso', 'anulado' => 'Anulado'] as $valor => $texto)
                    <option value="{{ $valor }}" @selected(request('estado') === $valor)>{{ $texto }}</option>
                @endforeach
            </select>
        </div>
        <div class="lg:w-40">
            <label class="form-label">Desde</label>
            <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="form-input">
        </div>
        <div class="lg:w-40">
            <label class="form-label">Hasta</label>
            <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" class="form-input">
        </div>
        <div class="flex gap-2">
            <button class="btn-secondary">Filtrar</button>
            @if($filtrando)<a href="{{ route('comprobantes.index') }}" class="btn-ghost">Limpiar</a>@endif
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th>Comprobante</th>
                    <th>Cliente</th>
                    <th class="hidden md:table-cell">Emisión</th>
                    <th class="num">Total</th>
                    <th class="center">SUNAT</th>
                    <th class="w-px"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($comprobantes as $comprobante)
                    <tr>
                        <td class="whitespace-nowrap">
                            <a href="{{ route('comprobantes.show', $comprobante) }}" class="font-mono font-medium text-gray-900 hover:text-primary-700">{{ $comprobante->numero_completo }}</a>
                            <p class="text-xs text-gray-500">{{ $comprobante->tipo_comprobante === '01' ? 'Factura' : 'Boleta' }}</p>
                        </td>
                        <td class="max-w-[260px]">
                            <p class="text-gray-900 truncate">{{ $comprobante->cliente_nombre ?: 'Clientes varios' }}</p>
                            <p class="text-xs text-gray-500">{{ $comprobante->cliente_num_doc }}</p>
                        </td>
                        <td class="hidden md:table-cell text-gray-500 whitespace-nowrap">{{ $comprobante->fecha_emision?->format('d/m/Y H:i') }}</td>
                        <td class="num font-medium text-gray-900 whitespace-nowrap">S/ {{ number_format($comprobante->total, 2) }}</td>
                        <td class="center"><x-estado-sunat :comprobante="$comprobante" /></td>
                        <td>
                            <div class="flex items-center justify-end gap-1">
                                @if($comprobante->pdf_path)
                                    <a href="{{ route('comprobantes.descargar-pdf', $comprobante) }}" class="btn-icon" title="Descargar PDF">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                @endif
                                <a href="{{ route('comprobantes.show', $comprobante) }}" class="btn-icon" title="Ver detalle">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-icon"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                                @if($filtrando)
                                    <p class="font-medium text-gray-900">Sin resultados</p>
                                    <p class="mt-1">No hay comprobantes con esos filtros.</p>
                                @else
                                    <p class="font-medium text-gray-900">Aún no hay comprobantes</p>
                                    <p class="mt-1">Emite boletas y facturas desde el detalle de cada venta.</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($comprobantes->hasPages())
        <div class="card-foot">{{ $comprobantes->links() }}</div>
    @endif
</div>
@endsection
