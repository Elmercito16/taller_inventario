@extends('layouts.app')

@section('title', 'Registrar compra')
@section('page-title', 'Registrar compra')
@section('page-description', 'La mercadería ingresa al stock y queda registrada en el kardex')

@section('content')
@php
    $itemsPrevios = collect(old('items', []))->values();
@endphp

<form method="POST" action="{{ route('compras.store') }}" x-data="compraForm()" @submit="enviando = true" class="space-y-6">
    @csrf

    @if($errors->any())
        <div class="alert alert-danger">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <ul class="text-sm list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Datos del documento --}}
    <section class="card">
        <div class="card-header">
            <h2 class="card-title">Datos de la compra</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="lg:col-span-2">
                <label class="form-label">Proveedor</label>
                <div class="flex gap-2">
                    <select name="proveedor_id" class="form-input">
                        <option value="">Sin proveedor</option>
                        @foreach($proveedores as $proveedor)
                            <option value="{{ $proveedor->id }}" @selected(old('proveedor_id') == $proveedor->id)>{{ $proveedor->nombre }}</option>
                        @endforeach
                    </select>
                    <a href="{{ route('proveedores.create') }}" class="btn-secondary shrink-0" title="Nuevo proveedor" target="_blank">+</a>
                </div>
            </div>
            <div>
                <label class="form-label">Fecha <span class="text-red-500">*</span></label>
                <input type="date" name="fecha" value="{{ old('fecha', now()->toDateString()) }}" max="{{ now()->toDateString() }}" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Documento</label>
                <select name="tipo_documento" class="form-input">
                    @foreach($tiposDocumento as $valor => $nombre)
                        <option value="{{ $valor }}" @selected(old('tipo_documento', 'factura') === $valor)>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">N° de documento</label>
                <input type="text" name="numero_documento" value="{{ old('numero_documento') }}" maxlength="30" class="form-input uppercase" placeholder="F001-00012345">
            </div>
            <div class="md:col-span-1 lg:col-span-3">
                <label class="form-label">Observaciones</label>
                <input type="text" name="observaciones" value="{{ old('observaciones') }}" maxlength="1000" class="form-input" placeholder="Opcional">
            </div>
        </div>
    </section>

    {{-- Productos --}}
    <section class="card p-0 overflow-visible">
        <div class="p-6 pb-4 flex flex-col lg:flex-row lg:items-end gap-4 justify-between">
            <div>
                <h2 class="card-title">Productos comprados</h2>
                <p class="card-subtitle">Busca por nombre o escanea el código de barras</p>
            </div>

            <div class="relative w-full lg:w-96" @click.outside="sugerencias = []">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="busqueda" x-ref="buscador" @input="buscar()" @keydown.enter.prevent="agregarPorEnter()"
                       @keydown.arrow-down.prevent="moverSeleccion(1)" @keydown.arrow-up.prevent="moverSeleccion(-1)"
                       class="form-input pl-10" placeholder="Producto, código o código de barras" autocomplete="off">

                <div x-show="sugerencias.length" x-cloak class="absolute z-20 mt-1 w-full rounded-xl border border-gray-200 bg-white shadow-xl max-h-72 overflow-y-auto">
                    <template x-for="(p, i) in sugerencias" :key="p.id">
                        <button type="button" @click="agregar(p)"
                                class="w-full flex items-center justify-between gap-3 px-4 py-2.5 text-left text-sm hover:bg-primary-50"
                                :class="i === seleccion && 'bg-primary-50'">
                            <span class="min-w-0">
                                <span class="block font-medium text-gray-900 truncate" x-text="p.nombre"></span>
                                <span class="block text-xs text-gray-500" x-text="p.codigo + ' · stock ' + formatear(p.stock) + ' ' + p.unidad"></span>
                            </span>
                            <span class="text-xs text-gray-500 shrink-0" x-text="p.costo !== null ? 'S/ ' + p.costo.toFixed(2) : 'sin costo'"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th class="w-40">Cantidad</th>
                        <th class="w-40">Costo unit. (S/)</th>
                        <th class="text-right w-32">Subtotal</th>
                        <th class="w-12"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in items" :key="item.id">
                        <tr>
                            <td>
                                <p class="font-medium text-gray-900" x-text="item.nombre"></p>
                                <p class="text-xs text-gray-500">
                                    Stock actual: <span x-text="formatear(item.stock) + ' ' + item.unidad"></span>
                                    → <span class="font-semibold text-emerald-600" x-text="formatear(item.stock + (parseFloat(item.cantidad) || 0)) + ' ' + item.unidad"></span>
                                </p>
                                <input type="hidden" :name="'items[' + index + '][id]'" :value="item.id">
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <input type="number" :name="'items[' + index + '][cantidad]'" x-model.number="item.cantidad"
                                           :step="item.decimales ? '0.001' : '1'" :min="item.decimales ? '0.001' : '1'"
                                           class="form-input !py-2 text-center" required>
                                    <span class="text-xs text-gray-500" x-text="item.unidad"></span>
                                </div>
                            </td>
                            <td>
                                <input type="number" :name="'items[' + index + '][costo]'" x-model.number="item.costo"
                                       step="0.0001" min="0" class="form-input !py-2 text-right" required>
                                <p class="text-[11px] text-gray-400 mt-0.5" x-show="item.precio > 0">
                                    Precio venta: S/ <span x-text="item.precio.toFixed(2)"></span>
                                    <span x-show="item.costo > 0" :class="margen(item) < 0 ? 'text-red-600' : 'text-emerald-600'"
                                          x-text="'· margen ' + margen(item).toFixed(0) + '%'"></span>
                                </p>
                            </td>
                            <td class="text-right font-semibold text-gray-900" x-text="'S/ ' + subtotal(item).toFixed(2)"></td>
                            <td class="text-center">
                                <button type="button" @click="quitar(index)" class="btn-icon hover:!text-red-600 hover:!bg-red-50" title="Quitar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="!items.length">
                        <td colspan="5">
                            <div class="empty-state">
                                <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                Busca un producto arriba para agregarlo a la compra.
                                <a href="{{ route('repuestos.create') }}" class="link block mt-1" target="_blank">¿Producto nuevo? Regístralo aquí</a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="p-6 border-t border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="hidden" name="actualizar_costo" value="0">
                <input type="checkbox" name="actualizar_costo" value="1" checked class="mt-0.5 w-5 h-5 rounded text-primary-600 focus:ring-primary-500 border-gray-300">
                <span class="text-sm">
                    <span class="font-semibold text-gray-900">Actualizar el precio de compra de los productos</span>
                    <span class="block text-xs text-gray-500">Se usa para calcular la ganancia en Reportes.</span>
                </span>
            </label>

            <div class="flex items-center gap-6">
                <div class="text-right">
                    <p class="text-sm text-gray-500"><span x-text="items.length"></span> producto(s)</p>
                    <p class="text-2xl font-bold text-gray-900">S/ <span x-text="total().toFixed(2)"></span></p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('compras.index') }}" class="btn-secondary">Cancelar</a>
                    <button type="submit" class="btn-primary" :disabled="!items.length || enviando">
                        <span x-text="enviando ? 'Guardando...' : 'Registrar compra'"></span>
                    </button>
                </div>
            </div>
        </div>
    </section>
