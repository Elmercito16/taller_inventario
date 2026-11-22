@extends('layouts.app')

@php
    $esFactura = $comprobante->tipo_comprobante === '01';
    $tipoDoc = ['6' => 'RUC', '1' => 'DNI', '0' => 'Sin documento'][$comprobante->cliente_tipo_doc] ?? 'Documento';
@endphp

@section('title', $comprobante->numero_completo)
@section('breadcrumb')
    <a href="{{ route('comprobantes.index') }}" class="hover:text-gray-900">Comprobantes</a>
    <span class="text-gray-300">/</span>
    <span class="text-gray-900">{{ $comprobante->numero_completo }}</span>
@endsection
@section('page-title')
    <span class="font-mono">{{ $comprobante->numero_completo }}</span>
    <x-estado-sunat :comprobante="$comprobante" class="align-middle ml-2 text-xs" />
@endsection
@section('page-description', ($esFactura ? 'Factura electrónica' : 'Boleta de venta electrónica') . ' · Emitida el ' . $comprobante->fecha_emision?->format('d/m/Y H:i'))

@section('page-actions')
    @if($comprobante->pdf_path)
        <a href="{{ route('comprobantes.ver-pdf', $comprobante) }}" target="_blank" class="btn-secondary">Ver PDF</a>
    @endif
    @if($comprobante->venta)
        <a href="{{ route('ventas.show', $comprobante->venta) }}" class="btn-secondary">Ver venta</a>
    @endif
