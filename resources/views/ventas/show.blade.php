@extends('layouts.app')

@php
    $numero = '#' . str_pad($venta->id, 6, '0', STR_PAD_LEFT);
    $estados = ['pagado' => ['Pagada', 'badge-success'], 'pendiente' => ['Pendiente', 'badge-warning'], 'anulado' => ['Anulada', 'badge-danger']];
    $comprobante = $venta->comprobante;
    $tieneVigente = $venta->tieneComprobanteElectronico();
    $estadosSunat = [
        'aceptado' => ['Aceptado por SUNAT', 'badge-success'], 'observado' => ['Aceptado con observaciones', 'badge-success'],
        'pendiente' => ['Pendiente de respuesta', 'badge-warning'], 'rechazado' => ['Rechazado', 'badge-danger'],
        'baja_pendiente' => ['Anulación en proceso', 'badge-warning'], 'anulado' => ['Anulado', 'badge-gray'],
    ];
@endphp

@section('title', 'Venta ' . $numero)
@section('breadcrumb')
    <a href="{{ route('ventas.index') }}" class="hover:text-gray-900">Ventas</a>
    <span class="text-gray-300">/</span>
    <span class="text-gray-900">{{ $numero }}</span>
@endsection
@section('page-title')
    Venta {{ $numero }}
    <span class="badge {{ $estados[$venta->estado][1] ?? 'badge-gray' }} align-middle ml-2 text-xs">{{ $estados[$venta->estado][0] ?? ucfirst($venta->estado) }}</span>
@endsection
@section('page-description', $venta->fecha?->format('d/m/Y H:i') . ' · ' . $venta->fecha?->diffForHumans())

@section('page-actions')
    <a href="{{ route('ventas.ticket', $venta->id) }}" target="_blank" class="btn-secondary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Ticket
    </a>
    <a href="{{ route('ventas.boleta', $venta->id) }}" target="_blank" class="btn-secondary">Boleta PDF</a>
    <a href="{{ route('ventas.create') }}" class="btn-primary">Nueva venta</a>
