@props(['comprobante'])
@php
    $estado = $comprobante->anulado ? 'anulado' : $comprobante->estado_sunat;
    [$texto, $clase] = [
        'aceptado' => ['Aceptado', 'badge-success'],
        'observado' => ['Aceptado con obs.', 'badge-success'],
        'pendiente' => ['Pendiente', 'badge-warning'],
        'rechazado' => ['Rechazado', 'badge-danger'],
        'baja_pendiente' => ['Baja en proceso', 'badge-warning'],
        'anulado' => ['Anulado', 'badge-gray'],
    ][$estado] ?? [ucfirst((string) $estado), 'badge-gray'];
@endphp
<span {{ $attributes->merge(['class' => "badge $clase"]) }}>{{ $texto }}</span>
