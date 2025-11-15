@extends('layouts.app')

@section('title', 'Editar Repuesto')
@section('page-title', 'Editar Repuesto')
@section('page-description', 'Actualiza la información del repuesto en el inventario')

@section('breadcrumbs')
<nav class="flex items-center space-x-2 text-sm text-gray-500">
    <a href="{{ route('dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
    <a href="{{ route('repuestos.index') }}" class="hover:text-primary-600 transition-colors">Repuestos</a>
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
    <span class="font-medium text-gray-900">Editar</span>
</nav>
@endsection

@push('styles')
<style>
    /* Animaciones personalizadas */
    .form-section {
        animation: slideUp 0.5s ease-out;
        animation-fill-mode: both;
    }
    
    .form-section:nth-child(1) { animation-delay: 0.1s; }
    .form-section:nth-child(2) { animation-delay: 0.2s; }
    .form-section:nth-child(3) { animation-delay: 0.3s; }
    .form-section:nth-child(4) { animation-delay: 0.4s; }
    
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Estilos para drag and drop */
    .drag-area {
        border: 2px dashed #d1d5db;
        transition: all 0.3s ease;
    }
    
    .drag-area.dragover {
        border-color: #218786;
        background-color: #f0fdfa;
        transform: scale(1.02);
    }
    
    /* Estilos para inputs mejorados */
    .input-group { position: relative; }
    
    .input-group input:focus + .input-icon,
    .input-group select:focus + .input-icon,
    .input-group textarea:focus + .input-icon {
        color: #218786;
    }
    
    .input-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        transition: color 0.2s ease;
        pointer-events: none;
    }
    
    /* Preview de imagen */
    .image-preview {
        transition: all 0.3s ease;
    }
    .image-preview:hover {
        transform: scale(1.05);
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header de la página -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Repuesto</h1>
                <p class="text-gray-600 mt-1 truncate">Actualizando: <strong>{{ $repuesto->nombre }}</strong></p>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-primary-500 rounded-full"></div>
                <span class="text-sm font-medium text-gray-700">Código: <strong class="text-primary-600">{{ $repuesto->codigo }}</strong></span>
            </div>
        </div>
    </div>

    <form action="{{ route('repuestos.update', $repuesto->id) }}" method="POST" enctype="multipart/form-data" x-data="repuestoForm()" @submit.prevent="submitForm">
        @csrf
        @method('PUT')
        
        <!-- Código oculto -->
        <input type="hidden" name="codigo" value="{{ $repuesto->codigo }}">

        <div class="space-y-6">
            <!-- Sección: Información Básica -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 form-section">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Información Básica</h2>
                        <p class="text-sm text-gray-600">Datos principales del repuesto</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre -->
                    <div class="input-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre del Repuesto <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="nombre" 
                               x-model="form.nombre"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('nombre') border-red-300 @enderror" 
                               placeholder="Ej: Filtro de aceite, Bujía, etc."
                               required>
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        @error('nombre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Marca -->
                    <div class="input-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Marca <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="marca" 
                               x-model="form.marca"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('marca') border-red-300 @enderror" 
                               placeholder="Ej: Toyota, Nissan, Bosch"
                               required>
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        @error('marca')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Descripción -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                        <textarea name="descripcion" 
                                  x-model="form.descripcion"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 resize-none @error('descripcion') border-red-300 @enderror" 
                                  rows="4"
                                  placeholder="Descripción detallada del repuesto, compatibilidades, especificaciones técnicas...">{{ old('descripcion', $repuesto->descripcion) }}</textarea>
                        @error('descripcion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sección: Inventario y Precios -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 form-section">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Inventario y Precios</h2>
                        <p class="text-sm text-gray-600">Stock actual y precio del repuesto</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Cantidad -->
                    <div class="input-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Cantidad <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="cantidad" 
                               x-model="form.cantidad"
                               @input="calculateTotal"
                               min="0" 
                               step="1"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('cantidad') border-red-300 @enderror" 
                               placeholder="0"
                               required>
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        @error('cantidad')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Precio Unitario -->
                    <div class="input-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Precio Unitario (S/) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">S/</span>
                            <input type="number" 
                                   name="precio_unitario" 
                                   x-model="form.precio_unitario"
                                   @input="calculateTotal"
                                   step="0.01" 
                                   min="0"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('precio_unitario') border-red-300 @enderror" 
                                   placeholder="0.00"
                                   required>
                        </div>
                        @error('precio_unitario')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stock Mínimo -->
                    <div class="input-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock Mínimo</label>
                        <input type="number" 
                               name="minimo_stock" 
                               x-model="form.minimo_stock"
                               min="0" 
                               step="1"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('minimo_stock') border-red-300 @enderror" 
                               placeholder="0">
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        @error('minimo_stock')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Valor Total (Solo lectura) -->
                    <div class="lg:col-span-3">
                        <div class="bg-gradient-to-r from-primary-50 to-primary-100 rounded-lg p-4 border border-primary-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-primary-800">Valor Total del Inventario</p>
                                    <p class="text-xs text-primary-600">Cantidad × Precio unitario</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-primary-700" x-text="'S/ ' + totalValue.toFixed(2)"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección: Clasificación y Fechas -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 form-section">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Clasificación y Fechas</h2>
                        <p class="text-sm text-gray-600">Organización del repuesto en el sistema</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Categoría -->
                    <div class="input-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Categoría <span class="text-red-500">*</span>
                        </label>
                        <select name="categoria_id" 
                                x-model="form.categoria_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('categoria_id') border-red-300 @enderror" 
                                required>
                            <option value="">Seleccione una categoría</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}" {{ old('categoria_id', $repuesto->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                        @error('categoria_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Proveedor -->
                    <div class="input-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Proveedor</label>
                        <select name="proveedor_id" 
                                x-model="form.proveedor_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('proveedor_id') border-red-300 @enderror">
                            <option value="">Seleccione un proveedor</option>
                            @foreach($proveedores as $proveedor)
                                <option value="{{ $proveedor->id }}" {{ old('proveedor_id', $repuesto->proveedor_id) == $proveedor->id ? 'selected' : '' }}>
                                    {{ $proveedor->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                        @error('proveedor_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha de Ingreso -->
                    <div class="input-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Ingreso</label>
                        <input type="date" 
                               name="fecha_ingreso" 
                               x-model="form.fecha_ingreso"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('fecha_ingreso') border-red-300 @enderror">
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        @error('fecha_ingreso')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sección: Imagen -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 form-section">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Imagen del Repuesto</h2>
                        <p class="text-sm text-gray-600">Cambia la foto del repuesto (opcional)</p>
                    </div>
                </div>

                <!-- Área de carga de imagen -->
                <div class="drag-area relative border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-primary-400 transition-colors duration-300"
                     @dragover.prevent="$el.classList.add('dragover')"
                     @dragleave.prevent="$el.classList.remove('dragover')"
                     @drop.prevent="handleDrop($event, $el)">
                    
                    <div x-show="!imagePreview" class="space-y-4">
                        <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-medium text-gray-700">Arrastra una nueva imagen aquí</p>
                            <p class="text-sm text-gray-500">o</p>
                        </div>
                        <label for="imagen" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm cursor-pointer hover:bg-gray-50 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Seleccionar archivo
                        </label>
                        <p class="text-xs text-gray-400">PNG, JPG, JPEG hasta 5MB</p>
                    </div>

                    <!-- Preview de imagen -->
                    <div x-show="imagePreview" class="space-y-4">
                        <div class="relative inline-block">
                            <img :src="imagePreview" alt="Preview" class="image-preview w-48 h-48 object-cover rounded-xl shadow-md">
                            <button type="button" 
                                    @click="removeImage"
                                    class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <p class="text-sm text-gray-600" x-text="imageName"></p>
                        <button type="button" 
                                @click="$refs.imageInput.click()"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition-colors duration-200">
                            Cambiar imagen
                        </button>
                    </div>

                    <input type="file" 
                           name="imagen" 
                           id="imagen"
                           x-ref="imageInput"
                           @change="handleFileSelect($event)"
                           class="hidden" 
                           accept="image/*">
                </div>
                
                @error('imagen')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botones de acción -->
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center text-sm text-gray-500 text-center sm:text-left">
                    <svg class="w-4 h-4 mr-2 hidden sm:inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Los campos marcados con <span class="text-red-500 font-medium">*</span> son obligatorios</span>
                </div>
                
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                    <a href="{{ route('repuestos.index') }}" 
                       class="w-full sm:w-auto px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancelar
                    </a>
            
                    <button type="submit" 
        :disabled="isSubmitting"
        :class="isSubmitting ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#16735fff]'"
        class="w-full sm:w-auto px-6 py-3 bg-[#1b8c72ff] text-white font-medium rounded-lg transition-all duration-200 flex items-center justify-center">
    <svg x-show="!isSubmitting" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    <svg x-show="isSubmitting" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    <span x-text="isSubmitting ? 'Actualizando...' : 'Actualizar Repuesto'"></span>
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
        form: {
            nombre: @json(old('nombre', $repuesto->nombre)),
            marca: @json(old('marca', $repuesto->marca)),
            // Usamos json para escapar correctamente saltos de línea en la descripción
            descripcion: @json(old('descripcion', $repuesto->descripcion)),
            cantidad: @json(old('cantidad', $repuesto->cantidad)),
            precio_unitario: @json(old('precio_unitario', $repuesto->precio_unitario)),
            minimo_stock: @json(old('minimo_stock', $repuesto->minimo_stock)),
            categoria_id: @json(old('categoria_id', $repuesto->categoria_id)),
            proveedor_id: @json(old('proveedor_id', $repuesto->proveedor_id)),
            fecha_ingreso: @json(old('fecha_ingreso', $repuesto->fecha_ingreso))
        },
        // Inicializa la preview si existe una imagen
        imagePreview: @json($repuesto->imagen ? asset('storage/' . $repuesto->imagen) : null),
        imageName: @json($repuesto->imagen ? basename($repuesto->imagen) : ''),
        isSubmitting: false,
        
        get totalValue() {
            const cantidad = parseFloat(this.form.cantidad) || 0;
            const precio = parseFloat(this.form.precio_unitario) || 0;
            return cantidad * precio;
        },
        
        calculateTotal() {
            this.$nextTick(() => { /* El getter totalValue se actualiza solo */ });
        },
        
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                this.processImage(file);
            }
        },
        
        handleDrop(event, element) {
            element.classList.remove('dragover');
            const file = event.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                this.$refs.imageInput.files = event.dataTransfer.files;
                this.processImage(file);
            }
        },
        
        processImage(file) {
            if (file.size > 5 * 1024 * 1024) { // 5MB
                this.showError('La imagen debe ser menor a 5MB');
                return;
            }
            if (!file.type.startsWith('image/')) {
                this.showError('Por favor selecciona una imagen válida (PNG, JPG, JPEG)');
                return;
            }
            
            this.imageName = file.name;
            const reader = new FileReader();
            reader.onload = (e) => {
                this.imagePreview = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        
        removeImage() {
            this.imagePreview = null;
            this.imageName = '';
            this.$refs.imageInput.value = '';
            // Aquí podríamos añadir un input oculto para forzar la eliminación
            // pero la lógica del controlador actual (si no hay 'imagen' no hace nada) es más segura.
        },
        
        async submitForm(event) {
            if (this.isSubmitting) return;
            
            // Validaciones básicas (puedes añadir más)
            if (!this.form.nombre.trim() || !this.form.marca.trim() || !this.form.categoria_id) {
                this.showError('Por favor completa todos los campos obligatorios (*)');
                return;
            }
            
            const result = await Swal.fire({
                title: '¿Actualizar repuesto?',
                html: `
                    <div class="text-left space-y-2">
                        <p><strong>Nombre:</strong> ${this.form.nombre}</p>
                        <p><strong>Marca:</strong> ${this.form.marca}</p>
                        <p><strong>Valor total:</strong> S/ ${this.totalValue.toFixed(2)}</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Revisar',
                confirmButtonColor: '#218786',
                cancelButtonColor: '#6b7280'
            });
            
            if (!result.isConfirmed) return;
            
            this.isSubmitting = true;
            
            Swal.fire({
                title: 'Actualizando repuesto...',
                html: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            event.target.submit();
        },
        
        showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: message,
                confirmButtonColor: '#218786'
            });
        },
        
        init() {
            // Rellenar el campo de fecha si está vacío (aunque 'old' debería manejarlo)
            if (!this.form.fecha_ingreso) {
                this.form.fecha_ingreso = '{{ $repuesto->fecha_ingreso ? \Carbon\Carbon::parse($repuesto->fecha_ingreso)->format('Y-m-d') : '' }}';
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#218786'
        });
    @endif
    
    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Error de validación',
            html: '<ul class="text-left list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
            confirmButtonColor: '#218786'
        });
    @endif
});
</script>
@endpush

@endsection