{{--
    Datos comerciales del producto: tipo, unidad de medida, código de barras y afectación IGV.
    Variables: $unidades, $afectaciones, $tiposItem y opcionalmente $repuesto (edición).
--}}
@php
    $tipoActual = old('tipo', $repuesto->tipo ?? 'producto');
    $unidadActual = old('unidad_medida', $repuesto->unidad_medida ?? 'NIU');
    $afectacionActual = old('afectacion_igv', $repuesto->afectacion_igv ?? '10');
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 form-section">
    <div class="flex items-center mb-6">
        <div class="w-10 h-10 bg-sky-100 rounded-lg flex items-center justify-center mr-4">
            <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
            </svg>
        </div>
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Tipo, unidad y tributos</h2>
            <p class="text-sm text-gray-600">Cómo se vende y cómo se factura</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div>
            <label for="campo-tipo" class="block text-sm font-medium text-gray-700 mb-2">Tipo <span class="text-red-500">*</span></label>
            <select id="campo-tipo" name="tipo" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                @foreach($tiposItem as $valor => $nombre)
                    <option value="{{ $valor }}" @selected($tipoActual === $valor)>{{ $nombre }}</option>
                @endforeach
            </select>
            @error('tipo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="campo-unidad" class="block text-sm font-medium text-gray-700 mb-2">Unidad de medida <span class="text-red-500">*</span></label>
            <select id="campo-unidad" name="unidad_medida" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                @foreach($unidades as $codigo => [$nombre, $abreviatura, $decimales])
                    <option value="{{ $codigo }}" data-decimales="{{ $decimales ? 1 : 0 }}" @selected($unidadActual === $codigo)>
                        {{ $nombre }} ({{ $abreviatura }})
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500" id="ayuda-unidad"></p>
            @error('unidad_medida')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="campo-barras" class="block text-sm font-medium text-gray-700 mb-2">Código de barras</label>
            <input type="text" id="campo-barras" name="codigo_barras" maxlength="50"
                   value="{{ old('codigo_barras', $repuesto->codigo_barras ?? '') }}"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                   placeholder="Escanéalo o escríbelo" autocomplete="off">
            <p class="mt-1 text-xs text-gray-500">Opcional. Permite vender escaneando.</p>
            @error('codigo_barras')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="campo-afectacion" class="block text-sm font-medium text-gray-700 mb-2">IGV <span class="text-red-500">*</span></label>
            <select id="campo-afectacion" name="afectacion_igv" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                @foreach($afectaciones as $codigo => $nombre)
                    <option value="{{ $codigo }}" @selected($afectacionActual === $codigo)>{{ $nombre }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">La mayoría de productos son "Gravado".</p>
            @error('afectacion_igv')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const tipo = document.getElementById('campo-tipo');
    const unidad = document.getElementById('campo-unidad');
    const ayuda = document.getElementById('ayuda-unidad');
    // El formulario debe marcar con la clase "solo-producto" los campos de stock
    const camposStock = document.querySelectorAll('.solo-producto');
    const inputsCantidad = document.querySelectorAll('input[name="cantidad"], input[name="minimo_stock"]');

    function actualizar() {
        const esServicio = tipo.value === 'servicio';

        camposStock.forEach(el => el.classList.toggle('hidden', esServicio));
        inputsCantidad.forEach(input => {
            input.required = !esServicio && input.name === 'cantidad';
            if (esServicio) input.value = 0;
        });

        // Un servicio se factura normalmente como "ZZ"
        if (esServicio && unidad.value === 'NIU') unidad.value = 'ZZ';
        if (!esServicio && unidad.value === 'ZZ') unidad.value = 'NIU';

        const opcion = unidad.selectedOptions[0];
        const decimales = opcion && opcion.dataset.decimales === '1';
        inputsCantidad.forEach(input => input.step = decimales ? '0.001' : '1');
        ayuda.textContent = esServicio ? 'Los servicios no controlan stock.'
            : (decimales ? 'Admite cantidades con decimales (ej. 1.5).' : 'Solo cantidades enteras.');
    }

    tipo.addEventListener('change', actualizar);
    unidad.addEventListener('change', actualizar);
    actualizar();
});
</script>
@endpush
