@extends('layouts.app')

@section('title', 'Compra ' . $compra->numero)
@section('page-title', 'Compra ' . $compra->numero)
@section('page-description', $compra->documento_texto . ' · ' . ($compra->proveedor->nombre ?? 'Sin proveedor'))

@section('content')
<div class="space-y-6">

    <div class="flex flex-wrap items-center justify-between gap-3 print:hidden">
        <a href="{{ route('compras.index') }}" class="btn-secondary">← Volver a compras</a>
        <div class="flex gap-3">
            <button type="button" onclick="window.print()" class="btn-secondary">Imprimir</button>
            @unless($compra->estaAnulada())
                <button type="button" onclick="anularCompra()" class="btn-danger">Anular compra</button>
            @endunless
        </div>
    </div>

    @if($compra->estaAnulada())
        <div class="alert alert-danger">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            <div>
                <p class="font-semibold">Compra anulada el {{ $compra->fecha_anulacion?->format('d/m/Y H:i') }}</p>
                <p class="text-sm">Motivo: {{ $compra->motivo_anulacion }}. La mercadería fue retirada del stock.</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <p class="stat-label">Fecha</p>
            <p class="stat-value !text-lg">{{ $compra->fecha?->format('d/m/Y') }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Proveedor</p>
            <p class="stat-value !text-lg truncate">{{ $compra->proveedor->nombre ?? 'Sin proveedor' }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Registrada por</p>
            <p class="stat-value !text-lg truncate">{{ $compra->usuario->nombre ?? '—' }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Total</p>
            <p class="stat-value !text-lg">S/ {{ number_format($compra->total, 2) }}</p>
        </div>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="px-6 pt-5 pb-3">
            <h2 class="card-title">Detalle</h2>
            @if($compra->observaciones)
                <p class="card-subtitle">{{ $compra->observaciones }}</p>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th class="text-right">Cantidad</th>
                        <th class="text-right">Costo unit.</th>
                        <th class="text-right">Subtotal</th>
                        <th class="print:hidden"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($compra->detalles as $detalle)
                        <tr>
                            <td>
                                <p class="font-medium text-gray-900">{{ $detalle->repuesto->nombre ?? 'Producto eliminado' }}</p>
                                <p class="text-xs text-gray-500">{{ $detalle->repuesto->codigo ?? '' }}</p>
                            </td>
                            <td class="text-right">{{ $detalle->cantidad_formateada }} {{ $detalle->repuesto->unidad_abreviatura ?? '' }}</td>
                            <td class="text-right">S/ {{ number_format($detalle->costo_unitario, 2) }}</td>
                            <td class="text-right font-semibold text-gray-900">S/ {{ number_format($detalle->subtotal, 2) }}</td>
                            <td class="text-right print:hidden">
                                @if($detalle->repuesto)
                                    <a href="{{ route('kardex.show', $detalle->repuesto) }}" class="link text-xs">Kardex →</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right font-semibold text-gray-700">TOTAL</td>
                        <td class="px-6 py-4 text-right text-lg font-bold text-gray-900">S/ {{ number_format($compra->total, 2) }}</td>
                        <td class="print:hidden"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Movimientos de kardex generados --}}
    <div class="card print:hidden">
        <div class="card-header">
            <h2 class="card-title">Movimientos de inventario</h2>
            <p class="card-subtitle">Registro en el kardex generado por esta compra</p>
        </div>
        <div class="space-y-2">
            @foreach($compra->movimientos->sortBy('id') as $mov)
                <div class="flex items-center gap-3 text-sm">
                    <span class="badge {{ $mov->esEntrada() ? 'badge-success' : 'badge-danger' }}">{{ $mov->esEntrada() ? '+' : '−' }}{{ $mov->cantidadTexto($mov->cantidad) }}</span>
                    <span class="flex-1 truncate">{{ $mov->repuesto->nombre ?? 'Producto eliminado' }} · {{ $mov->motivo_nombre }}</span>
                    <span class="text-gray-500">{{ $mov->cantidadTexto($mov->stock_anterior) }} → <strong class="text-gray-900">{{ $mov->cantidadTexto($mov->stock_nuevo) }}</strong></span>
                    <span class="text-xs text-gray-400 hidden sm:inline">{{ $mov->fecha?->format('d/m/Y H:i') }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

@unless($compra->estaAnulada())
    <form id="form-anular-compra" method="POST" action="{{ route('compras.anular', $compra) }}" class="hidden">
        @csrf
        <input type="hidden" name="motivo" id="motivo-anular-compra">
    </form>
@endunless
@endsection

@push('scripts')
<script>
    async function anularCompra() {
        const { value: motivo } = await Swal.fire({
            title: 'Anular compra',
            html: '<p class="text-sm text-gray-600">Se retirará del stock toda la mercadería de esta compra. Si parte ya se vendió, no se podrá anular.</p>',
            input: 'text',
            inputLabel: 'Motivo',
            inputPlaceholder: 'Ej.: Devolución al proveedor',
            showCancelButton: true,
            confirmButtonText: 'Anular',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc2626',
            inputValidator: v => (!v || v.trim().length < 5) ? 'Escribe un motivo (mínimo 5 caracteres)' : undefined,
        });

        if (motivo) {
            document.getElementById('motivo-anular-compra').value = motivo.trim();
            document.getElementById('form-anular-compra').submit();
        }
    }
</script>
@endpush
