@extends('layouts.app')

@section('title', 'Agregar Repuesto')
@section('page-title', 'Nuevo Repuesto')
@section('page-description', 'Registra un nuevo repuesto en el inventario')

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
    <span class="font-medium text-gray-900">Nuevo Repuesto</span>
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
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
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
    .input-group {
        position: relative;
    }
    
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
                <h1 class="text-2xl font-bold text-gray-900">Agregar Nuevo Repuesto</h1>
                <p class="text-gray-600 mt-1">Complete la información para registrar el repuesto en el inventario</p>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-primary-500 rounded-full"></div>
                <span class="text-sm font-medium text-gray-700">Código: <strong class="text-primary-600">{{ $nuevoCodigo }}</strong></span>
            </div>
        </div>
    </div>

    <form action="{{ route('repuestos.store') }}" method="POST" enctype="multipart/form-data" x-data="repuestoForm()" @submit.prevent="submitForm">
        @csrf
        
        <!-- Código oculto generado automáticamente -->
        <input type="hidden" name="codigo" value="{{ $nuevoCodigo }}">

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
                                  placeholder="Descripción detallada del repuesto, compatibilidades, especificaciones técnicas..."></textarea>
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
                        <p class="text-sm text-gray-600">Stock inicial y precio del repuesto</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Cantidad -->
                    <div class="input-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Cantidad Inicial <span class="text-red-500">*</span>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
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
                               value="0" 
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
                                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
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
                                <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
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
                               :value="today"
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
                        <p class="text-sm text-gray-600">Sube una foto del repuesto (opcional)</p>
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
                            <p class="text-lg font-medium text-gray-700">Arrastra y suelta una imagen aquí</p>
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
            <div class="flex justify-between items-center bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Los campos marcados con <span class="text-red-500 font-medium">*</span> son obligatorios
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('repuestos.index') }}" 
                       class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancelar
                    </a>
                    
                    <button type="submit" 
                            :disabled="isSubmitting"
                            :class="isSubmitting ? 'opacity-50 cursor-not-allowed' : 'hover:from-primary-600 hover:to-primary-700'"
                            class="px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-medium rounded-lg transition-all duration-200 flex items-center">
                        <svg x-show="!isSubmitting" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg x-show="isSubmitting" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isSubmitting ? 'Guardando...' : 'Guardar Repuesto'"></span>
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
            nombre: '',
            marca: '',
            descripcion: '',
            cantidad: '',
            precio_unitario: '',
            minimo_stock: 0,
            categoria_id: '',
            proveedor_id: '',
            fecha_ingreso: ''
        },
        imagePreview: null,
        imageName: '',
        isSubmitting: false,
        today: new Date().toISOString().substr(0, 10),
        
        get totalValue() {
            const cantidad = parseFloat(this.form.cantidad) || 0;
            const precio = parseFloat(this.form.precio_unitario) || 0;
            return cantidad * precio;
        },
        
        calculateTotal() {
            // Método para forzar la recalculación del total
            this.$nextTick(() => {
                // El getter totalValue se ejecutará automáticamente
            });
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
            // Validar tamaño (5MB)
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo muy grande',
                    text: 'La imagen debe ser menor a 5MB',
                    confirmButtonColor: '#218786'
                });
                return;
            }
            
            // Validar tipo
            if (!file.type.startsWith('image/')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Formato no válido',
                    text: 'Por favor selecciona una imagen válida (PNG, JPG, JPEG)',
                    confirmButtonColor: '#218786'
                });
                return;
            }
            
            this.imageName = file.name;
            
            // Crear preview
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
        },
        
        async submitForm(event) {
            if (this.isSubmitting) return;
            
            // Validaciones básicas
            if (!this.form.nombre.trim()) {
                this.showError('Por favor ingresa el nombre del repuesto');
                return;
            }
            
            if (!this.form.marca.trim()) {
                this.showError('Por favor ingresa la marca del repuesto');
                return;
            }
            
            if (!this.form.categoria_id) {
                this.showError('Por favor selecciona una categoría');
                return;
            }
            
            if (this.form.cantidad === '' || this.form.cantidad < 0) {
                this.showError('Por favor ingresa una cantidad válida');
                return;
            }
            
            if (this.form.precio_unitario === '' || this.form.precio_unitario <= 0) {
                this.showError('Por favor ingresa un precio válido');
                return;
            }
            
            // Mostrar confirmación
            const result = await Swal.fire({
                title: '¿Guardar repuesto?',
                html: `
                    <div class="text-left space-y-2">
                        <p><strong>Nombre:</strong> ${this.form.nombre}</p>
                        <p><strong>Marca:</strong> ${this.form.marca}</p>
                        <p><strong>Cantidad:</strong> ${this.form.cantidad} unidades</p>
                        <p><strong>Precio:</strong> S/ ${parseFloat(this.form.precio_unitario).toFixed(2)}</p>
                        <p><strong>Valor total:</strong> S/ ${this.totalValue.toFixed(2)}</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Revisar',
                confirmButtonColor: '#218786',
                cancelButtonColor: '#6b7280'
            });
            
            if (!result.isConfirmed) return;
            
            this.isSubmitting = true;
            
            // Mostrar loading
            Swal.fire({
                title: 'Guardando repuesto...',
                html: 'Por favor espera mientras procesamos la información',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Enviar formulario
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
            // Inicializar fecha de hoy
            this.form.fecha_ingreso = this.today;
            
            // Auto-save en localStorage (opcional)
            this.$watch('form', (value) => {
                localStorage.setItem('repuesto_draft', JSON.stringify(value));
            }, { deep: true });
            
            // Recuperar borrador si existe
            const draft = localStorage.getItem('repuesto_draft');
            if (draft) {
                try {
                    const parsedDraft = JSON.parse(draft);
                    // Solo recuperar si no está vacío
                    if (parsedDraft.nombre || parsedDraft.marca) {
                        Swal.fire({
                            title: 'Borrador encontrado',
                            text: '¿Deseas recuperar los datos del formulario anterior?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, recuperar',
                            cancelButtonText: 'Empezar nuevo',
                            confirmButtonColor: '#218786'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.form = { ...this.form, ...parsedDraft };
                            } else {
                                localStorage.removeItem('repuesto_draft');
                            }
                        });
                    }
                } catch (e) {
                    localStorage.removeItem('repuesto_draft');
                }
            }
        }
    }
}

// Limpiar borrador cuando se guarda exitosamente
document.addEventListener('DOMContentLoaded', function() {
    // Escuchar mensajes de éxito del servidor
    @if(session('success'))
        localStorage.removeItem('repuesto_draft');
    @endif
});

// Funciones de utilidad para mejorar UX
document.addEventListener('DOMContentLoaded', function() {
    // Auto-resize para textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    });
    
    // Formatear precio mientras se escribe
    const precioInput = document.querySelector('input[name="precio_unitario"]');
    if (precioInput) {
        precioInput.addEventListener('input', function() {
            let value = this.value;
            // Permitir solo números y punto decimal
            value = value.replace(/[^\d.]/g, '');
            // Permitir solo un punto decimal
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }
            // Limitar a 2 decimales
            if (parts[1] && parts[1].length > 2) {
                value = parts[0] + '.' + parts[1].substring(0, 2);
            }
            this.value = value;
        });
    }
    
    // Prevenir caracteres no numéricos en campos de cantidad
    const numberInputs = document.querySelectorAll('input[type="number"]:not([name="precio_unitario"])');
    numberInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            // Permitir: backspace, delete, tab, escape, enter
            if ([46, 8, 9, 27, 13].indexOf(e.keyCode) !== -1 ||
                // Permitir Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (e.keyCode === 65 && e.ctrlKey === true) ||
                (e.keyCode === 67 && e.ctrlKey === true) ||
                (e.keyCode === 86 && e.ctrlKey === true) ||
                (e.keyCode === 88 && e.ctrlKey === true)) {
                return;
            }
            // Asegurarse de que es un número
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush

@endsection