<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $comprobante->numero_completo }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
            padding: 20px;
        }
        
        .header {
            border: 2px solid #218786;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .header-grid {
            display: table;
            width: 100%;
        }
        
        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        
        .header-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: center;
            border-left: 2px solid #218786;
            padding-left: 15px;
        }
        
        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #218786;
            margin-bottom: 5px;
        }
        
        .ruc {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .comprobante-tipo {
            font-size: 14px;
            font-weight: bold;
            color: #218786;
            margin-bottom: 5px;
        }
        
        .comprobante-numero {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        
        .section-title {
            background-color: #218786;
            color: white;
            padding: 8px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 10px;
        }
        
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }
        
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        
        .label {
            font-weight: bold;
            width: 30%;
        }
        
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .details-table th {
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        
        .details-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .totals-table {
            width: 50%;
            margin-left: auto;
            margin-top: 15px;
        }
        
        .totals-table td {
            padding: 5px;
        }
        
        .total-final {
            font-size: 14px;
            font-weight: bold;
            background-color: #218786;
            color: white;
            padding: 8px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        
        .estado-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 11px;
        }
        
        .estado-aceptado {
            background-color: #d4edda;
            color: #155724;
        }
        
        .estado-rechazado {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    
    {{-- Header --}}
    <div class="header">
        <div class="header-grid">
            <div class="header-left">
                <div class="company-name">{{ strtoupper($empresa->nombre) }}</div>
                <div class="ruc">RUC: {{ $empresa->ruc }}</div>
                <div style="margin-top: 10px;">
                    <strong>Dirección:</strong> {{ $empresa->direccion }}<br>
                    @if($empresa->telefono)
                    <strong>Teléfono:</strong> {{ $empresa->telefono }}<br>
                    @endif
                    @if($empresa->email)
                    <strong>Email:</strong> {{ $empresa->email }}
                    @endif
                </div>
            </div>
            <div class="header-right">
                <div class="comprobante-tipo">
                    {{ $comprobante->tipo_comprobante == '01' ? 'FACTURA ELECTRÓNICA' : 'BOLETA DE VENTA ELECTRÓNICA' }}
                </div>
                <div class="comprobante-numero">{{ $comprobante->numero_completo }}</div>
            </div>
        </div>
    </div>
    
    {{-- Información del Cliente --}}
    <div class="section-title">DATOS DEL CLIENTE</div>
    <table class="info-table">
        <tr>
            <td class="label">{{ $comprobante->cliente_tipo_doc == '6' ? 'Razón Social' : 'Nombre' }}:</td>
            <td>{{ $comprobante->cliente_nombre }}</td>
        </tr>
        <tr>
            <td class="label">{{ $comprobante->cliente_tipo_doc == '6' ? 'RUC' : 'DNI' }}:</td>
            <td>{{ $comprobante->cliente_num_doc }}</td>
        </tr>
        <tr>
            <td class="label">Fecha de Emisión:</td>
            <td>{{ $comprobante->fecha_emision->format('d/m/Y H:i:s') }}</td>
        </tr>
        <tr>
            <td class="label">Moneda:</td>
            <td>{{ $comprobante->moneda }} - Soles</td>
        </tr>
    </table>
    
    {{-- Detalle de Productos/Servicios --}}
    <div class="section-title">DETALLE</div>
    <table class="details-table">
        <thead>
            <tr>
                <th width="10%" class="text-center">CANT.</th>
                <th width="15%">CÓDIGO</th>
                <th width="40%">DESCRIPCIÓN</th>
                <th width="15%" class="text-right">P. UNIT.</th>
                <th width="20%" class="text-right">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detalles as $detalle)
            <tr>
                <td class="text-center">{{ $detalle->cantidad }}</td>
                <td>{{ $detalle->repuesto->codigo ?? 'N/A' }}</td>
                <td>{{ $detalle->repuesto->nombre }}</td>
                <td class="text-right">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                <td class="text-right">S/ {{ number_format($detalle->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    {{-- Totales --}}
    <table class="totals-table">
        <tr>
            <td><strong>Subtotal (Base Imponible):</strong></td>
            <td class="text-right">S/ {{ number_format($comprobante->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td><strong>IGV (18%):</strong></td>
            <td class="text-right">S/ {{ number_format($comprobante->igv, 2) }}</td>
        </tr>
        <tr>
            <td class="total-final">TOTAL:</td>
            <td class="total-final text-right">S/ {{ number_format($comprobante->total, 2) }}</td>
        </tr>
    </table>
    
    {{-- Monto en Letras --}}
    <div style="margin-top: 15px; padding: 10px; background-color: #f9f9f9; border: 1px solid #ddd;">
        <strong>Son:</strong> {{ $comprobante->total_letras }}
    </div>
    
    {{-- Estado SUNAT --}}
    <div style="margin-top: 20px;">
        <strong>Estado SUNAT:</strong>
        <span class="estado-badge {{ $comprobante->estado_sunat == 'aceptado' ? 'estado-aceptado' : 'estado-rechazado' }}">
            {{ strtoupper($comprobante->estado_sunat) }}
        </span>
        
        @if($comprobante->codigo_sunat)
        <div style="margin-top: 5px;">
            <strong>Código:</strong> {{ $comprobante->codigo_sunat }}
        </div>
        @endif
        
        @if($comprobante->mensaje_sunat)
        <div style="margin-top: 5px; font-size: 9px;">
            <strong>Mensaje:</strong> {{ $comprobante->mensaje_sunat }}
        </div>
        @endif
    </div>
    
    {{-- Hash CDR --}}
    @if($comprobante->hash_cdr)
    <div style="margin-top: 10px; font-size: 9px;">
        <strong>Hash CDR:</strong> {{ $comprobante->hash_cdr }}
    </div>
    @endif
    
    {{-- Footer --}}
    <div class="footer">
        Representación impresa del {{ $comprobante->tipo_comprobante == '01' ? 'Comprobante de Pago Electrónico' : 'Comprobante Electrónico' }}<br>
        Generado en ambiente: <strong>{{ strtoupper($comprobante->ambiente) }}</strong><br>
        Autorizado mediante Resolución de Superintendencia N° 097-2012/SUNAT
    </div>
    
</body>
</html>
