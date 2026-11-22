{{--
    Formulario de producto/servicio (alta y edición).
    Variables: $categorias, $proveedores, $unidades, $afectaciones, $tiposItem,
               $repuesto (opcional, edición), $nuevoCodigo (opcional, alta).
--}}
@php
    $r = $repuesto ?? null;
    $estado = [
        'tipo' => old('tipo', $r->tipo ?? 'producto'),
        'unidad' => old('unidad_medida', $r->unidad_medida ?? 'NIU'),
        'precio' => old('precio_unitario', $r->precio_unitario ?? ''),
        'costo' => old('precio_compra', $r->precio_compra ?? ''),
        'imagen' => $r && $r->imagen ? asset('storage/' . $r->imagen) : null,
    ];
    $decimales = collect($unidades)->filter(fn ($u) => $u[2])->keys()->values();
    $campo = fn ($nombre) => $errors->has($nombre) ? 'form-input form-input-error' : 'form-input';
@endphp

<div x-data="productoForm(@js($estado), @js($decimales))" class="space-y-6">

    @if($errors->any())
        <div class="alert alert-danger">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <p class="font-medium">Revisa los campos marcados</p>
                <ul class="mt-1 list-disc list-inside">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card">
        {{-- Información general --}}
        <section class="form-section">
            <div>
                <h2 class="form-section-title">Información general</h2>
                <p class="form-section-text">Nombre y clasificación del producto.</p>
            </div>
            <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="form-label" for="nombre">Nombre <span class="text-red-500">*</span></label>
                    <input id="nombre" type="text" name="nombre" value="{{ old('nombre', $r->nombre ?? '') }}" class="{{ $campo('nombre') }}" required maxlength="255" placeholder="Ej.: Aceite Primor 1L" autofocus>
                    @error('nombre')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="codigo">Código interno <span class="text-red-500">*</span></label>
                    <input id="codigo" type="text" name="codigo" value="{{ old('codigo', $r->codigo ?? ($nuevoCodigo ?? '')) }}" class="{{ $campo('codigo') }} font-mono" required maxlength="50">
                    @error('codigo')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="marca">Marca</label>
                    <input id="marca" type="text" name="marca" value="{{ old('marca', $r->marca ?? '') }}" class="{{ $campo('marca') }}" maxlength="255" placeholder="Opcional">
                    @error('marca')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="categoria_id">Categoría <span class="text-red-500">*</span></label>
                    <select id="categoria_id" name="categoria_id" class="{{ $campo('categoria_id') }}" required>
                        <option value="">Selecciona…</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" @selected(old('categoria_id', $r->categoria_id ?? '') == $categoria->id)>{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                    @if($categorias->isEmpty())
                        <p class="form-help">No hay categorías. <a href="{{ route('categorias.create') }}" class="link">Crea una</a>.</p>
                    @endif
                    @error('categoria_id')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="proveedor_id">Proveedor</label>
                    <select id="proveedor_id" name="proveedor_id" class="{{ $campo('proveedor_id') }}">
                        <option value="">Sin proveedor</option>
                        @foreach($proveedores as $proveedor)
                            <option value="{{ $proveedor->id }}" @selected(old('proveedor_id', $r->proveedor_id ?? '') == $proveedor->id)>{{ $proveedor->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label" for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="3" class="{{ $campo('descripcion') }}" placeholder="Presentación, características, notas…">{{ old('descripcion', $r->descripcion ?? '') }}</textarea>
                </div>
            </div>
        </section>

        {{-- Venta e impuestos --}}
        <section class="form-section">
            <div>
                <h2 class="form-section-title">Venta e impuestos</h2>
                <p class="form-section-text">Cómo se vende y cómo se factura ante SUNAT.</p>
            </div>
            <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="form-label">Tipo <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($tiposItem as $valor => $nombre)
                            <label class="choice" :class="tipo === '{{ $valor }}' && 'choice-active'">
                                <input type="radio" name="tipo" value="{{ $valor }}" x-model="tipo" @change="alCambiarTipo()" class="sr-only">
                                <span class="text-sm font-medium text-gray-900">{{ $valor === 'producto' ? 'Producto' : 'Servicio' }}</span>
                                <span class="text-xs text-gray-500">{{ $valor === 'producto' ? 'Se controla su stock e ingresa por compras.' : 'No maneja stock (delivery, instalación, mano de obra…).' }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="form-label" for="unidad_medida">Unidad de medida <span class="text-red-500">*</span></label>
                    <select id="unidad_medida" name="unidad_medida" x-model="unidad" class="{{ $campo('unidad_medida') }}">
                        @foreach($unidades as $codigo => [$nombre, $abreviatura])
                            <option value="{{ $codigo }}">{{ $nombre }} ({{ $abreviatura }})</option>
                        @endforeach
                    </select>
                    <p class="form-help" x-text="permiteDecimales ? 'Admite cantidades con decimales (ej. 1.5).' : 'Solo cantidades enteras.'"></p>
                    @error('unidad_medida')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="afectacion_igv">Afectación del IGV <span class="text-red-500">*</span></label>
                    <select id="afectacion_igv" name="afectacion_igv" class="{{ $campo('afectacion_igv') }}">
                        @foreach($afectaciones as $codigo => $nombre)
                            <option value="{{ $codigo }}" @selected(old('afectacion_igv', $r->afectacion_igv ?? '10') === $codigo)>{{ $nombre }}</option>
                        @endforeach
                    </select>
                    <p class="form-help">La mayoría de productos son "Gravado".</p>
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label" for="codigo_barras">Código de barras</label>
                    <input id="codigo_barras" type="text" name="codigo_barras" value="{{ old('codigo_barras', $r->codigo_barras ?? '') }}" class="{{ $campo('codigo_barras') }} font-mono" maxlength="50" placeholder="Escanéalo o escríbelo" autocomplete="off">
                    <p class="form-help">Opcional. Permite vender y comprar escaneando.</p>
                    @error('codigo_barras')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        {{-- Precios --}}
        <section class="form-section">
            <div>
                <h2 class="form-section-title">Precios</h2>
                <p class="form-section-text">El precio de venta es el que paga el cliente (IGV incluido).</p>
            </div>
            <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="form-label" for="precio_unitario">Precio de venta <span class="text-red-500">*</span></label>
                    <div class="input-addon">
                        <span>S/</span>
                        <input id="precio_unitario" type="number" name="precio_unitario" x-model="precio" step="0.01" min="0" class="{{ $campo('precio_unitario') }} tabular-nums" required placeholder="0.00">
                    </div>
                    @error('precio_unitario')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="precio_compra">Costo de compra</label>
                    <div class="input-addon">
                        <span>S/</span>
                        <input id="precio_compra" type="number" name="precio_compra" x-model="costo" step="0.01" min="0" class="{{ $campo('precio_compra') }} tabular-nums" placeholder="0.00">
                    </div>
                    @error('precio_compra')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <p class="form-label">Margen</p>
                    <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm tabular-nums">
                        <template x-if="margen !== null">
                            <span :class="margen < 0 ? 'text-red-600' : 'text-gray-900'">
                                <span class="font-semibold" x-text="margen.toFixed(1) + '%'"></span>
                                <span class="text-gray-500" x-text="'· S/ ' + ganancia.toFixed(2) + ' por unidad'"></span>
                            </span>
                        </template>
                        <span x-show="margen === null" class="text-gray-400">Ingresa precio y costo</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- Inventario --}}
        <section class="form-section" x-show="tipo === 'producto'">
            <div>
                <h2 class="form-section-title">Inventario</h2>
                <p class="form-section-text">
                    @if($r)
                        Si cambias el stock aquí, quedará registrado como ajuste en el kardex.
                    @else
                        El stock inicial queda registrado en el kardex.
                    @endif
                </p>
            </div>
            <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="form-label" for="cantidad">{{ $r ? 'Stock actual' : 'Stock inicial' }} <span class="text-red-500">*</span></label>
                    <input id="cantidad" type="number" name="cantidad" value="{{ old('cantidad', isset($r) ? \App\Support\Catalogos::cantidad($r->cantidad) : 0) }}"
                           :step="permiteDecimales ? '0.001' : '1'" min="0" :required="tipo === 'producto'" class="{{ $campo('cantidad') }} tabular-nums">
                    @error('cantidad')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="minimo_stock">Stock mínimo</label>
                    <input id="minimo_stock" type="number" name="minimo_stock" value="{{ old('minimo_stock', isset($r) ? \App\Support\Catalogos::cantidad($r->minimo_stock) : 5) }}"
                           :step="permiteDecimales ? '0.001' : '1'" min="0" class="{{ $campo('minimo_stock') }} tabular-nums">
                    <p class="form-help">Avisa cuando el stock baje de este número.</p>
                </div>
                <div>
                    <label class="form-label" for="fecha_ingreso">Fecha de ingreso</label>
                    <input id="fecha_ingreso" type="date" name="fecha_ingreso" value="{{ old('fecha_ingreso', isset($r) && $r->fecha_ingreso ? $r->fecha_ingreso->format('Y-m-d') : '') }}" class="{{ $campo('fecha_ingreso') }}">
                </div>
            </div>
        </section>

        {{-- Imagen --}}
        <section class="form-section">
            <div>
                <h2 class="form-section-title">Imagen</h2>
                <p class="form-section-text">Opcional. JPG o PNG de hasta 2 MB.</p>
            </div>
            <div class="lg:col-span-2 flex items-center gap-4">
                <div class="thumb w-20 h-20 flex items-center justify-center overflow-hidden">
                    <img x-show="imagen" :src="imagen" alt="" class="w-full h-full object-cover">
                    <svg x-show="!imagen" class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div class="flex-1">
                    <input type="file" name="imagen" accept="image/*" class="form-file" @change="vistaPrevia($event)">
                    @error('imagen')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>
    </div>
</div>

@push('scripts')
<script>
    function productoForm(estado, unidadesDecimales) {
        return {
            tipo: estado.tipo,
            unidad: estado.unidad,
            precio: estado.precio,
            costo: estado.costo,
            imagen: estado.imagen,

            get permiteDecimales() {
                return unidadesDecimales.includes(this.unidad);
            },
            get ganancia() {
                return (parseFloat(this.precio) || 0) - (parseFloat(this.costo) || 0);
            },
            get margen() {
                const precio = parseFloat(this.precio), costo = parseFloat(this.costo);
                if (!precio || isNaN(costo) || this.costo === '' || this.costo === null) return null;
                return ((precio - costo) / precio) * 100;
            },
            alCambiarTipo() {
                // Un servicio se factura como "ZZ"; un producto, por defecto en unidades
                if (this.tipo === 'servicio' && this.unidad === 'NIU') this.unidad = 'ZZ';
                if (this.tipo === 'producto' && this.unidad === 'ZZ') this.unidad = 'NIU';
            },
            vistaPrevia(evento) {
                const archivo = evento.target.files[0];
                if (!archivo) return;
                if (archivo.size > 2 * 1024 * 1024) {
                    aviso('La imagen debe pesar menos de 2 MB', 'error');
                    evento.target.value = '';
                    return;
                }
                const lector = new FileReader();
                lector.onload = e => this.imagen = e.target.result;
                lector.readAsDataURL(archivo);
            },
        };
    }
</script>
@endpush