@endsection

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    <div class="xl:col-span-2 space-y-6">
        <div class="card">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Cliente</p>
                    <p class="mt-1 font-medium text-gray-900">{{ $comprobante->cliente_nombre ?: 'Clientes varios' }}</p>
                    <p class="text-sm text-gray-500">{{ $tipoDoc }} {{ $comprobante->cliente_num_doc }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Moneda</p>
                    <p class="mt-1 font-medium text-gray-900">{{ ! $comprobante->moneda || $comprobante->moneda === 'PEN' ? 'Soles (PEN)' : $comprobante->moneda }}</p>
                </div>
            </div>
        </div>

        <div class="card-flush">
            <div class="card-head"><h2 class="card-title">Detalle</h2></div>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Descripción</th>
                            <th class="num">Cantidad</th>
                            <th class="num">P. unitario</th>
                            <th class="num">Importe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($comprobante->venta?->detalles ?? [] as $detalle)
                            <tr>
                                <td class="text-gray-900">{{ $detalle->repuesto->nombre ?? 'Producto eliminado' }}</td>
                                <td class="num">{{ $detalle->cantidad_formateada }}</td>
                                <td class="num">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                                <td class="num font-medium text-gray-900">S/ {{ number_format($detalle->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-200 px-5 py-4">
                <dl class="ml-auto max-w-xs space-y-1.5 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Op. gravada</dt><dd class="tabular-nums">S/ {{ number_format($comprobante->total_gravadas ?? $comprobante->subtotal, 2) }}</dd></div>
                    @if((float) $comprobante->total_exoneradas > 0)
                        <div class="flex justify-between"><dt class="text-gray-500">Op. exonerada</dt><dd class="tabular-nums">S/ {{ number_format($comprobante->total_exoneradas, 2) }}</dd></div>
                    @endif
                    @if((float) $comprobante->total_inafectas > 0)
                        <div class="flex justify-between"><dt class="text-gray-500">Op. inafecta</dt><dd class="tabular-nums">S/ {{ number_format($comprobante->total_inafectas, 2) }}</dd></div>
                    @endif
                    <div class="flex justify-between"><dt class="text-gray-500">IGV (18%)</dt><dd class="tabular-nums">S/ {{ number_format($comprobante->igv, 2) }}</dd></div>
                    <div class="flex justify-between border-t border-gray-200 pt-2 text-base font-semibold text-gray-900"><dt>Total</dt><dd class="tabular-nums">S/ {{ number_format($comprobante->total, 2) }}</dd></div>
                </dl>
                @if($comprobante->total_letras)
                    <p class="mt-3 text-right text-xs text-gray-500">SON: {{ $comprobante->total_letras }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="card">
            <h2 class="card-title mb-3">Respuesta de SUNAT</h2>
            <x-estado-sunat :comprobante="$comprobante" />
            @if($comprobante->mensaje_sunat)
                <p class="mt-3 text-sm text-gray-700">{{ $comprobante->mensaje_sunat }}</p>
            @endif
            @if($comprobante->observaciones_sunat)
                <div class="alert alert-warning mt-3 !p-3">
                    <p><span class="font-medium">Observaciones:</span> {{ $comprobante->observaciones_sunat }}</p>
                </div>
            @endif
            <dl class="desc-list mt-3">
                @if($comprobante->codigo_sunat !== null && $comprobante->codigo_sunat !== '')<div><dt>Código</dt><dd class="font-mono">{{ $comprobante->codigo_sunat }}</dd></div>@endif
                <div><dt>Ambiente</dt><dd>{{ $comprobante->ambiente === 'produccion' ? 'Producción' : 'Pruebas (Beta)' }}</dd></div>
                <div><dt>Enviado</dt><dd>{{ $comprobante->fecha_envio_sunat?->format('d/m/Y H:i') ?? '—' }}</dd></div>
                <div><dt>Emitido por</dt><dd>{{ $comprobante->usuarioEmision->nombre ?? '—' }}</dd></div>
            </dl>
            @if(in_array($comprobante->estado_sunat, ['pendiente', 'baja_pendiente']))
                <a href="{{ route('comprobantes.consultar-estado', $comprobante) }}" class="btn-primary w-full mt-4">
                    {{ $comprobante->estado_sunat === 'pendiente' ? 'Reenviar a SUNAT' : 'Consultar estado de la baja' }}
                </a>
            @endif
        </div>

        <div class="card">
            <h2 class="card-title mb-3">Archivos</h2>
            <div class="space-y-2">
                @foreach([['pdf_path', 'descargar-pdf', 'Representación impresa (PDF)'], ['xml_path', 'descargar-xml', 'XML firmado'], ['cdr_path', 'descargar-cdr', 'Constancia de recepción (CDR)']] as [$campo, $ruta, $texto])
                    @if($comprobante->{$campo})
                        <a href="{{ route('comprobantes.' . $ruta, $comprobante) }}" class="flex items-center justify-between rounded-lg border border-gray-200 px-3 py-2.5 text-sm hover:bg-gray-50">
                            <span class="text-gray-700">{{ $texto }}</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </a>
                    @else
                        <div class="flex items-center justify-between rounded-lg border border-dashed border-gray-200 px-3 py-2.5 text-sm text-gray-400">
                            <span>{{ $texto }}</span><span class="shrink-0 whitespace-nowrap pl-3">No disponible</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        @if($comprobante->anulado || $comprobante->fecha_anulacion)
            <div class="card">
                <h2 class="card-title mb-2">Anulación</h2>
                <dl class="desc-list">
                    <div><dt>Fecha</dt><dd>{{ $comprobante->fecha_anulacion?->format('d/m/Y H:i') ?? '—' }}</dd></div>
                    <div><dt>Motivo</dt><dd class="max-w-[200px]">{{ $comprobante->motivo_anulacion ?? '—' }}</dd></div>
                </dl>
            </div>
        @elseif($comprobante->puedeSerAnulado())
            <div class="card">
                <h2 class="card-title">Anular comprobante</h2>
                <p class="card-subtitle mb-4">Se enviará {{ str_starts_with($comprobante->serie, 'B') ? 'el resumen diario de anulación' : 'la comunicación de baja' }} a SUNAT.</p>
                <form method="POST" action="{{ route('comprobantes.anular', $comprobante) }}" class="space-y-3"
                      data-confirm="¿Anular {{ $comprobante->numero_completo }}?" data-confirm-detalle="Esta acción no se puede deshacer." data-confirm-text="Anular" data-confirm-danger>
                    @csrf
                    <div>
                        <label class="form-label">Motivo</label>
                        <input type="text" name="motivo_anulacion" class="form-input @error('motivo_anulacion') form-input-error @enderror" minlength="10" maxlength="100" required placeholder="Ej.: Error en los datos del cliente" value="{{ old('motivo_anulacion') }}">
                        @error('motivo_anulacion')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <button class="btn-danger-soft w-full">Anular comprobante</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
