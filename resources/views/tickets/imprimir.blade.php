<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket {{ $ticket->numero_formateado }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Courier New', monospace; font-size: 12px; color: #111; background: #f3f4f6; }
        .ticket { width: 80mm; margin: 16px auto; background: #fff; padding: 12px 10px; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: 700; }
        .sep { border-top: 1px dashed #555; margin: 8px 0; }
        .cabecera { display: flex; align-items: flex-start; gap: 8px; }
        .logo { width: 20mm; max-height: 20mm; object-fit: contain; flex-shrink: 0; }
        .empresa { font-size: 11px; line-height: 1.35; }
        .empresa .nombre { font-size: 13px; font-weight: 700; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; vertical-align: top; }
        .total { font-size: 15px; }
        .anulado { margin-top: 6px; padding: 4px; border: 2px solid #b91c1c; color: #b91c1c; font-weight: 700; text-align: center; }
        .acciones { width: 80mm; margin: 0 auto 16px; display: flex; gap: 8px; }
        .acciones button, .acciones a { flex: 1; padding: 10px; border: 0; border-radius: 8px; font: 600 13px system-ui, sans-serif; cursor: pointer; text-align: center; text-decoration: none; }
        .btn-print { background: #218786; color: #fff; }
        .btn-back { background: #e5e7eb; color: #111; }
        @media print {
            body { background: #fff; }
            .ticket { margin: 0; box-shadow: none; width: 100%; }
            .acciones { display: none; }
            @page { size: 80mm auto; margin: 2mm; }
        }
    </style>
</head>
<body>
    @php
        $logo = $empresa?->logoDataUri();
        $direccion = $empresa?->direccion_fiscal ?: $empresa?->direccion;
    @endphp
    <div class="ticket">
        {{-- Cabecera: logo arriba a la izquierda + datos de la empresa --}}
        <div class="{{ $logo ? 'cabecera' : 'center' }}">
            @if($logo)<img src="{{ $logo }}" alt="Logo" class="logo">@endif
            <div class="empresa">
                <div class="nombre">{{ $empresa?->nombre_comercial ?: ($empresa?->nombre ?? 'MI NEGOCIO') }}</div>
                @if($empresa?->razon_social && $empresa->razon_social !== $empresa->nombre_comercial)<div>{{ $empresa->razon_social }}</div>@endif
                @if($empresa?->ruc)<div>RUC: {{ $empresa->ruc }}</div>@endif
                @if($direccion)<div>{{ $direccion }}</div>@endif
                @if($empresa?->telefono)<div>Telf: {{ $empresa->telefono }}</div>@endif
                @if($empresa?->email)<div>{{ $empresa->email }}</div>@endif
            </div>
        </div>

        <div class="sep"></div>
        <div class="center bold">TICKET DE VENTA</div>
        <div class="center">N° {{ $ticket->numero_formateado }}</div>
        <div class="sep"></div>

        <div>Fecha: {{ $ticket->fecha?->format('d/m/Y H:i') }}</div>
        <div>Cliente: {{ $ticket->cliente_nombre ?: 'Cliente general' }}</div>
        @if($ticket->cliente_documento)<div>{{ $ticket->tipo_documento }}: {{ $ticket->cliente_documento }}</div>@endif
        @if($ticket->usuario)<div>Atendió: {{ $ticket->usuario->nombre }}</div>@endif

        <div class="sep"></div>
        <table>
            @foreach($ticket->items as $item)
                <tr><td colspan="2">{{ $item['descripcion'] }}</td></tr>
                <tr>
                    <td>{{ $ticket->cantidadTexto($item['cantidad']) }} x S/ {{ number_format($item['precio'], 2) }}</td>
                    <td class="right">S/ {{ number_format($item['importe'], 2) }}</td>
                </tr>
            @endforeach
        </table>
        <div class="sep"></div>

        <table>
            <tr class="bold total"><td>TOTAL</td><td class="right">S/ {{ number_format($ticket->total, 2) }}</td></tr>
        </table>
        <div style="margin-top:4px;font-size:10px">SON: {{ $ticket->total_letras }}</div>

        @if($ticket->estaAnulado())
            <div class="anulado">*** TICKET ANULADO ***</div>
        @endif

        <div class="sep"></div>
        <div class="center">¡Gracias por su compra!</div>
        <div class="center" style="font-size:10px;margin-top:4px">Este ticket no es un comprobante de pago electrónico</div>
    </div>

    <div class="acciones">
        <a href="{{ route('tickets.show', $ticket) }}" class="btn-back">Volver</a>
        <button type="button" class="btn-print" onclick="window.print()">Imprimir</button>
    </div>
</body>
</html>
