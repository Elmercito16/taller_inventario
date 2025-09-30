@extends('layouts.app')

@section('title', isset($proveedor) ? 'Editar Proveedor' : 'Nuevo Proveedor')
@section('page-title', isset($proveedor) ? 'Editar Proveedor' : 'Registrar Nuevo Proveedor')
@section('page-description', isset($proveedor) ? 'Actualiza la información del proveedor' : 'Agrega un nuevo proveedor a tu base de datos')

@section('breadcrumbs')
<nav class="flex items-center space-x-2 text-sm text-gray-500">
    <a href="{{ route('dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
    <a href="{{ route('proveedores.index') }}" class="hover:text-primary-600 transition-colors">Proveedores</a>
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
    <span class="font-medium text-gray-900">{{ isset($proveedor) ? 'Editar' : 'Nuevo Proveedor' }}</span>
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
    
    /* Estilos para inputs mejorados */
    .input-group {
        position: relative;
    }
    
    .input-group input:focus + .input-icon,
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
    
    .input-icon.textarea-icon {
        top: 16px;
        transform: none;
    }
    
    /* Estados de validación */
    .input-valid {
        border-color: #10b981;
        background-color: #ecfdf5;
    }
    
    .input-invalid {
        border-color: #ef4444;
        background-color: #fef2f2;
    }
    
    /* Animación del botón de validación */
    .btn-validating {
        background: linear-gradient(90deg, #218786, #1d7874, #218786);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }
    
    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
    /* Tooltip personalizado */
    .tooltip {
        position: relative;
        display: inline-block;
    }
    
    .tooltip .tooltiptext {
        visibility: hidden;
        background-color: #1f2937;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px 10px;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        margin-left: -60px;
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 12px;
        white-space: nowrap;
    }
    
    .tooltip:hover .tooltiptext {
        visibility: visible;
        opacity: 1;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto" x-data="proveedorForm()">
    <!-- Header con información -->
    <div class="form-section bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-primary-500 to-primary-600 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ isset($proveedor) ? 'Editar Proveedor' : 'Registrar Nuevo Proveedor' }}
                    </h1>
                    <p class="text-gray-600 mt-1">
                        {{ isset($proveedor) ? 'Actualiza la información del proveedor existente' : 'Complete los datos para registrar un nuevo proveedor' }}
                    </p>
                </div>
            </div>
            
            @if(isset($proveedor))
                <div class="text-right text-sm text-gray-500">
                    <p>Creado: {{ $proveedor->created_at->format('d/m/Y') }}</p>
                    <p>Actualizado: {{ $proveedor->updated_at->diffForHumans() }}</p>
                </div>
            @endif
        </div>
    </div>

    <form action="{{ isset($proveedor) ? route('proveedores.update', $proveedor->id) : route('proveedores.store') }}" 
          method="POST" @submit.prevent="submitForm" class="space-y-6">
        @csrf
        @if(isset($proveedor))
            @method('PUT')
        @endif

        <!-- Sección: Información Básica -->
        <div class="form-section bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Información Básica</h2>
                    <p class="text-sm text-gray-600">Datos principales del proveedor</p>
                </div>
            </div>

            <!-- Nombre del Proveedor -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre del Proveedor <span class="text-red-500">*</span>
                </label>
                <div class="input-group">
                    <input type="text" 
                           id="nombre" 
                           name="nombre"
                           x-model="form.nombre"
                           @input="validateNombre"
                           value="{{ old('nombre', $proveedor->nombre ?? '') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('nombre') input-invalid @enderror"
                           placeholder="Ej: Repuestos García SAC"
                           required>
                    <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="mt-1 flex items-center" x-show="nombreMessage" x-transition>
                    <svg x-show="nombreValid" class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <svg x-show="!nombreValid && nombreMessage" class="w-4 h-4 text-red-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <p class="text-sm" :class="nombreValid ? 'text-green-600' : 'text-red-600'" x-text="nombreMessage"></p>
                </div>
                @error('nombre')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Sección: Información de Contacto -->
        <div class="form-section bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Información de Contacto</h2>
                    <p class="text-sm text-gray-600">Datos para comunicarse con el proveedor</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Correo Electrónico
                        </span>
                    </label>
                    <div class="input-group">
                        <input type="email" 
                               name="contacto"
                               x-model="form.contacto"
                               @input="validateEmail"
                               value="{{ old('contacto', $proveedor->contacto ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('contacto') input-invalid @enderror"
                               placeholder="correo@ejemplo.com">
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="mt-1 flex items-center" x-show="emailMessage" x-transition>
                        <svg x-show="emailValid" class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg x-show="!emailValid && emailMessage" class="w-4 h-4 text-orange-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-sm" :class="emailValid ? 'text-green-600' : 'text-orange-600'" x-text="emailMessage"></p>
                    </div>
                    @error('contacto')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Teléfono -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            Teléfono
                        </span>
                    </label>
                    <div class="input-group">
                        <input type="tel" 
                               name="telefono"
                               x-model="form.telefono"
                               @input="validatePhone"
                               value="{{ old('telefono', $proveedor->telefono ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('telefono') input-invalid @enderror"
                               placeholder="999 999 999"
                               maxlength="15">
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <div class="mt-1 flex items-center" x-show="phoneMessage" x-transition>
                        <svg x-show="phoneValid" class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <p class="text-sm text-green-600" x-text="phoneMessage"></p>
                    </div>
                    @error('telefono')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Sección: Información Adicional -->
        <div class="form-section bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Información Adicional</h2>
                    <p class="text-sm text-gray-600">Datos complementarios del proveedor</p>
                </div>
            </div>

            <!-- Dirección -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Dirección
                    </span>
                </label>
                <div class="input-group">
                    <textarea 
                        name="direccion"
                        x-model="form.direccion"
                        @input="countCharacters"
                        rows="3"
                        maxlength="250"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 resize-none @error('direccion') input-invalid @enderror"
                        placeholder="Dirección completa del proveedor">{{ old('direccion', $proveedor->direccion ?? '') }}</textarea>
                    <svg class="input-icon textarea-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div class="mt-1 text-xs text-gray-500 text-right" x-text="`${characterCount}/250 caracteres`"></div>
                @error('direccion')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Nota informativa -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-medium">Información importante</p>
                <p class="text-blue-700 mt-1">Los campos marcados con <span class="text-red-500">*</span> son obligatorios. Los datos de contacto ayudarán a mantener comunicación con el proveedor.</p>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex justify-between items-center bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center text-sm text-gray-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Presiona <kbd class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded">Ctrl+S</kbd> para guardar rápido</span>
            </div>
            
            <div class="flex items-center space-x-4">
                <a href="{{ route('proveedores.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cancelar
                </a>
                
                <button type="submit" 
                        :disabled="isSubmitting || !isFormValid"
                        :class="isSubmitting ? 'opacity-50 cursor-not-allowed' : 'hover:from-primary-600 hover:to-primary-700'"
                        class="px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-medium rounded-lg transition-all duration-200 flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg x-show="!isSubmitting" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <svg x-show="isSubmitting" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="isSubmitting ? 'Guardando...' : '{{ isset($proveedor) ? 'Actualizar Proveedor' : 'Guardar Proveedor' }}'"></span>
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function proveedorForm() {
    return {
        form: {
            nombre: '{{ old('nombre', $proveedor->nombre ?? '') }}',
            contacto: '{{ old('contacto', $proveedor->contacto ?? '') }}',
            telefono: '{{ old('telefono', $proveedor->telefono ?? '') }}',
            direccion: '{{ old('direccion', $proveedor->direccion ?? '') }}'
        },
        
        // Estados
        isSubmitting: false,
        nombreValid: false,
        nombreMessage: '',
        emailValid: false,
        emailMessage: '',
        phoneValid: false,
        phoneMessage: '',
        characterCount: '{{ old('direccion', $proveedor->direccion ?? '') }}'.length,
        
        // Validación Nombre
        validateNombre() {
            const nombre = this.form.nombre.trim();
            
            if (nombre.length === 0) {
                this.nombreValid = false;
                this.nombreMessage = '';
            } else if (nombre.length < 3) {
                this.nombreValid = false;
                this.nombreMessage = 'El nombre debe tener al menos 3 caracteres';
            } else if (nombre.length > 255) {
                this.nombreValid = false;
                this.nombreMessage = 'El nombre no puede exceder 255 caracteres';
            } else {
                this.nombreValid = true;
                this.nombreMessage = 'Nombre válido';
            }
        },
        
        // Validación Email
        validateEmail() {
            const email = this.form.contacto.trim();
            this.form.contacto = email;
            
            if (email.length === 0) {
                this.emailValid = false;
                this.emailMessage = '';
                return;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!emailRegex.test(email)) {
                this.emailValid = false;
                this.emailMessage = 'Formato de correo inválido';
            } else {
                this.emailValid = true;
                this.emailMessage = 'Correo válido';
            }
        },
        
        // Validación Teléfono
        validatePhone() {
            let phone = this.form.telefono.replace(/\D/g, '');
            if (phone.length > 15) phone = phone.substr(0, 15);
            this.form.telefono = phone;
            
            if (phone.length >= 7) {
                this.phoneValid = true;
                this.phoneMessage = 'Teléfono válido';
            } else {
                this.phoneValid = false;
                this.phoneMessage = '';
            }
        },
        
        // Contador de caracteres
        countCharacters() {
            this.characterCount = this.form.direccion.length;
        },
        
        // Validación del formulario
        get isFormValid() {
            return this.form.nombre.trim().length >= 3;
        },
        
        // Envío del formulario
        async submitForm(event) {
            if (this.isSubmitting) return;
            
            // Validaciones básicas
            if (!this.form.nombre.trim()) {
                this.showNotification('Por favor ingresa el nombre del proveedor', 'error');
                document.getElementById('nombre').focus();
                return;
            }
            
            if (this.form.nombre.trim().length < 3) {
                this.showNotification('El nombre debe tener al menos 3 caracteres', 'error');
                document.getElementById('nombre').focus();
                return;
            }
            
            // Validar email si está presente
            if (this.form.contacto && !this.emailValid) {
                this.showNotification('Por favor ingresa un correo electrónico válido', 'error');
                return;
            }
            
            // Mostrar confirmación
            const action = '{{ isset($proveedor) ? 'actualizar' : 'registrar' }}';
            
            let confirmHtml = `
                <div class="text-left space-y-2">
                    <p><strong>Nombre:</strong> ${this.form.nombre}</p>
            `;
            
            if (this.form.contacto) {
                confirmHtml += `<p><strong>Email:</strong> ${this.form.contacto}</p>`;
            }
            if (this.form.telefono) {
                confirmHtml += `<p><strong>Teléfono:</strong> ${this.form.telefono}</p>`;
            }
            if (this.form.direccion) {
                confirmHtml += `<p><strong>Dirección:</strong> ${this.form.direccion.substring(0, 50)}${this.form.direccion.length > 50 ? '...' : ''}</p>`;
            }
            
            confirmHtml += '</div>';
            
            const result = await Swal.fire({
                title: `¿${action === 'actualizar' ? 'Actualizar' : 'Registrar'} proveedor?`,
                html: confirmHtml,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: `Sí, ${action}`,
                cancelButtonText: 'Revisar',
                confirmButtonColor: '#218786',
                cancelButtonColor: '#6b7280',
                reverseButtons: true
            });
            
            if (!result.isConfirmed) return;
            
            this.isSubmitting = true;
            
            // Mostrar loading
            Swal.fire({
                title: `${action === 'actualizar' ? 'Actualizando' : 'Registrando'} proveedor...`,
                html: 'Por favor espera mientras procesamos la información',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Enviar formulario
            event.target.submit();
        },
        
        // Utilidades
        showNotification(message, type = 'info') {
            const config = {
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                text: message
            };
            
            switch (type) {
                case 'success':
                    config.icon = 'success';
                    config.background = '#f0fdf4';
                    config.color = '#166534';
                    break;
                case 'error':
                    config.icon = 'error';
                    config.background = '#fef2f2';
                    config.color = '#dc2626';
                    break;
                case 'warning':
                    config.icon = 'warning';
                    config.background = '#fffbeb';
                    config.color = '#d97706';
                    break;
                default:
                    config.icon = 'info';
                    config.background = '#f0f9ff';
                    config.color = '#1e40af';
            }
            
            Swal.fire(config);
        },
        
        // Inicialización
        init() {
            // Validar datos iniciales si existen
            if (this.form.nombre) {
                this.validateNombre();
            }
            if (this.form.contacto) {
                this.validateEmail();
            }
            if (this.form.telefono) {
                this.validatePhone();
            }
            
            // Auto-save en localStorage (opcional)
            this.$watch('form', (value) => {
                if (!{{ isset($proveedor) ? 'true' : 'false' }}) {
                    localStorage.setItem('proveedor_draft', JSON.stringify(value));
                }
            }, { deep: true });
            
            // Recuperar borrador si no está editando
            @if(!isset($proveedor))
            const draft = localStorage.getItem('proveedor_draft');
            if (draft) {
                try {
                    const parsedDraft = JSON.parse(draft);
                    if (parsedDraft.nombre || parsedDraft.contacto) {
                        Swal.fire({
                            title: 'Borrador encontrado',
                            text: '¿Deseas recuperar los datos del formulario anterior?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, recuperar',
                            cancelButtonText: 'Empezar nuevo',
                            confirmButtonColor: '#218786',
                            cancelButtonColor: '#6b7280',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.form = { ...this.form, ...parsedDraft };
                                // Actualizar inputs visuales
                                Object.keys(parsedDraft).forEach(key => {
                                    const input = document.querySelector(`[name="${key}"]`);
                                    if (input) input.value = parsedDraft[key];
                                });
                                this.validateNombre();
                                this.validateEmail();
                                this.validatePhone();
                                this.countCharacters();
                            } else {
                                localStorage.removeItem('proveedor_draft');
                            }
                        });
                    }
                } catch (e) {
                    localStorage.removeItem('proveedor_draft');
                }
            }
            @endif
            
            // Auto-focus en primer campo después de la animación
            setTimeout(() => {
                const firstInput = document.getElementById('nombre');
                if (firstInput && !firstInput.value) firstInput.focus();
            }, 600);
        }
    }
}

// Limpiar borrador cuando se guarda exitosamente
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        localStorage.removeItem('proveedor_draft');
        
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#218786',
            confirmButtonText: 'Aceptar',
            timer: 3000,
            timerProgressBar: true
        });
    @endif
    
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: "{{ session('error') }}",
            confirmButtonColor: '#218786',
            confirmButtonText: 'Aceptar'
        });
    @endif
    
    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Errores en el formulario',
            html: '<ul class="text-left list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
            confirmButtonColor: '#218786',
            confirmButtonText: 'Aceptar'
        });
    @endif
    
    // Formatear inputs en tiempo real
    const telefonoInput = document.querySelector('input[name="telefono"]');
    if (telefonoInput) {
        telefonoInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').substr(0, 15);
        });
    }
});

