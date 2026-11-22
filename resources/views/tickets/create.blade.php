@extends('layouts.app')

@section('title', 'Nuevo ticket')
@section('page-title', 'Ticket')
@section('page-description', 'Venta rápida escribiendo cada ítem · No descuenta stock del inventario')

@section('page-actions')
    <a href="{{ route('tickets.index') }}" class="btn-secondary">Tickets emitidos</a>
@endsection

@php
    $itemsPrevios = collect(old('items', [['descripcion' => '', 'cantidad' => 1, 'precio' => '']]))->values();
@endphp

@section('content')
<form method="POST" action="{{ route('tickets.store') }}" x-data="ticketForm()" @submit="enviando = true" class="max-w-2xl mx-auto pb-32">
    @csrf

    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <p class="text-sm text-gray-500 mb-4">
        Escribe cada ítem con su cantidad y precio. Si el DNI o RUC ya se usó antes, el nombre se completa solo, aunque no haya internet.
    </p>

    {{-- Cliente --}}
    <div class="card !p-4 sm:!p-5 space-y-3">
        <div class="relative">
            <label class="form-label" for="doc">DNI o RUC</label>
            <div class="relative">
                <input id="doc" type="text" name="cliente_documento" x-model="documento" inputmode="numeric" maxlength="11" autocomplete="off"
                       @input="documento = documento.replace(/\D/g, '').slice(0, 11); autoBuscar()" @keydown.enter.prevent="buscar()"
                       class="form-input !py-3 pr-12 text-base font-mono" placeholder="Opcional">
                <button type="button" @click="buscar()" :disabled="buscando" title="Buscar"
                        class="absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-500 hover:text-primary-700">
                    <svg x-show="!buscando" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <svg x-show="buscando" x-cloak class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/><path fill="currentColor" class="opacity-75" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                </button>
            </div>
            <p class="form-help" x-show="mensaje" x-text="mensaje" :class="encontrado ? '!text-emerald-600' : ''"></p>
        </div>
        <div>
            <label class="form-label" for="nombre">Nombre del cliente</label>
            <input id="nombre" type="text" name="cliente_nombre" x-model="nombre" maxlength="150" class="form-input !py-3 text-base" placeholder="Opcional · Cliente general">
        </div>
    </div>

    {{-- Ítems --}}
    <h2 class="mt-6 mb-2 text-sm font-semibold text-primary-700">Ítems</h2>
    <div class="space-y-3">
        <template x-for="(item, i) in items" :key="item.uid">
            <div class="card !p-4 sm:!p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-semibold text-gray-900" x-text="'Ítem ' + (i + 1)"></p>
                    <button type="button" x-show="items.length > 1" @click="quitar(i)" class="btn-icon-danger !w-7 !h-7" title="Quitar ítem">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="form-label !text-xs">Descripción</label>
                        <input type="text" :name="'items[' + i + '][descripcion]'" x-model="item.descripcion" maxlength="200" required
                               class="form-input !py-3 text-base" placeholder="Ej.: Cambio de aceite">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label !text-xs">Cantidad</label>
                            <input type="number" :name="'items[' + i + '][cantidad]'" x-model="item.cantidad" step="any" min="0.001" required inputmode="decimal"
                                   class="form-input !py-3 text-base tabular-nums">
                        </div>
                        <div>
                            <label class="form-label !text-xs">Precio c/u</label>
                            <div class="input-addon">
                                <span>S/</span>
                                <input type="number" :name="'items[' + i + '][precio]'" x-model="item.precio" step="0.01" min="0" required inputmode="decimal"
                                       class="form-input !py-3 text-base tabular-nums" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <p class="text-right text-sm font-semibold text-gray-900 tabular-nums">Importe: S/ <span x-text="importe(item).toFixed(2)"></span></p>
                </div>
            </div>
        </template>
    </div>

    <button type="button" @click="agregar()" class="mt-3 w-full rounded-xl border-2 border-dashed border-gray-300 py-3 text-sm font-medium text-primary-700 hover:border-primary-400 hover:bg-primary-50/40 transition">
        + Agregar ítem
    </button>

    {{-- Total y Vender (fijo abajo) --}}
    <div class="fixed bottom-0 inset-x-0 lg:left-64 z-20 border-t border-gray-200 bg-white/95 backdrop-blur">
        <div class="max-w-2xl mx-auto px-4 py-3 space-y-2">
            <div class="flex items-baseline justify-between">
                <span class="text-lg font-semibold text-gray-900">Total</span>
                <span class="text-2xl font-semibold text-gray-900 tabular-nums">S/ <span x-text="total().toFixed(2)"></span></span>
            </div>
            <button type="submit" class="btn-primary btn-lg w-full !py-3" :disabled="!valido() || enviando">
                <span x-text="enviando ? 'Registrando…' : 'Vender'"></span>
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    function ticketForm() {
        let uid = 0;
        const previos = @json($itemsPrevios);
        const rutaBuscar = @js(route('tickets.buscar-cliente'));

        return {
            documento: @js(old('cliente_documento', '')),
            nombre: @js(old('cliente_nombre', '')),
            items: previos.map(i => ({ uid: ++uid, descripcion: i.descripcion || '', cantidad: i.cantidad ?? 1, precio: i.precio ?? '' })),
            buscando: false,
            mensaje: '',
            encontrado: false,
            enviando: false,
            ultimoBuscado: '',

            importe(item) { return (parseFloat(item.cantidad) || 0) * (parseFloat(item.precio) || 0); },
            total() { return this.items.reduce((s, i) => s + this.importe(i), 0); },
            valido() { return this.items.length && this.items.every(i => i.descripcion.trim() && parseFloat(i.cantidad) > 0 && i.precio !== '' && parseFloat(i.precio) >= 0); },
            agregar() {
                this.items.push({ uid: ++uid, descripcion: '', cantidad: 1, precio: '' });
                this.$nextTick(() => {
                    const campos = this.$root.querySelectorAll('input[name$="[descripcion]"]');
                    campos[campos.length - 1]?.focus();
                });
            },
            quitar(i) { this.items.splice(i, 1); },

            autoBuscar() {
                if ((this.documento.length === 8 || this.documento.length === 11) && this.documento !== this.ultimoBuscado) this.buscar();
            },
            async buscar() {
                if (![8, 11].includes(this.documento.length)) {
                    this.mensaje = 'Ingresa un DNI (8 dígitos) o RUC (11 dígitos).';
                    this.encontrado = false;
                    return;
                }
                this.buscando = true;
                this.ultimoBuscado = this.documento;
                try {
                    const res = await fetch(rutaBuscar + '?doc=' + this.documento, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    this.encontrado = !!data.encontrado;
                    if (data.encontrado) {
                        this.nombre = data.nombre;
                        this.mensaje = '✓ Cliente encontrado';
                    } else {
                        this.mensaje = data.mensaje || 'No encontrado. Escribe el nombre.';
                    }
                } catch (e) {
                    this.encontrado = false;
                    this.mensaje = 'Sin conexión. Escribe el nombre.';
                } finally {
                    this.buscando = false;
                }
            },
        };
    }
</script>
@endpush
