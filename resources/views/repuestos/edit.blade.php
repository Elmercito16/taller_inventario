@extends('layouts.app')

@section('title', 'Editar Repuesto')

@push('styles')
<style>
    .form-section { animation: slideUp 0.5s ease-out both; }
    .form-section:nth-child(n) { animation-delay: calc(0.1s * var(--i)); }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    
    .drag-area { border: 2px dashed #d1d5db; transition: all 0.3s ease; }
    .drag-area.dragover { border-color: #218786; background-color: #f0fdfa; }
    
    .input-group { position: relative; }
    .input-icon { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; pointer-events: none; }
    .input-group input:focus ~ .input-icon, .input-group select:focus ~ .input-icon { color: #218786; }
    
    [x-cloak] { display: none !important; }
    
    .image-preview { transition: all 0.3s ease; }
    .image-preview:hover { transform: scale(1.05); }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto px-4 pb-8">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border p-4 sm:p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Editar Repuesto</h1>
                <p class="text-sm text-gray-600 mt-1">Actualizando: <strong class="text-[#218786]">{{ $repuesto->nombre }}</strong></p>
            </div>
            <div class="flex items-center gap-2 bg-[#218786]/10 px-3 py-2 rounded-lg">
                <div class="w-3 h-3 bg-[#218786] rounded-full"></div>
                <span class="text-sm font-medium">Código: <strong class="text-[#218786]">{{ $repuesto->codigo }}</strong></span>
            </div>
        </div>
    </div>

    <form action="{{ route('repuestos.update', $repuesto->id) }}" method="POST" enctype="multipart/form-data" x-data="repuestoForm()" @submit.prevent="submitForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="codigo" value="{{ $repuesto->codigo }}">

        <!-- Información Básica -->
        <div class="bg-white rounded-xl shadow-sm border p-4 sm:p-6 mb-6 form-section" style="--i:1">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <span class="w-10 h-10 bg-[#218786]/10 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
                Información Básica
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <div class="input-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $repuesto->nombre) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786]" 
                           placeholder="Ej: Filtro de aceite" required>
                    <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    @error('nombre')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="input-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Marca <span class="text-red-500">*</span></label>
                    <input type="text" name="marca" value="{{ old('marca', $repuesto->marca) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786]" 
                           placeholder="Ej: Toyota, Bosch" required>
                    @error('marca')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea name="descripcion" rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] resize-none"
                              placeholder="Descripción detallada del repuesto...">{{ old('descripcion', $repuesto->descripcion) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Inventario y Precios -->
        <div class="bg-white rounded-xl shadow-sm border p-4 sm:p-6 mb-6 form-section" style="--i:2">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <span class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </span>
                Inventario y Precios
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <div class="input-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cantidad <span class="text-red-500">*</span></label>
                    <input type="number" 
                           name="cantidad" 
                           id="cantidad-input"
                           value="{{ old('cantidad', $repuesto->cantidad) }}" 
                           @input="updateCantidad($event.target.value)"
                           min="0" 
                           step="1"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786]" 
                           placeholder="0" 
                           required>
                    <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    @error('cantidad')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="input-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Precio Unitario (S/) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">S/</span>
                        <input type="number" 
                               name="precio_unitario" 
                               id="precio-input"
                               value="{{ old('precio_unitario', $repuesto->precio_unitario) }}" 
                               @input="updatePrecio($event.target.value)"
                               step="0.01" 
                               min="0"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786]" 
                               placeholder="0.00" 
                               required>
                    </div>
                    @error('precio_unitario')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="input-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stock Mínimo</label>
                    <input type="number" 
                           name="minimo_stock" 
                           value="{{ old('minimo_stock', $repuesto->minimo_stock) }}" 
                           min="0" 
                           step="1"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786]" 
                           placeholder="0">
                    <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    @error('minimo_stock')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2 lg:col-span-3">
                    <div class="bg-gradient-to-r from-[#218786]/10 to-[#218786]/20 rounded-xl p-4 border-2 border-[#218786]/30">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                            <div>
                                <p class="text-sm font-medium text-[#218786]">Valor Total del Inventario</p>
                                <p class="text-xs text-gray-600">Cantidad × Precio unitario</p>
                            </div>
                            <p class="text-2xl sm:text-3xl font-bold text-[#218786]" x-text="'S/ ' + total.toFixed(2)">S/ {{ number_format($repuesto->cantidad * $repuesto->precio_unitario, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clasificación -->
        <div class="bg-white rounded-xl shadow-sm border p-4 sm:p-6 mb-6 form-section" style="--i:3">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <span class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </span>
                Clasificación y Fechas
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="input-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categoría <span class="text-red-500">*</span></label>
                    <select name="categoria_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786]" required>
                        <option value="">Seleccione una categoría</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ old('categoria_id', $repuesto->categoria_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    @error('categoria_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="input-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Proveedor</label>
                    <select name="proveedor_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786]">
                        <option value="">Seleccione un proveedor</option>
                        @foreach($proveedores as $prov)
                            <option value="{{ $prov->id }}" {{ old('proveedor_id', $repuesto->proveedor_id) == $prov->id ? 'selected' : '' }}>
                                {{ $prov->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                <div class="input-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Ingreso</label>
                    <input type="date" name="fecha_ingreso" 
                           value="{{ old('fecha_ingreso', $repuesto->fecha_ingreso ? \Carbon\Carbon::parse($repuesto->fecha_ingreso)->format('Y-m-d') : '') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786]">
                    <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Imagen -->
        <div class="bg-white rounded-xl shadow-sm border p-4 sm:p-6 mb-6 form-section" style="--i:4">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <span class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </span>
                Imagen del Repuesto
            </h2>

            <div class="drag-area rounded-xl p-6 text-center hover:border-[#218786] transition-colors" 
                 @dragover.prevent="$el.classList.add('dragover')" 
                 @dragleave.prevent="$el.classList.remove('dragover')" 
                 @drop.prevent="handleDrop($event, $el)">
                
                <div x-show="!preview" class="space-y-4">
                    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-lg font-medium text-gray-700">Arrastra una imagen aquí</p>
                        <p class="text-sm text-gray-500">o</p>
                    </div>
                    <label for="imagen" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm cursor-pointer hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Seleccionar archivo
                    </label>
                    <p class="text-xs text-gray-400">PNG, JPG, JPEG hasta 5MB</p>
                </div>

                <div x-show="preview" x-cloak class="space-y-4">
                    <div class="relative inline-block">
                        <img :src="preview" alt="Preview" class="image-preview w-48 h-48 object-cover rounded-xl shadow-lg mx-auto border-2 border-gray-200">
                        <button type="button" @click="removeImg" 
                                class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <p class="text-sm text-gray-600 truncate px-4" x-text="imgName"></p>
                    <button type="button" @click="$refs.imgInput.click()"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                        Cambiar imagen
                    </button>
                </div>

                <input type="file" name="imagen" id="imagen" x-ref="imgInput" @change="handleFile($event)" class="hidden" accept="image/*">
            </div>
        </div>

        <!-- Botones -->
        <div class="bg-white rounded-xl shadow-sm border p-4 sm:p-6 sticky bottom-0 z-10 form-section" style="--i:5">
            <div class="flex flex-col gap-4">
                <p class="text-sm text-gray-500 text-center sm:text-left">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Los campos marcados con <span class="text-red-500 font-medium">*</span> son obligatorios
                </p>
                
                <div class="flex flex-col-reverse sm:flex-row gap-3">
                    <a href="{{ route('repuestos.index') }}" 
                       class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all text-center flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancelar
                    </a>
                    
                    <button type="submit" :disabled="loading" 
                            :class="loading ? 'opacity-60 cursor-not-allowed' : 'hover:shadow-lg transform hover:-translate-y-0.5'"
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-bold rounded-lg transition-all flex items-center justify-center gap-2 shadow-md">
                        <svg x-show="!loading" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg x-show="loading" x-cloak class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                            ircle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="loading ? 'Actualizando...' : 'Actualizar Repuesto'">Actualizar Repuesto</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function repuestoForm() {
    return {
        cantidad: {{ old('cantidad', $repuesto->cantidad) }},
        precio: {{ old('precio_unitario', $repuesto->precio_unitario) }},
        preview: @json($repuesto->imagen ? asset('storage/' . $repuesto->imagen) : null),
        imgName: @json($repuesto->imagen ? basename($repuesto->imagen) : ''),
        loading: false,
        
        get total() {
            return (parseFloat(this.cantidad) || 0) * (parseFloat(this.precio) || 0);
        },
        
        updateCantidad(value) {
            this.cantidad = parseFloat(value) || 0;
        },
        
        updatePrecio(value) {
            this.precio = parseFloat(value) || 0;
        },
        
        handleFile(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            if (file.size > 5242880) {
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Archivo muy grande', 
                    text: 'La imagen debe ser menor a 5MB', 
                    confirmButtonColor: '#218786' 
                });
                return;
            }
            
            if (!file.type.startsWith('image/')) {
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Formato inválido', 
                    text: 'Solo se permiten imágenes (PNG, JPG, JPEG)', 
                    confirmButtonColor: '#218786' 
                });
                return;
            }
            
            this.imgName = file.name;
            const reader = new FileReader();
            reader.onload = e => this.preview = e.target.result;
            reader.readAsDataURL(file);
        },
        
        handleDrop(e, el) {
            el.classList.remove('dragover');
            const file = e.dataTransfer.files[0];
            if (file?.type.startsWith('image/')) {
                this.$refs.imgInput.files = e.dataTransfer.files;
                this.handleFile({ target: { files: [file] } });
            }
        },
        
        removeImg() {
            this.preview = null;
            this.imgName = '';
            this.$refs.imgInput.value = '';
        },
        
        async submitForm(e) {
            if (this.loading) return;
            
            if (!this.cantidad || this.cantidad < 0) {
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Error de validación', 
                    text: 'La cantidad debe ser mayor a 0', 
                    confirmButtonColor: '#218786' 
                });
                return;
            }
            
            if (!this.precio || this.precio <= 0) {
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Error de validación', 
                    text: 'El precio debe ser mayor a 0', 
                    confirmButtonColor: '#218786' 
                });
                return;
            }
            
            const result = await Swal.fire({
                title: '<strong>¿Actualizar repuesto?</strong>',
                html: `
                    <div class="text-left bg-gray-50 p-4 rounded-lg space-y-2">
                        <p class="text-sm"><strong class="text-gray-700">Cantidad:</strong> <span class="text-gray-900">${this.cantidad} unidades</span></p>
                        <p class="text-sm"><strong class="text-gray-700">Precio:</strong> <span class="text-gray-900">S/ ${parseFloat(this.precio).toFixed(2)}</span></p>
                        <p class="text-sm"><strong class="text-gray-700">Valor Total:</strong> <span class="text-[#218786] font-bold text-lg">S/ ${this.total.toFixed(2)}</span></p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '✓ Sí, actualizar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#218786',
                cancelButtonColor: '#6b7280',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-lg px-6 py-3 font-semibold',
                    cancelButton: 'rounded-lg px-6 py-3'
                }
            });
            
            if (!result.isConfirmed) return;
            
            this.loading = true;
            
            Swal.fire({ 
                title: 'Actualizando repuesto...', 
                html: '<div class="flex justify-center mt-4"><div class="animate-spin w-10 h-10 border-4 border-[#218786] border-t-transparent rounded-full"></div></div>',
                allowOutsideClick: false, 
                showConfirmButton: false
            });
            
            e.target.submit();
        },
        
        init() {
            console.log('📦 Repuesto cargado:', {
                cantidad: this.cantidad,
                precio: this.precio,
                total: this.total.toFixed(2),
                imagen: this.preview || 'No hay imagen'
            });
            
            console.log('🖼️ URL de imagen:', this.preview);
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Swal.fire({ 
            icon: 'success', 
            title: '¡Éxito!', 
            text: "{{ session('success') }}", 
            confirmButtonColor: '#218786',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-lg px-6 py-3'
            }
        });
    @endif
    
    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Error de validación',
            html: '<ul class="text-left list-disc pl-5 space-y-1 mt-2">@foreach($errors->all() as $error)<li class="text-sm">{{ $error }}</li>@endforeach</ul>',
            confirmButtonColor: '#218786',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-lg px-6 py-3'
            }
        });
    @endif
});
</script>
@endpush

@endsection
