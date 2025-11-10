<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleta #{{ $venta->id }}</title>
    <style>
        /* Estilos optimizados para impresora térmica 80mm (302px) */
        @page {
            margin: 0;
            size: 80mm auto;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            width: 80mm;
            margin: 0 auto;
            padding: 5mm;
            background: #fff;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
        }
        
        .negocio-nombre {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        
        .negocio-info {
            font-size: 10px;
            line-height: 1.3;
        }
        
        .boleta-numero {
            font-size: 14px;
            font-weight: bold;
            margin-top: 8px;
            padding: 5px;
            border: 1px solid #000;
            display: inline-block;
        }
        
        /* Información */
        .info-section {
            margin: 10px 0;
            font-size: 10px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .info-label {
            font-weight: bold;
        }
        
        /* Tabla de productos */
        .productos {
            margin: 10px 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 8px 0;
        }
        
        .productos-header {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 5px;
            padding-bottom: 3px;
            border-bottom: 1px solid #000;
        }
        
        .producto-item {
            margin-bottom: 8px;
            font-size: 10px;
        }
        
        .producto-nombre {
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .producto-detalle {
            display: flex;
            justify-content: space-between;
            padding-left: 5px;
        }
        
        /* Totales */
        .totales {
            margin-top: 10px;
            font-size: 11px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }
        
        .total-final {
            font-size: 14px;
            font-weight: bold;
            padding-top: 5px;
            border-top: 2px solid #000;
            margin-top: 5px;
        }
        
        /* Footer */
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 10px;
            border-top: 2px dashed #000;
            padding-top: 10px;
        }
        
        .footer-message {
            margin: 8px 0;
            font-style: italic;
        }
        
        .qr-code {
            margin: 10px auto;
            width: 100px;
            height: 100px;
        }
        
        /* Espaciado para corte */
        .corte {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
        }
        
        /* Helpers */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .mt-5 { margin-top: 5px; }
        .mb-5 { margin-bottom: 5px; }
        .bold { font-weight: bold; }
        
        /* Print styles */
        @media print {
            body {
                width: 80mm;
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="header">
        <div class="negocio-nombre">TALLER AUTOMOTRIZ</div>
        <div class="negocio-info">
            RUC: 20123456789<br>
            Av. Principal 123 - Lima, Perú<br>
            Telf: (01) 234-5678<br>
            Email: ventas@taller.com
        </div>
        <div class="boleta-numero mt-5">
            BOLETA DE VENTA<br>
            N° {{ str_pad($venta->id, 8, '0', STR_PAD_LEFT) }}
        </div>
    </div>

    <!-- INFORMACIÓN DE LA VENTA -->
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">FECHA:</span>
            <span>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</span>
        </div>
        
        @if($venta->cliente)
        <div class="info-row">
            <span class="info-label">CLIENTE:</span>
            <span>{{ $venta->cliente->nombre }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">DNI:</span>
            <span>{{ $venta->cliente->dni }}</span>
        </div>
        @if($venta->cliente->telefono)
        <div class="info-row">
            <span class="info-label">TELÉFONO:</span>
            <span>{{ $venta->cliente->telefono }}</span>
        </div>
        @endif
        @else
        <div class="info-row">
            <span class="info-label">CLIENTE:</span>
            <span>Cliente General</span>
        </div>
        @endif
        
        <div class="info-row">
            <span class="info-label">ESTADO:</span>
            <span style="text-transform: uppercase;">{{ $venta->estado }}</span>
        </div>
    </div>

    <!-- PRODUCTOS -->
    <div class="productos">
        <div class="productos-header">
            <span style="flex: 2;">PRODUCTO</span>
            <span style="flex: 1; text-align: center;">CANT</span>
            <span style="flex: 1; text-align: right;">P.UNIT</span>
            <span style="flex: 1; text-align: right;">SUBT</span>
        </div>
        
        @foreach($venta->detalles as $detalle)
        <div class="producto-item">
            <div class="producto-nombre">
                {{ strtoupper($detalle->repuesto->nombre) }}
            </div>
            <div class="producto-detalle">
                <span style="flex: 2; font-size: 9px;">
                    Código: {{ $detalle->repuesto->codigo }}
                </span>
                <span style="flex: 1; text-align: center;">
                    {{ $detalle->cantidad }}
                </span>
                <span style="flex: 1; text-align: right;">
                    {{ number_format($detalle->precio_unitario, 2) }}
                </span>
                <span style="flex: 1; text-align: right; font-weight: bold;">
                    {{ number_format($detalle->subtotal, 2) }}
                </span>
            </div>
        </div>
        @endforeach
    </div>

    <!-- RESUMEN -->
    <div class="totales">
        <div class="total-row">
            <span>CANTIDAD DE ITEMS:</span>
            <span class="bold">{{ $venta->detalles->count() }}</span>
        </div>
        <div class="total-row">
            <span>TOTAL UNIDADES:</span>
            <span class="bold">{{ $venta->detalles->sum('cantidad') }}</span>
        </div>
        
        @php
            $subtotal = $venta->total / 1.18;
            $igv = $venta->total - $subtotal;
        @endphp
        
        <div class="total-row mt-5">
            <span>SUBTOTAL:</span>
            <span>S/ {{ number_format($subtotal, 2) }}</span>
        </div>
        <div class="total-row">
            <span>IGV (18%):</span>
            <span>S/ {{ number_format($igv, 2) }}</span>
        </div>
        
        <div class="total-row total-final">
            <span>TOTAL A PAGAR:</span>
            <span>S/ {{ number_format($venta->total, 2) }}</span>
        </div>
    </div>

    <!-- MÉTODO DE PAGO -->
    <div class="info-section" style="border-top: 1px dashed #000; padding-top: 8px;">
        <div class="info-row">
            <span class="info-label">MÉTODO DE PAGO:</span>
            <span style="text-transform: uppercase;">EFECTIVO</span>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <div class="footer-message">
            ¡GRACIAS POR SU COMPRA!
        </div>
        <div style="font-size: 9px; line-height: 1.3;">
            Esta es una representación impresa de la<br>
            boleta electrónica. Consulte su documento<br>
            electrónico en www.taller.com
        </div>
        
        <!-- QR Code (opcional) -->
        {{-- 
        <div class="qr-code">
            <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code" style="width: 100%; height: 100%;">
        </div>
        --}}
        
        <div style="margin-top: 10px; font-size: 9px;">
            Sistema de Gestión de Inventario<br>
            Versión 1.0 - {{ date('Y') }}
        </div>
    </div>

    <!-- LÍNEA DE CORTE -->
    <div class="corte">
        ✂ - - - - - - - - - - - - - - - -
    </div>

    <!-- Botón para cerrar (solo en vista web) -->
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #218786; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; margin-right: 10px;">
            Imprimir
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
            Cerrar
        </button>
    </div>

    <script>
        // Auto-print al cargar (opcional, comentado por defecto)
        // window.onload = function() { window.print(); }
        
        // Cerrar ventana después de imprimir
        window.onafterprint = function() {
            // window.close();
        }
    </script>
</body>
</html>