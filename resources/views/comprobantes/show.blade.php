@extends('layouts.app')

@section('title', 'Comprobante ' . $comprobante->numero_completo)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="container mx-auto px-4">
        
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-800 flex items-center gap-3">
                        @if($comprobante->tipo_comprobante == '01')
                            <span class="text-5xl">📄</span>
                        @else
                            <span class="text-5xl">🧾</span>
                        @endif
                        Comprobante {{ $comprobante->numero_completo }}
                    </h1>
                    <p class="text-gray-600 mt-2 ml-16">{{ $comprobante->tipo_comprobante_nombre }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('comprobantes.index') }}" 
                       class="px-6 py-3 bg-gray-600 text-white font-bold rounded-xl hover:bg-gray-700 transition-all shadow-lg">
                        ← Volver
                    </a>
                    <a href="{{ route('ventas.show', $comprobante->venta) }}" 
                       class="px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg">
                        👁️ Ver Venta
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Columna Principal --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Información del Comprobante --}}
                <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                    <div class="bg-gradient-to-r from-[#218786] to-[#1a6d6c] px-8 py-6">
                        <h2 class="text-2xl font-bold text-white">📋 Información del Comprobante</h2>
                    </div>
                    
                    <div class="p-8">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="text-sm font-bold text-gray-600">Número</label>
                                <p class="text-xl font-mono font-bold text-[#218786]">{{ $comprobante->numero_completo }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-bold text-gray-600">Tipo</label>
                                <p class="text-lg font-bold">{{ $comprobante->tipo_comprobante_nombre }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-bold text-gray-600">Fecha Emisión</label>
                                <p class="text-lg">{{ $comprobante->fecha_emision->format('d/m/Y H:i') }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-bold text-gray-600">Moneda</label>
                                <p class="text-lg">{{ $comprobante->moneda }} - Soles</p>
                            </div>
                            
                            <div class="col-span-2">
                                <label class="text-sm font-bold text-gray-600">Cliente</label>
                                <p class="text-lg font-bold">{{ $comprobante->cliente_nombre }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ $comprobante->cliente_tipo_doc == '6' ? 'RUC' : 'DNI' }}: {{ $comprobante->cliente_num_doc }}
                                </p>
                            </div>
                            
                            <div class="col-span-2">
                                <label class="text-sm font-bold text-gray-600">Monto en Letras</label>
                                <p class="text-sm italic text-gray-700">{{ $comprobante->total_letras }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Detalles de la Venta --}}
                <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                    <div class="bg-gradient-to-r from-[#218786] to-[#1a6d6c] px-8 py-6">
                        <h2 class="text-2xl font-bold text-white">🛒 Detalles</h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Producto</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase">Cant.</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase">P. Unit.</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($comprobante->venta->detalles as $detalle)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $detalle->repuesto->nombre }}</div>
                                        <div class="text-sm text-gray-500">Código: {{ $detalle->repuesto->codigo ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold">{{ $detalle->cantidad }}</td>
                                    <td class="px-6 py-4 text-right">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                                    <td class="px-6 py-4 text-right font-bold">S/ {{ number_format($detalle->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-6 py-3 text-right font-bold">Subtotal (Base Imponible):</td>
                                    <td class="px-6 py-3 text-right font-bold">S/ {{ number_format($comprobante->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-6 py-3 text-right font-bold">IGV (18%):</td>
                                    <td class="px-6 py-3 text-right font-bold">S/ {{ number_format($comprobante->igv, 2) }}</td>
                                </tr>
                                <tr class="bg-[#218786] text-white">
                                    <td colspan="3" class="px-6 py-4 text-right text-lg font-bold">TOTAL:</td>
                                    <td class="px-6 py-4 text-right text-2xl font-bold">S/ {{ number_format($comprobante->total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                
            </div>
            
            {{-- Columna Lateral --}}
            <div class="space-y-6">
                
                {{-- Estado SUNAT --}}
                <div class="bg-white rounded-2xl shadow-2xl p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">📡 Estado SUNAT</h3>
                    
                    <div class="text-center mb-4">
                        @if($comprobante->estado_sunat == 'aceptado' && !$comprobante->anulado)
                            <div class="text-6xl mb-2">✅</div>
                            <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full font-bold">
                                Aceptado por SUNAT
                            </span>
                        @elseif($comprobante->estado_sunat == 'pendiente')
                            <div class="text-6xl mb-2">⏳</div>
                            <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full font-bold">
                                Pendiente de Envío
                            </span>
                        @elseif($comprobante->estado_sunat == 'rechazado')
                            <div class="text-6xl mb-2">❌</div>
                            <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full font-bold">
                                Rechazado
                            </span>
                        @elseif($comprobante->anulado)
                            <div class="text-6xl mb-2">🚫</div>
                            <span class="px-4 py-2 bg-gray-100 text-gray-800 rounded-full font-bold">
                                Anulado
                            </span>
                        @endif
                    </div>
                    
                    @if($comprobante->mensaje_sunat)
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs font-bold text-gray-600 mb-1">Mensaje:</p>
                        <p class="text-sm text-gray-700">{{ $comprobante->mensaje_sunat }}</p>
                    </div>
                    @endif
                    
                    @if($comprobante->codigo_sunat)
                    <div class="mt-2 p-3 bg-blue-50 rounded-lg">
                        <p class="text-xs font-bold text-gray-600 mb-1">Código SUNAT:</p>
                        <p class="text-sm font-mono text-gray-700">{{ $comprobante->codigo_sunat }}</p>
                    </div>
                    @endif
                </div>
                
                {{-- Acciones --}}
<div class="bg-white rounded-2xl shadow-2xl p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">⚡ Acciones</h3>
    
    <div class="space-y-2">
        @if($comprobante->xml_path)
        <a href="{{ route('comprobantes.descargar-xml', $comprobante) }}" 
           class="w-full px-4 py-3 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition-all flex items-center justify-center gap-2">
            📄 Descargar XML
        </a>
        @endif
        
        @if($comprobante->cdr_path)
        <a href="{{ route('comprobantes.descargar-cdr', $comprobante) }}" 
           class="w-full px-4 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all flex items-center justify-center gap-2">
            📦 Descargar CDR
        </a>
        @endif
        
        {{-- AGREGA ESTO 👇 --}}
        @if($comprobante->pdf_path)
        <a href="{{ route('comprobantes.descargar-pdf', $comprobante) }}" 
           class="w-full px-4 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition-all flex items-center justify-center gap-2">
            📑 Descargar PDF
        </a>
        @else
        <button disabled
                class="w-full px-4 py-3 bg-gray-300 text-gray-500 font-bold rounded-xl cursor-not-allowed flex items-center justify-center gap-2"
                title="PDF no disponible aún">
            📑 PDF (Próximamente)
        </button>
        @endif
        
        @if($comprobante->puedeSerAnulado())
        <button onclick="confirmarAnulacion({{ $comprobante->id }})"
                class="w-full px-4 py-3 bg-gray-600 text-white font-bold rounded-xl hover:bg-gray-700 transition-all flex items-center justify-center gap-2">
            🚫 Anular Comprobante
        </button>
        @endif
    </div>
</div>

                
                {{-- Información Adicional --}}
                <div class="bg-white rounded-2xl shadow-2xl p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">ℹ️ Información Adicional</h3>
                    
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="font-bold text-gray-600">Ambiente:</span>
                            <span class="ml-2 px-2 py-1 bg-gray-100 rounded text-xs">
                                {{ $comprobante->ambiente == 'beta' ? '🧪 Pruebas (Beta)' : '🚀 Producción' }}
                            </span>
                        </div>
                        
                        @if($comprobante->usuarioEmision)
                        <div>
                            <span class="font-bold text-gray-600">Emitido por:</span>
                            <span class="ml-2">{{ $comprobante->usuarioEmision->nombre }}</span>
                        </div>
                        @endif
                        
                        <div>
                            <span class="font-bold text-gray-600">Fecha envío SUNAT:</span>
                            <span class="ml-2">{{ $comprobante->fecha_envio_sunat ? $comprobante->fecha_envio_sunat->format('d/m/Y H:i:s') : '-' }}</span>
                        </div>
                        
                        @if($comprobante->anulado)
                        <div class="pt-3 border-t">
                            <span class="font-bold text-red-600">Anulado el:</span>
                            <span class="ml-2">{{ $comprobante->fecha_anulacion->format('d/m/Y H:i') }}</span>
                            
                            @if($comprobante->motivo_anulacion)
                            <p class="mt-2 text-xs text-gray-600">
                                <strong>Motivo:</strong> {{ $comprobante->motivo_anulacion }}
                            </p>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                
            </div>
            
        </div>
        
    </div>
</div>

{{-- Script para anular --}}
@if($comprobante->puedeSerAnulado())
<script>
function confirmarAnulacion(comprobanteId) {
    const motivo = prompt('Ingresa el motivo de anulación (mínimo 10 caracteres):');
    
    if (motivo && motivo.length >= 10) {
        if (confirm('¿Estás seguro de anular este comprobante? Esta acción no se puede deshacer.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/comprobantes/${comprobanteId}/anular`;
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            
            form.innerHTML = `
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="motivo_anulacion" value="${motivo}">
            `;
            
            document.body.appendChild(form);
            form.submit();
        }
    } else if (motivo !== null) {
        alert('El motivo debe tener al menos 10 caracteres');
    }
}
</script>
@endif
@endsection