@endsection

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Productos --}}
    <div class="xl:col-span-2 space-y-6">
        @if($venta->estado === 'anulado')
            <div class="alert alert-danger">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                <p>Esta venta está <strong>anulada</strong>. Los productos ya regresaron al stock.</p>
            </div>
        @endif

        <div class="card-flush">
            <div class="card-head">
                <h2 class="card-title">Productos</h2>
                <span class="text-sm text-gray-500">{{ $venta->detalles->count() }} {{ $venta->detalles->count() === 1 ? 'ítem' : 'ítems' }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="num">Cantidad</th>
                            <th class="num">Precio</th>
                            <th class="num">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($venta->detalles as $detalle)
                            <tr>
                                <td>
                                    <p class="font-medium text-gray-900">{{ $detalle->repuesto->nombre ?? 'Producto eliminado' }}</p>
                                    <p class="text-xs text-gray-500">{{ $detalle->repuesto->codigo ?? '' }}@if(($detalle->afectacion_igv ?? '10') !== '10') · {{ \App\Support\Catalogos::AFECTACIONES_IGV[$detalle->afectacion_igv] ?? '' }}@endif</p>
                                </td>
                                <td class="num whitespace-nowrap">{{ $detalle->cantidad_formateada }} {{ \App\Support\Catalogos::abreviaturaUnidad($detalle->unidad_medida) }}</td>
                                <td class="num whitespace-nowrap">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                                <td class="num whitespace-nowrap font-medium text-gray-900">S/ {{ number_format($detalle->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-200 px-5 py-4">
                <dl class="ml-auto max-w-xs space-y-1.5 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Op. gravada</dt><dd class="tabular-nums">S/ {{ number_format($venta->calcularBaseImponible(), 2) }}</dd></div>
                    @if($venta->calcularNoGravado() > 0)
                        <div class="flex justify-between"><dt class="text-gray-500">Op. exonerada / inafecta</dt><dd class="tabular-nums">S/ {{ number_format($venta->calcularNoGravado(), 2) }}</dd></div>
                    @endif
                    <div class="flex justify-between"><dt class="text-gray-500">IGV (18%)</dt><dd class="tabular-nums">S/ {{ number_format($venta->calcularIgv(), 2) }}</dd></div>
                    <div class="flex justify-between border-t border-gray-200 pt-2 text-base font-semibold text-gray-900"><dt>Total</dt><dd class="tabular-nums">S/ {{ number_format($venta->total, 2) }}</dd></div>
                </dl>
            </div>
        </div>
    </div>

    {{-- Panel lateral --}}
    <div class="space-y-6">
        <div class="card">
            <h2 class="card-title mb-3">Cliente</h2>
            @if($venta->cliente)
                <div class="flex items-center gap-3">
                    <span class="avatar w-10 h-10">{{ mb_strtoupper(mb_substr($venta->cliente->nombre, 0, 1)) }}</span>
                    <div class="min-w-0">
                        <p class="font-medium text-gray-900 truncate">{{ $venta->cliente->nombre }}</p>
                        <p class="text-xs text-gray-500">{{ $venta->cliente->documento_formateado }}</p>
                    </div>
                </div>
                <dl class="desc-list mt-3">
                    @if($venta->cliente->telefono)<div><dt>Teléfono</dt><dd>{{ $venta->cliente->telefono }}</dd></div>@endif
                    @if($venta->cliente->email)<div><dt>Email</dt><dd class="truncate max-w-[180px]">{{ $venta->cliente->email }}</dd></div>@endif
                </dl>
                <a href="{{ route('clientes.historial', $venta->cliente->id) }}" class="link mt-3 inline-block">Ver historial del cliente →</a>
            @else
                <p class="text-sm text-gray-500">Cliente general (sin registrar)</p>
            @endif
        </div>

        <div class="card">
            <h2 class="card-title mb-1">Pago</h2>
            <dl class="desc-list">
                <div><dt>Método</dt><dd>{{ $venta->metodo_pago_nombre }}</dd></div>
                <div><dt>Atendido por</dt><dd>{{ $venta->usuario->nombre ?? '—' }}</dd></div>
                <div><dt>Total</dt><dd>S/ {{ number_format($venta->total, 2) }}</dd></div>
            </dl>
        </div>

        {{-- Comprobante electrónico --}}
        @if($venta->estado === 'pagado')
            <div class="card">
                <h2 class="card-title mb-3">Comprobante electrónico</h2>

                @if($tieneVigente)
                    <div class="rounded-lg border border-gray-200 p-4">
                        <p class="text-xs text-gray-500">{{ $comprobante->tipo_comprobante === '01' ? 'Factura electrónica' : 'Boleta de venta electrónica' }}</p>
                        <p class="mt-0.5 font-mono text-lg font-semibold text-gray-900">{{ $comprobante->numero_completo }}</p>
                        @php [$texto, $clase] = $estadosSunat[$comprobante->anulado ? 'anulado' : $comprobante->estado_sunat] ?? [ucfirst($comprobante->estado_sunat), 'badge-gray']; @endphp
                        <span class="badge {{ $clase }} mt-2">{{ $texto }}</span>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <a href="{{ route('comprobantes.show', $comprobante) }}" class="btn-secondary">Ver detalle</a>
                        @if($comprobante->pdf_path)
                            <a href="{{ route('comprobantes.descargar-pdf', $comprobante) }}" class="btn-secondary">PDF</a>
                        @elseif($comprobante->xml_path)
                            <a href="{{ route('comprobantes.descargar-xml', $comprobante) }}" class="btn-secondary">XML</a>
                        @endif
                    </div>
                @elseif(auth()->user()->empresa?->tieneFacturacionActiva())
                    @if($comprobante && ($comprobante->estado_sunat === 'rechazado' || $comprobante->anulado))
                        <div class="alert alert-danger mb-4">
                            <div>
                                El comprobante {{ $comprobante->numero_completo }} fue <strong>{{ $comprobante->anulado ? 'anulado' : 'rechazado' }}</strong>.
                                @if($comprobante->mensaje_sunat)<span class="block mt-1 text-xs">{{ $comprobante->mensaje_sunat }}</span>@endif
                                <a href="{{ route('comprobantes.show', $comprobante) }}" class="underline text-xs">Ver detalle</a>
                            </div>
                        </div>
                    @endif

                    @php $conRuc = $venta->cliente && $venta->cliente->tieneRuc(); @endphp
                    <form action="{{ route('comprobantes.emitir', $venta) }}" method="POST" x-data="{ tipo: '{{ $conRuc ? '01' : '03' }}' }"
                          data-confirm="¿Emitir el comprobante electrónico?" data-confirm-detalle="Se enviará a SUNAT y no se podrá deshacer." data-confirm-text="Emitir">
                        @csrf
                        <div class="space-y-2">
                            @if($conRuc)
                                <label class="choice" :class="tipo === '01' && 'choice-active'">
                                    <input type="radio" name="tipo_comprobante" value="01" x-model="tipo" class="sr-only">
                                    <span class="text-sm font-medium text-gray-900">Factura electrónica</span>
                                    <span class="text-xs text-gray-500">RUC {{ $venta->cliente->ruc }}</span>
                                </label>
                            @endif
                            <label class="choice" :class="tipo === '03' && 'choice-active'">
                                <input type="radio" name="tipo_comprobante" value="03" x-model="tipo" class="sr-only">
                                <span class="text-sm font-medium text-gray-900">Boleta de venta electrónica</span>
                                <span class="text-xs text-gray-500">{{ $venta->cliente ? 'DNI ' . ($venta->cliente->dni ?: '—') : 'Clientes varios' }}</span>
                            </label>
                        </div>
                        @unless($conRuc)
                            <p class="form-help mt-2">Para emitir factura, registra el RUC del cliente.</p>
                        @endunless
                        @error('tipo_comprobante')<p class="form-error">{{ $message }}</p>@enderror
                        <button type="submit" class="btn-primary w-full mt-4">Emitir comprobante</button>
                    </form>
                @else
                    <p class="text-sm text-gray-500">La facturación electrónica no está configurada.</p>
                    @if(auth()->user()->esAdmin())
                        <a href="{{ route('empresa.index') }}" class="btn-secondary w-full mt-3">Configurar facturación</a>
                    @endif
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
