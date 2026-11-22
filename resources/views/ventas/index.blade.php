@extends('layouts.app')

@section('title', 'Ventas')
@section('page-title', 'Historial de ventas')
@section('page-description', 'Todas las ventas registradas en el punto de venta')

@section('page-actions')
    <a href="{{ route('ventas.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nueva venta
    </a>
@endsection

@section('content')
@php
    $filtrando = collect(request()->only(['q', 'estado', 'metodo_pago', 'desde', 'hasta']))->filter()->isNotEmpty();
    $estados = ['pagado' => ['Pagada', 'badge-success'], 'pendiente' => ['Pendiente', 'badge-warning'], 'anulado' => ['Anulada', 'badge-danger']];
@endphp
<div class="space-y-6" x-data="ventasLista()">
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="stat-card">
            <p class="stat-label">Vendido hoy</p>
            <p class="stat-value">S/ {{ number_format($resumen['hoy'], 2) }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Total {{ $filtrando ? 'del filtro' : 'histórico' }}</p>
            <p class="stat-value">S/ {{ number_format($resumen['total'], 2) }}</p>
            <p class="stat-foot">Solo ventas pagadas</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Ventas</p>
            <p class="stat-value">{{ number_format($resumen['cantidad']) }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Anuladas</p>
            <p class="stat-value">{{ number_format($resumen['anuladas']) }}</p>
        </div>
    </div>

    <div class="card-flush">
        <form method="GET" class="toolbar">
            <div class="flex-1 min-w-0">
                <label class="form-label">Buscar</label>
                <div class="input-search">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-input" placeholder="N° de venta, cliente, DNI o RUC">
                </div>
            </div>
            <div class="lg:w-36">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-input">
                    <option value="">Todos</option>
                    @foreach($estados as $valor => [$texto])
                        <option value="{{ $valor }}" @selected(request('estado') === $valor)>{{ $texto }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:w-40">
                <label class="form-label">Pago</label>
                <select name="metodo_pago" class="form-input">
                    <option value="">Todos</option>
                    @foreach(\App\Models\Venta::METODOS_PAGO as $valor => $texto)
                        <option value="{{ $valor }}" @selected(request('metodo_pago') === $valor)>{{ $texto }}</option>
                    @endforeach
                </select>
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
                @if($filtrando)<a href="{{ route('ventas.index') }}" class="btn-ghost">Limpiar</a>@endif
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Venta</th>
                        <th>Cliente</th>
                        <th class="hidden lg:table-cell">Pago</th>
                        <th class="hidden md:table-cell">Comprobante</th>
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
                            <td class="max-w-[240px]">
                                <p class="text-gray-900 truncate">{{ $venta->cliente->nombre ?? 'Cliente general' }}</p>
                                <p class="text-xs text-gray-500">{{ $venta->detalles_count }} {{ $venta->detalles_count === 1 ? 'ítem' : 'ítems' }}@if($venta->usuario) · {{ $venta->usuario->nombre }}@endif</p>
                            </td>
                            <td class="hidden lg:table-cell text-gray-500">{{ $venta->metodo_pago_nombre }}</td>
                            <td class="hidden md:table-cell">
                                @if($venta->comprobante && ! $venta->comprobante->anulado && $venta->comprobante->estado_sunat !== 'rechazado')
                                    <a href="{{ route('comprobantes.show', $venta->comprobante) }}" class="font-mono text-xs text-gray-700 hover:text-primary-700">{{ $venta->comprobante->numero_completo }}</a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="num font-medium text-gray-900 whitespace-nowrap">S/ {{ number_format($venta->total, 2) }}</td>
                            <td class="center">
                                <span class="badge {{ $estados[$venta->estado][1] ?? 'badge-gray' }}">{{ $estados[$venta->estado][0] ?? ucfirst($venta->estado) }}</span>
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('ventas.show', $venta) }}" class="btn-icon" title="Ver detalle">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('ventas.ticket', $venta->id) }}" target="_blank" class="btn-icon" title="Imprimir ticket">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    </a>
                                    @if($venta->estado === 'anulado')
                                        <button type="button" class="btn-icon" title="Reactivar venta" @click="cambiarEstado({{ $venta->id }}, 'pagado')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        </button>
                                    @else
                                        <button type="button" class="btn-icon-danger" title="Anular venta" @click="anular({{ $venta->id }})">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-icon"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg></div>
                                    @if($filtrando)
                                        <p class="font-medium text-gray-900">Sin resultados</p>
                                        <p class="mt-1">No hay ventas con esos filtros.</p>
                                    @else
                                        <p class="font-medium text-gray-900">Aún no hay ventas</p>
                                        <p class="mt-1">Registra tu primera venta desde el punto de venta.</p>
                                        <a href="{{ route('ventas.create') }}" class="btn-primary mt-4">Nueva venta</a>
                                    @endif
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

@push('scripts')
<script>
    function ventasLista() {
        const url = (id, accion) => @js(url('ventas')) + '/' + id + '/' + accion;

        // Muestra el mensaje real del servidor (stock, comprobante vigente, etc.)
        async function enviar(id, accion, metodo, cuerpo = null) {
            Swal.fire({ title: 'Procesando…', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            try {
                const res = await fetch(url(id, accion), {
                    method: metodo,
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': @js(csrf_token()) },
                    body: cuerpo ? JSON.stringify(cuerpo) : null,
                });
                const data = await res.json().catch(() => ({}));
                if (res.ok && data.success) {
                    await Dialogo.fire({ icon: 'success', title: 'Listo', text: data.message || 'Operación realizada', timer: 1600, showConfirmButton: false });
                    location.reload();
                } else {
                    Dialogo.fire({ icon: 'error', title: 'No se pudo completar', text: data.message || 'Ocurrió un error inesperado.' });
                }
            } catch (e) {
                Dialogo.fire({ icon: 'error', title: 'Error de conexión', text: 'Revisa tu conexión e inténtalo de nuevo.' });
            }
        }

        return {
            async anular(id) {
                const r = await Dialogo.fire({
                    icon: 'warning',
                    title: '¿Anular la venta #' + String(id).padStart(6, '0') + '?',
                    text: 'Los productos regresarán al stock.',
                    showCancelButton: true,
                    confirmButtonText: 'Anular venta',
                    cancelButtonText: 'Cancelar',
                    customClass: { confirmButton: 'btn-danger mx-1', cancelButton: 'btn-secondary mx-1', popup: 'rounded-xl' },
                });
                if (r.isConfirmed) enviar(id, 'anular', 'POST');
            },
            async cambiarEstado(id, estado) {
                const r = await Dialogo.fire({
                    icon: 'question',
                    title: '¿Reactivar la venta?',
                    text: 'Se volverá a descontar el stock de los productos.',
                    showCancelButton: true,
                    confirmButtonText: 'Reactivar',
                    cancelButtonText: 'Cancelar',
                });
                if (r.isConfirmed) enviar(id, 'estado', 'PATCH', { estado });
            },
        };
    }
</script>
@endpush