// Prevenir envío duplicado
let formSubmitted = false;
document.addEventListener('submit', function(e) {
    if (formSubmitted) {
        e.preventDefault();
        return false;
    }
    formSubmitted = true;
    
    // Resetear después de 5 segundos por si hay error
    setTimeout(() => {
        formSubmitted = false;
    }, 5000);
});

// Atajos de teclado
document.addEventListener('keydown', function(e) {
    // Ctrl + S para guardar
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        const form = document.querySelector('form');
        if (form) {
            form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
        }
    }
    
    // Escape para cancelar con confirmación
    if (e.key === 'Escape') {
        const inputs = document.querySelectorAll('input, textarea');
        let hasChanges = false;
        
        inputs.forEach(input => {
            if (input.value && input.value.trim() !== '') {
                hasChanges = true;
            }
        });
        
        if (hasChanges) {
            Swal.fire({
                title: '¿Salir sin guardar?',
                text: 'Los cambios no guardados se perderán',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, salir',
                cancelButtonText: 'Continuar editando',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#218786',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route("proveedores.index") }}';
                }
            });
        } else {
            window.location.href = '{{ route("proveedores.index") }}';
        }
    }
});

// Advertencia antes de salir si hay cambios
window.addEventListener('beforeunload', function (e) {
    const inputs = document.querySelectorAll('input, textarea');
    let hasChanges = false;
    
    inputs.forEach(input => {
        if (input.value && input.value.trim() !== '' && !formSubmitted) {
            hasChanges = true;
        }
    });
    
    if (hasChanges && !formSubmitted) {
        e.preventDefault();
        e.returnValue = '';
    }
});
</script>
@endpush

@endsection