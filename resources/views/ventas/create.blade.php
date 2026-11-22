@extends('layouts.app')

@section('title', 'Punto de venta')
@section('page-title', 'Punto de venta')
@section('page-description', 'Busca o escanea productos y cobra en segundos · F2 para buscar')

@section('page-actions')
    <a href="{{ route('ventas.index') }}" class="btn-secondary">Historial de ventas</a>
@endsection

@php
    $catalogo = $repuestos->map(fn ($r) => [
        'id' => $r->id,
        'nombre' => $r->nombre,
        'codigo' => (string) $r->codigo,
        'barras' => (string) ($r->codigo_barras ?? ''),
        'precio' => (float) $r->precio_unitario,
        'stock' => (float) $r->cantidad,
        'servicio' => $r->esServicio(),
        'unidad' => $r->unidad_abreviatura,
        'decimales' => $r->permiteDecimales(),
        'categoria_id' => $r->categoria_id,
        'imagen' => $r->imagen ? asset('storage/' . $r->imagen) : null,
    ])->values();
    $carritoPrevio = collect(old('repuestos', []))->values();
@endphp

@section('content')
<form method="POST" action="{{ route('ventas.store') }}" x-data="puntoDeVenta()" @submit.prevent="confirmar($event)" @keydown.window.f2.prevent="$refs.buscador.focus()">
    @csrf

    @if($errors->any())
        <div class="alert alert-danger mb-6">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6 items-start">

        {{-- ================= CATÁLOGO ================= --}}
        <div class="xl:col-span-3 card-flush min-w-0">
            <div class="p-4 border-b border-gray-100 space-y-3">
                <div class="input-search">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" x-ref="buscador" x-model="busqueda" @keydown.enter.prevent="agregarPorCodigo()" autofocus
                           class="form-input !py-2.5" placeholder="Buscar producto o escanear código de barras y presionar Enter" autocomplete="off">
                </div>
                <div class="flex gap-2 overflow-x-auto pb-1 custom-scrollbar">
                    <button type="button" @click="categoria = null" class="btn-sm rounded-full border px-3 whitespace-nowrap"
                            :class="categoria === null ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'">Todos</button>
                    @foreach($categorias as $cat)
                        <button type="button" @click="categoria = {{ $cat->id }}" class="btn-sm rounded-full border px-3 whitespace-nowrap"
                                :class="categoria === {{ $cat->id }} ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'">{{ $cat->nombre }}</button>
                    @endforeach
                </div>
            </div>

            <div class="p-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 max-h-[55vh] xl:max-h-[calc(100vh-300px)] min-h-[300px] overflow-y-auto custom-scrollbar">
                <template x-for="p in productosFiltrados" :key="p.id">
                    <button type="button" @click="agregar(p)" class="group text-left rounded-lg border border-gray-200 bg-white p-3 transition hover:border-primary-500 hover:shadow-sm flex flex-col"
                            :class="enCarrito(p.id) && 'border-primary-500 ring-1 ring-primary-500'">
                        <div class="flex items-start justify-between gap-2">
                            <template x-if="p.imagen"><img :src="p.imagen" alt="" class="thumb w-10 h-10"></template>
                            <template x-if="!p.imagen">
                                <div class="thumb w-10 h-10 flex items-center justify-center text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </div>
                            </template>
                            <span class="badge" :class="p.servicio ? 'badge-info' : (p.stock <= 5 ? 'badge-warning' : 'badge-gray')"
                                  x-text="p.servicio ? 'Servicio' : formatear(p.stock) + ' ' + p.unidad"></span>
                        </div>
                        <p class="mt-2 text-sm font-medium text-gray-900 leading-snug line-clamp-2 min-h-[2.5rem]" x-text="p.nombre"></p>
                        <p class="text-xs text-gray-400" x-text="p.codigo"></p>
                        <p class="mt-auto pt-2 text-[15px] font-semibold text-gray-900 tabular-nums">
                            S/ <span x-text="p.precio.toFixed(2)"></span>
                            <span class="text-xs font-normal text-gray-400" x-text="'/ ' + p.unidad"></span>
                        </p>
                    </button>
                </template>
                <div x-show="!productosFiltrados.length" class="col-span-full empty-state">
                    <p class="font-medium text-gray-900">Sin productos</p>
                    <p class="mt-1">No hay productos disponibles con esa búsqueda.</p>
                </div>
            </div>
        </div>

        {{-- ================= VENTA ACTUAL ================= --}}
        <div id="venta-actual" class="xl:col-span-2 xl:sticky xl:top-6 card-flush min-w-0 scroll-mt-20">
            {{-- Cliente --}}
            <div class="p-4 border-b border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Cliente</p>
                    <button type="button" class="link text-xs" @click="abrirNuevoCliente()">+ Nuevo cliente</button>
                </div>
                <div class="relative" @click.outside="clientesAbierto = false">
                    <template x-if="cliente">
                        <div class="flex items-center gap-3 rounded-lg border border-gray-200 p-2.5">
                            <span class="avatar w-9 h-9 text-sm" x-text="cliente.nombre.charAt(0).toUpperCase()"></span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="cliente.nombre"></p>
                                <p class="text-xs text-gray-500" x-text="cliente.ruc ? 'RUC ' + cliente.ruc : 'DNI ' + (cliente.dni || '—')"></p>
                            </div>
                            <button type="button" class="btn-icon" title="Quitar cliente" @click="cliente = null">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                    <template x-if="!cliente">
                        <div class="input-search">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <input type="text" x-model="busquedaCliente" @input.debounce.300ms="buscarClientes()" @focus="buscarClientes()"
                                   class="form-input" placeholder="Cliente general · buscar por nombre, DNI o RUC" autocomplete="off">
                        </div>
                    </template>
                    <div x-show="clientesAbierto && !cliente" x-cloak class="absolute z-20 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg max-h-64 overflow-y-auto">
                        <template x-for="c in clientes" :key="c.id">
                            <button type="button" @click="elegirCliente(c)" class="w-full px-3 py-2 text-left hover:bg-gray-50 border-b border-gray-50 last:border-0">
                                <p class="text-sm font-medium text-gray-900" x-text="c.nombre"></p>
                                <p class="text-xs text-gray-500" x-text="(c.ruc ? 'RUC ' + c.ruc + ' · ' : '') + 'DNI ' + (c.dni || '—')"></p>
                            </button>
                        </template>
                        <p x-show="!clientes.length" class="px-3 py-3 text-sm text-gray-500">Sin resultados. <button type="button" class="link" @click="abrirNuevoCliente()">Registrar cliente</button></p>
                    </div>
                </div>
                <input type="hidden" name="cliente_id" :value="cliente ? cliente.id : ''">
            </div>

            {{-- Carrito --}}
            <div class="max-h-[340px] overflow-y-auto custom-scrollbar">
                <template x-for="(item, i) in carrito" :key="item.id">
                    <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 truncate" x-text="item.nombre"></p>
                            <p class="text-xs text-gray-500 tabular-nums" x-text="'S/ ' + item.precio.toFixed(2) + ' / ' + item.unidad"></p>
                            <input type="hidden" :name="'repuestos[' + i + '][id]'" :value="item.id">
                            <input type="hidden" :name="'repuestos[' + i + '][cantidad]'" :value="item.cantidad">
                            <input type="hidden" :name="'repuestos[' + i + '][precio]'" :value="item.precio">
                        </div>
                        <div class="flex items-center rounded-lg border border-gray-300">
                            <button type="button" class="px-2 py-1 text-gray-500 hover:text-gray-900" @click="cambiarCantidad(item, -1)">−</button>
                            <input type="number" class="w-14 border-0 p-1 text-center text-sm tabular-nums focus:ring-0 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none"
                                   :value="item.cantidad" :step="item.decimales ? '0.001' : '1'" @change="fijarCantidad(item, $event.target.value); $event.target.value = item.cantidad">
                            <button type="button" class="px-2 py-1 text-gray-500 hover:text-gray-900" @click="cambiarCantidad(item, 1)">+</button>
                        </div>
                        <p class="w-20 text-right text-sm font-semibold text-gray-900 tabular-nums" x-text="'S/ ' + (item.precio * item.cantidad).toFixed(2)"></p>
                        <button type="button" class="btn-icon-danger !w-7 !h-7" @click="quitar(i)" title="Quitar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
                <div x-show="!carrito.length" class="empty-state !py-10">
                    <div class="empty-icon"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
                    <p>Agrega productos desde el catálogo</p>
                </div>
            </div>

            {{-- Pago --}}
            <div class="p-4 border-t border-gray-200 bg-gray-50/60 space-y-4">
                <div class="flex items-baseline justify-between">
                    <span class="text-sm text-gray-500"><span x-text="carrito.length"></span> ítem(s)</span>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Total a cobrar</p>
                        <p class="text-3xl font-semibold text-gray-900 tabular-nums">S/ <span x-text="total().toFixed(2)"></span></p>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 mb-2">Método de pago</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach(\App\Models\Venta::METODOS_PAGO as $valor => $texto)
                            <label class="cursor-pointer rounded-lg border px-2 py-2 text-center text-xs font-medium transition"
                                   :class="metodo === '{{ $valor }}' ? 'border-primary-600 bg-primary-600 text-white' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'">
                                <input type="radio" name="metodo_pago" value="{{ $valor }}" x-model="metodo" class="sr-only">
                                {{ $valor === 'yape' ? 'Yape/Plin' : $texto }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div x-show="metodo === 'efectivo'" class="grid grid-cols-2 gap-3 items-end">
                    <div>
                        <label class="form-label !text-xs">Recibido</label>
                        <div class="input-addon">
                            <span>S/</span>
                            <input type="number" step="0.01" min="0" x-model.number="recibido" class="form-input tabular-nums" placeholder="0.00">
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Vuelto</p>
                        <p class="text-lg font-semibold tabular-nums" :class="vuelto() < 0 ? 'text-red-600' : 'text-gray-900'" x-text="recibido ? 'S/ ' + vuelto().toFixed(2) : '—'"></p>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="button" class="btn-secondary" @click="vaciar()" :disabled="!carrito.length">Vaciar</button>
                    <button type="submit" class="btn-primary btn-lg flex-1" :disabled="!carrito.length || enviando">
                        <span x-text="enviando ? 'Registrando…' : 'Cobrar S/ ' + total().toFixed(2)"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Celular/tablet: acceso rápido a la venta (el carrito queda debajo del catálogo) --}}
    <div x-show="carrito.length" x-cloak x-transition.opacity class="xl:hidden h-20"></div>
    <div x-show="carrito.length" x-cloak x-transition.opacity
         class="xl:hidden fixed inset-x-0 bottom-0 z-30 border-t border-gray-200 bg-white/95 backdrop-blur px-4 py-3 shadow-[0_-4px_12px_rgba(0,0,0,0.06)]">
        <button type="button" class="btn-primary btn-lg w-full justify-between"
                @click="document.getElementById('venta-actual').scrollIntoView({ behavior: 'smooth' })">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Ver venta (<span x-text="carrito.length"></span>)
            </span>
            <span class="tabular-nums" x-text="'S/ ' + total().toFixed(2)"></span>
        </button>
    </div>

    {{-- ================= MODAL: NUEVO CLIENTE ================= --}}
    <div x-show="modalCliente" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="modalCliente = false">
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="modalCliente = false"></div>
        <div class="relative w-full max-w-md card" x-transition>
            <h3 class="text-lg font-semibold text-gray-900">Nuevo cliente</h3>
            <p class="text-sm text-gray-500 mb-4">Se agregará y quedará seleccionado en esta venta.</p>
            <div class="space-y-3">
                <div>
                    <label class="form-label">DNI <span class="text-red-500">*</span></label>
                    <input type="text" x-model="nuevo.dni" @input="nuevo.dni = nuevo.dni.replace(/\D/g, '').slice(0, 8)" class="form-input font-mono" inputmode="numeric" placeholder="8 dígitos">
                    <p class="form-error" x-text="erroresCliente.dni?.[0]"></p>
                </div>
                <div>
                    <label class="form-label">Nombre completo <span class="text-red-500">*</span></label>
                    <input type="text" x-model="nuevo.nombre" class="form-input" maxlength="100">
                    <p class="form-error" x-text="erroresCliente.nombre?.[0]"></p>
                </div>
                <div>
                    <label class="form-label">Teléfono <span class="text-red-500">*</span></label>
                    <input type="tel" x-model="nuevo.telefono" class="form-input" maxlength="15">
                    <p class="form-error" x-text="erroresCliente.telefono?.[0]"></p>
                </div>
                <div>
                    <label class="form-label">Dirección</label>
                    <input type="text" x-model="nuevo.direccion" class="form-input" maxlength="150">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-5">
                <button type="button" class="btn-secondary" @click="modalCliente = false">Cancelar</button>
                <button type="button" class="btn-primary" @click="guardarCliente()" :disabled="guardandoCliente" x-text="guardandoCliente ? 'Guardando…' : 'Guardar cliente'"></button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    function puntoDeVenta() {
        const catalogo = @json($catalogo);
        const previo = @json($carritoPrevio);
        const rutas = { clientes: @js(route('clientes.search')), nuevoCliente: @js(route('clientes.storeQuick')) };
        const metodos = @json(\App\Models\Venta::METODOS_PAGO);

        return {
            busqueda: '',
            categoria: null,
            carrito: previo.map(i => {
                const p = catalogo.find(x => x.id === parseInt(i.id));
                return p ? { ...p, cantidad: parseFloat(i.cantidad) || 1 } : null;
            }).filter(Boolean),
            cliente: null,
            clientes: [],
            busquedaCliente: '',
            clientesAbierto: false,
            metodo: @js(old('metodo_pago', 'efectivo')),
            recibido: null,
            enviando: false,
            modalCliente: false,
            nuevo: { dni: '', nombre: '', telefono: '', direccion: '' },
            erroresCliente: {},
            guardandoCliente: false,

            get productosFiltrados() {
                const q = this.busqueda.trim().toLowerCase();
                return catalogo.filter(p =>
                    (this.categoria === null || p.categoria_id === this.categoria) &&
                    (!q || p.nombre.toLowerCase().includes(q) || p.codigo.toLowerCase().includes(q) || p.barras.toLowerCase() === q)
                ).slice(0, 120);
            },

            formatear(n) { return +(parseFloat(n) || 0).toFixed(3); },
            enCarrito(id) { return this.carrito.some(i => i.id === id); },
            total() { return this.carrito.reduce((s, i) => s + i.precio * i.cantidad, 0); },
            vuelto() { return (parseFloat(this.recibido) || 0) - this.total(); },

            normalizar(item, valor) {
                let n = parseFloat(valor) || 0;
                n = item.decimales ? Math.round(n * 1000) / 1000 : Math.round(n);
                const minimo = item.decimales ? 0.001 : 1;
                if (n < minimo) n = minimo;
                if (!item.servicio && n > item.stock) {
                    aviso(`Solo hay ${this.formatear(item.stock)} ${item.unidad} de ${item.nombre}`, 'warning');
                    n = item.stock;
                }
                return n;
            },

            agregar(p) {
                const item = this.carrito.find(i => i.id === p.id);
                if (item) {
                    item.cantidad = this.normalizar(item, item.cantidad + 1);
                } else {
                    this.carrito.push({ ...p, cantidad: 1 });
                }
            },

            agregarPorCodigo() {
                const q = this.busqueda.trim().toLowerCase();
                if (!q) return;
                const exacto = catalogo.find(p => (p.barras && p.barras.toLowerCase() === q) || p.codigo.toLowerCase() === q);
                const unico = this.productosFiltrados.length === 1 ? this.productosFiltrados[0] : null;
                const producto = exacto || unico;
                if (producto) {
                    this.agregar(producto);
                    this.busqueda = '';
                } else {
                    aviso('No se encontró un producto con ese código', 'warning');
                }
            },

            cambiarCantidad(item, paso) { item.cantidad = this.normalizar(item, item.cantidad + paso); },
            fijarCantidad(item, valor) { item.cantidad = this.normalizar(item, valor); },
            quitar(i) { this.carrito.splice(i, 1); },

            async vaciar() {
                const r = await Dialogo.fire({ title: '¿Vaciar la venta?', text: 'Se quitarán todos los productos.', icon: 'question', showCancelButton: true, confirmButtonText: 'Vaciar', cancelButtonText: 'Cancelar' });
                if (r.isConfirmed) { this.carrito = []; this.recibido = null; }
            },

            async buscarClientes() {
                const res = await fetch(rutas.clientes + '?q=' + encodeURIComponent(this.busquedaCliente), { headers: { 'Accept': 'application/json' } });
                this.clientes = res.ok ? (await res.json()).slice(0, 15) : [];
                this.clientesAbierto = true;
            },

            elegirCliente(c) {
                this.cliente = c;
                this.clientesAbierto = false;
                this.busquedaCliente = '';
            },

            abrirNuevoCliente() {
                this.nuevo = { dni: /^\d{8}$/.test(this.busquedaCliente) ? this.busquedaCliente : '', nombre: '', telefono: '', direccion: '' };
                this.erroresCliente = {};
                this.clientesAbierto = false;
                this.modalCliente = true;
            },

            async guardarCliente() {
                this.guardandoCliente = true;
                this.erroresCliente = {};
                try {
                    const res = await fetch(rutas.nuevoCliente, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': @js(csrf_token()) },
                        body: JSON.stringify(this.nuevo),
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        this.elegirCliente(data.cliente);
                        this.modalCliente = false;
                        aviso('Cliente registrado');
                    } else {
                        this.erroresCliente = data.errors || { nombre: [data.message || 'No se pudo guardar'] };
                    }
                } catch (e) {
                    aviso('No se pudo guardar el cliente', 'error');
                } finally {
                    this.guardandoCliente = false;
                }
            },

            async confirmar(evento) {
                if (!this.carrito.length) return;
                if (this.metodo === 'efectivo' && this.recibido && this.vuelto() < 0) {
                    aviso('El monto recibido es menor al total', 'warning');
                    return;
                }
                const vuelto = this.metodo === 'efectivo' && this.recibido ? `<p class="mt-1">Vuelto: <strong>S/ ${this.vuelto().toFixed(2)}</strong></p>` : '';
                const r = await Dialogo.fire({
                    title: 'Confirmar venta',
                    html: `<p>${this.cliente ? this.cliente.nombre : 'Cliente general'} · ${metodos[this.metodo]}</p>
                           <p class="mt-2 text-2xl font-semibold text-gray-900">S/ ${this.total().toFixed(2)}</p>${vuelto}`,
                    showCancelButton: true,
                    confirmButtonText: 'Registrar venta',
                    cancelButtonText: 'Revisar',
                });
                if (r.isConfirmed) {
                    this.enviando = true;
                    evento.target.submit();
                }
            },
        };
    }
</script>
@endpush
