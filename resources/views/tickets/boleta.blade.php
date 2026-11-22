<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta {{ $ticket->numero_formateado }}</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Courier New', Courier, monospace; font-size: 10px; margin: 0; padding: 5mm 3mm; color: #000; line-height: 1.25; }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .linea { border-bottom: 1px dashed #000; margin: 6px 0; }
        .nombre { font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 2px; }
        .datos { font-size: 9px; }
        table { width: 100%; border-collapse: collapse; }
        .items th { text-align: left; font-size: 9px; border-bottom: 1px dashed #000; padding-bottom: 3px; }
        .items td { padding: 3px 0; vertical-align: top; }
        .col-cant { width: 12%; text-align: center; }
        .col-pu, .col-imp { width: 20%; text-align: right; }
        .totales td { padding: 2px 0; }
        .anulado { margin-top: 6px; padding: 3px; border: 2px solid #b91c1c; color: #b91c1c; font-weight: bold; text-align: center; }
        .pie { margin-top: 12px; text-align: center; font-size: 9px; border-top: 1px dashed #000; padding-top: 8px; }
    </style>
</head>
<body>
    @php
        $logo = $empresa?->logoDataUri();
        $direccion = $empresa?->direccion_fiscal ?: $empresa?->direccion;
    @endphp

    {{-- Cabecera: logo arriba a la izquierda + datos de la empresa --}}
    <table>
        <tr>
            @if($logo)
                <td style="width: 22mm; vertical-align: top; padding-right: 2mm;">
                    <img src="{{ $logo }}" alt="Logo" style="max-width: 20mm; max-height: 20mm;">
                </td>
            @endif
            <td style="vertical-align: top; {{ $logo ? 'text-align: left;' : 'text-align: center;' }}">
                <div class="nombre">{{ $empresa?->nombre_comercial ?: ($empresa?->nombre ?? 'MI NEGOCIO') }}</div>
                <div class="datos">
                    @if($empresa?->razon_social && $empresa->razon_social !== $empresa->nombre_comercial){{ $empresa->razon_social }}<br>@endif
                    @if($empresa?->ruc)RUC: {{ $empresa->ruc }}<br>@endif
                    @if($direccion){{ $direccion }}<br>@endif
                    @if($empresa?->telefono)Telf: {{ $empresa->telefono }}<br>@endif
                    @if($empresa?->email){{ $empresa->email }}@endif
                </div>
            </td>
        </tr>
    </table>

    <div class="center bold" style="margin-top: 8px; font-size: 11px;">
        BOLETA DE VENTA<br>N° {{ $ticket->numero_formateado }}
    </div>
    <div class="linea"></div>

    <table>
        <tr><td class="bold" style="width: 18mm;">FECHA:</td><td>{{ $ticket->fecha?->format('d/m/Y H:i') }}</td></tr>
        <tr><td class="bold">CLIENTE:</td><td>{{ mb_strtoupper($ticket->cliente_nombre ?: 'Cliente general') }}</td></tr>
        @if($ticket->cliente_documento)
            <tr><td class="bold">{{ $ticket->tipo_documento }}:</td><td>{{ $ticket->cliente_documento }}</td></tr>
        @endif
    </table>
    <div class="linea"></div>

    <table class="items">
        <thead>
            <tr>
                <th class="col-cant">CANT</th>
                <th>DESCRIPCIÓN</th>
                <th class="col-pu">P.U.</th>
                <th class="col-imp">IMPORTE</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ticket->items as $item)
                <tr>
                    <td class="col-cant">{{ $ticket->cantidadTexto($item['cantidad']) }}</td>
                    <td>{{ mb_strtoupper($item['descripcion']) }}</td>
                    <td class="col-pu">{{ number_format($item['precio'], 2) }}</td>
                    <td class="col-imp">{{ number_format($item['importe'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="linea"></div>

    <table class="totales">
        <tr class="bold" style="font-size: 12px;">
            <td class="right" style="width: 60%;">TOTAL A PAGAR:</td>
            <td class="right">S/ {{ number_format($ticket->total, 2) }}</td>
        </tr>
    </table>
    <div style="margin-top: 4px; font-size: 9px;">SON: {{ $ticket->total_letras }}</div>

    @if($ticket->estaAnulado())
        <div class="anulado">*** DOCUMENTO ANULADO ***</div>
    @endif

    <div class="pie">
        ¡Gracias por su compra!<br>
        Documento interno · no es un comprobante electrónico SUNAT
    </div>
</body>
</html>
