<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleta #{{ str_pad($venta->id, 8, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            margin: 0;
            size: 80mm auto;
        }
        
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px; /* Reduje un poco la fuente para que quepa mejor */
            margin: 0 auto;
            padding: 5mm 2mm; /* Menos padding lateral para ganar espacio */
            width: 100%;
            max-width: 78mm;
            color: #000;
            background: #fff;
            line-height: 1.2;
        }

        /* Utilidades */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .dashed-line { border-bottom: 1px dashed #000; margin: 5px 0; display: block; }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .negocio-nombre {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        /* Tablas de Información (Cliente, etc) */
        .info-table {
            width: 100%;
            margin-bottom: 5px;
        }
        
        .info-table td {
            vertical-align: top;
            padding-bottom: 2px;
        }
        
        .label {
            width: 60px;
            font-weight: bold;
        }

        /* Tabla de Productos - DISEÑO MEJORADO */
        .productos-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        
        .productos-table th {
            text-align: left;
            border-bottom: 1px dashed #000;
            padding-bottom: 4px;
            font-size: 9px;
        }
        
        .productos-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        /* Anchos de columnas ajustados */
        .col-cant { width: 10%; text-align: center; }
        .col-desc { width: 50%; text-align: left; padding-right: 2px; }
        .col-pu   { width: 20%; text-align: right; }
        .col-total{ width: 20%; text-align: right; }

        /* Totales - ALINEACIÓN PERFECTA A LA DERECHA */
        .totales-table {
            width: 100%;
            margin-top: 5px;
        }
        
        .totales-table td {
            text-align: right;
            padding-bottom: 2px;
        }
        
        .total-label {
            width: 60%; /* Empuja los valores a la derecha */
            font-weight: bold;
        }
        
        .total-value {
            width: 40%;
        }
        
        /* Footer */
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 9px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0; width: 80mm; }
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <div class="negocio-nombre uppercase">{{ $venta->empresa->nombre ?? 'MI EMPRESA' }}</div>
        
        <div style="font-size: 9px;">
            @if($venta->empresa->ruc) RUC: {{ $venta->empresa->ruc }}<br> @endif
            @if($venta->empresa->direccion) {{ Str::limit($venta->empresa->direccion, 35) }}<br> @endif
            @if($venta->empresa->telefono) Telf: {{ $venta->empresa->telefono }}<br> @endif
            @if($venta->empresa->email) {{ $venta->empresa->email }} @endif
        </div>

        <div style="margin-top: 8px; font-weight: bold; font-size: 11px;">
            BOLETA DE VENTA ELECTRÓNICA<br>
            N° {{ str_pad($venta->id, 8, '0', STR_PAD_LEFT) }}
        </div>
    </div>

    <div class="dashed-line"></div>

    <!-- DATOS DEL CLIENTE -->
    <table class="info-table">
        <tr>
            <td class="label">FECHA:</td>
            <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="label">CLIENTE:</td>
            <td class="uppercase">{{ $venta->cliente ? Str::limit($venta->cliente->nombre, 30) : 'CLIENTE GENERAL' }}</td>
        </tr>
        @if($venta->cliente && $venta->cliente->dni)
        <tr>
            <td class="label">DNI/RUC:</td>
            <td>{{ $venta->cliente->dni }}</td>
        </tr>
        @endif
        @if($venta->cliente && $venta->cliente->direccion)
        <tr>
            <td class="label">DIR:</td>
            <td class="uppercase">{{ Str::limit($venta->cliente->direccion, 30) }}</td>
        </tr>
        @endif
    </table>

    <div class="dashed-line"></div>

    <!-- PRODUCTOS -->
    <table class="productos-table">
        <thead>
            <tr>
                <th class="col-cant">CNT</th>
                <th class="col-desc">DESCRIPCIÓN</th>
                <th class="col-pu">P.U.</th>
                <th class="col-total">IMPORTE</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $detalle)
            <tr>
                <td class="col-cant">{{ $detalle->cantidad }}</td>
                <td class="col-desc">
                    {{ strtoupper($detalle->repuesto->nombre) }}
                    @if($detalle->repuesto->marca)
                        <br><span style="font-size: 8px; color: #333;">({{ $detalle->repuesto->marca }})</span>
                    @endif
                </td>
                <td class="col-pu">{{ number_format($detalle->precio_unitario, 2) }}</td>
                <td class="col-total">{{ number_format($detalle->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="dashed-line"></div>

    <!-- TOTALES (Alineados a la derecha) -->
    <table class="totales-table">
        @php
            $op_gravada = $venta->total / 1.18;
            $igv = $venta->total - $op_gravada;
        @endphp
        <tr>
            <td class="total-label">OP. GRAVADA:</td>
            <td class="total-value">S/ {{ number_format($op_gravada, 2) }}</td>
        </tr>
        <tr>
            <td class="total-label">I.G.V. (18%):</td>
            <td class="total-value">S/ {{ number_format($igv, 2) }}</td>
        </tr>
        <tr>
            <td class="total-label" style="font-size: 12px; padding-top: 5px;">TOTAL A PAGAR:</td>
            <td class="total-value" style="font-size: 12px; padding-top: 5px; font-weight: bold;">S/ {{ number_format($venta->total, 2) }}</td>
        </tr>
    </table>
    
    <!-- MÉTODO DE PAGO -->
    <div style="margin-top: 5px; font-size: 9px; text-align: right;">
        MÉTODO DE PAGO: EFECTIVO
    </div>

    <div class="dashed-line"></div>

    <!-- FOOTER -->
    <div class="footer">
        <p>Representación impresa de la Boleta Electrónica</p>
        <p class="bold" style="margin-top: 5px;">¡GRACIAS POR SU PREFERENCIA!</p>
        <br>
        <p style="font-size: 8px;">Software: MultiSoft Inventario</p>
    </div>
    
    <script>
        window.onload = function() {
            try {
                window.print();
            } catch (e) {}
        }
    </script>
</body>
</html>