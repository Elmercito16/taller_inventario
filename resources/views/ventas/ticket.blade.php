<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Courier New', monospace; font-size: 12px; color: #111; background: #f3f4f6; }
        .ticket { width: 80mm; margin: 16px auto; background: #fff; padding: 12px 10px; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
        .center { text-align: center; }
        .bold { font-weight: 700; }
        .sep { border-top: 1px dashed #555; margin: 8px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; vertical-align: top; }
        .right { text-align: right; }
        .total { font-size: 15px; }
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
    <div class="ticket">
        <div class="center">
            <div class="bold" style="font-size:14px">{{ strtoupper($venta->empresa->nombre_comercial ?? $venta->empresa->nombre ?? 'MI EMPRESA') }}</div>
            @if($venta->empresa?->ruc)<div>RUC: {{ $venta->empresa->ruc }}</div>@endif
            @if($venta->empresa?->direccion)<div>{{ $venta->empresa->direccion }}</div>@endif
            @if($venta->empresa?->telefono)<div>Telf: {{ $venta->empresa->telefono }}</div>@endif
        </div>

        <div class="sep"></div>
        <div class="center bold">TICKET DE VENTA</div>
        <div class="center">N° {{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</div>
        <div class="sep"></div>

        <div>Fecha: {{ $venta->fecha?->format('d/m/Y H:i') }}</div>
        <div>Cliente: {{ $venta->cliente->nombre ?? 'Cliente general' }}</div>
        @if($venta->cliente?->dni)<div>DNI: {{ $venta->cliente->dni }}</div>@endif
        <div>Pago: {{ $venta->metodo_pago_nombre }}</div>
        @if($venta->usuario)<div>Atendió: {{ $venta->usuario->nombre }}</div>@endif

        <div class="sep"></div>
        <table>
            @foreach($venta->detalles as $detalle)
                <tr><td colspan="2">{{ $detalle->repuesto->nombre ?? 'Producto eliminado' }}</td></tr>
                <tr>
                    <td>{{ $detalle->cantidad_formateada }} {{ \App\Support\Catalogos::abreviaturaUnidad($detalle->unidad_medida) }} x S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td class="right">S/ {{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </table>
        <div class="sep"></div>

        <table>
            <tr><td>Op. gravada</td><td class="right">S/ {{ number_format($venta->calcularBaseImponible(), 2) }}</td></tr>
            @if($venta->calcularNoGravado() > 0)
                <tr><td>Op. exon./inaf.</td><td class="right">S/ {{ number_format($venta->calcularNoGravado(), 2) }}</td></tr>
            @endif
            <tr><td>IGV (18%)</td><td class="right">S/ {{ number_format($venta->calcularIgv(), 2) }}</td></tr>
            <tr class="bold total"><td>TOTAL</td><td class="right">S/ {{ number_format($venta->total, 2) }}</td></tr>
        </table>

        @if($venta->estado === 'anulado')
            <div class="sep"></div>
            <div class="center bold">*** VENTA ANULADA ***</div>
        @endif

        <div class="sep"></div>
        <div class="center">¡Gracias por su compra!</div>
        <div class="center" style="font-size:10px;margin-top:4px">Este ticket no es un comprobante de pago electrónico</div>
    </div>

    <div class="acciones">
        <a href="{{ route('ventas.show', $venta) }}" class="btn-back">Volver</a>
        <button type="button" class="btn-print" onclick="window.print()">Imprimir</button>
    </div>
</body>
</html>