</form>
@endsection

@push('scripts')
<script>
    function compraForm() {
        const catalogo = @json($productos);
        const previos = @json($itemsPrevios);

        return {
            busqueda: '',
            sugerencias: [],
            seleccion: 0,
            enviando: false,
            // Si la validación falló, se restauran los ítems ingresados
            items: previos.map(i => {
                const p = catalogo.find(x => x.id === parseInt(i.id));
                return p ? { ...p, cantidad: parseFloat(i.cantidad), costo: parseFloat(i.costo) } : null;
            }).filter(Boolean),

            buscar() {
                const q = this.busqueda.trim().toLowerCase();
                this.seleccion = 0;
                this.sugerencias = q.length < 1 ? [] : catalogo.filter(p =>
                    p.nombre.toLowerCase().includes(q) || p.codigo.toLowerCase().includes(q) || (p.barras && p.barras.toLowerCase() === q)
                ).slice(0, 12);
            },

            moverSeleccion(paso) {
                if (!this.sugerencias.length) return;
                this.seleccion = (this.seleccion + paso + this.sugerencias.length) % this.sugerencias.length;
            },

            agregarPorEnter() {
                const q = this.busqueda.trim().toLowerCase();
                const exacto = catalogo.find(p => (p.barras && p.barras.toLowerCase() === q) || p.codigo.toLowerCase() === q);
                const producto = exacto || this.sugerencias[this.seleccion];
                if (producto) this.agregar(producto);
            },

            agregar(producto) {
                const existente = this.items.find(i => i.id === producto.id);
                if (existente) {
                    existente.cantidad = (parseFloat(existente.cantidad) || 0) + 1;
                } else {
                    this.items.push({ ...producto, cantidad: 1, costo: producto.costo ?? 0 });
                }
                this.busqueda = '';
                this.sugerencias = [];
                this.$nextTick(() => this.$refs.buscador.focus());
            },

            quitar(index) {
                this.items.splice(index, 1);
            },

            subtotal(item) {
                return (parseFloat(item.cantidad) || 0) * (parseFloat(item.costo) || 0);
            },

            total() {
                return this.items.reduce((suma, item) => suma + this.subtotal(item), 0);
            },

            margen(item) {
                const costo = parseFloat(item.costo) || 0;
                return item.precio > 0 ? ((item.precio - costo) / item.precio) * 100 : 0;
            },

            formatear(valor) {
                return +(parseFloat(valor) || 0).toFixed(3);
            },
        };
    }
</script>
@endpush
