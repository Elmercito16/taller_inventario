@extends('layouts.app')

@section('title', 'Editar Proveedor')
@section('page-title', 'Editar Proveedor')
@section('page-description', 'Actualiza la información del proveedor')

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
    
    /* Badge de cambios */
    .changes-badge {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .5;
        }
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto" x-data="proveedorEditForm()">
    <!-- Header con información -->
    <div class="form-section bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-primary-500 to-primary-600 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Editar Proveedor</h1>
                    <p class="text-gray-600 mt-1">Actualiza la información del proveedor existente</p>
                </div>
            </div>
            
            <div class="text-right">
                <div class="text-sm text-gray-500 mb-2">
                    <p>Creado: {{ $proveedor->created_at->format('d/m/Y') }}</p>
                    <p>Actualizado: {{ $proveedor->updated_at->diffForHumans() }}</p>
                </div>
                <span x-show="hasChanges" class="changes-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    Cambios sin guardar
                </span>
            </div>
        </div>
    </div>

    <form action="{{ route('proveedores.update', $proveedor->id) }}" 
          method="POST" @submit.prevent="submitForm" class="space-y-6">
        @csrf
        @method('PUT')

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
                           @input="validateNombre(); detectChanges()"
                           value="{{ old('nombre', $proveedor->nombre) }}"
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
                               @input="validateEmail(); detectChanges()"
                               value="{{ old('contacto', $proveedor->contacto) }}"
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
                               @input="validatePhone(); detectChanges()"
                               value="{{ old('telefono', $proveedor->telefono) }}"
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
                        @input="countCharacters(); detectChanges()"
                        rows="3"
                        maxlength="250"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 resize-none @error('direccion') input-invalid @enderror"
                        placeholder="Dirección completa del proveedor">{{ old('direccion', $proveedor->direccion) }}</textarea>
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

        <!-- Comparación de cambios -->
        <div x-show="hasChanges" x-transition class="bg-amber-50 border border-amber-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-medium text-amber-800">Hay cambios sin guardar</p>
                    <p class="text-xs text-amber-700 mt-1">Los cambios que realices se guardarán al hacer clic en "Actualizar Proveedor"</p>
                </div>
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
                   @click="checkBeforeLeaving"
                   class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cancelar
                </a>
                
                <button type="submit" 
                        :disabled="isSubmitting || !isFormValid || !hasChanges"
                        :class="isSubmitting ? 'opacity-50 cursor-not-allowed' : 'hover:from-primary-600 hover:to-primary-700'"
                        class="px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-medium rounded-lg transition-all duration-200 flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg x-show="!isSubmitting" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <svg x-show="isSubmitting" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="isSubmitting ? 'Actualizando...' : 'Actualizar Proveedor'"></span>
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function proveedorEditForm() {
    return {
        // Datos originales
        originalData: {
            nombre: '{{ $proveedor->nombre }}',
            contacto: '{{ $proveedor->contacto }}',
            telefono: '{{ $proveedor->telefono }}',
            direccion: '{{ $proveedor->direccion }}'
        },
        
        // Formulario actual
        form: {
            nombre: '{{ old('nombre', $proveedor->nombre) }}',
            contacto: '{{ old('contacto', $proveedor->contacto) }}',
            telefono: '{{ old('telefono', $proveedor->telefono) }}',
            direccion: '{{ old('direccion', $proveedor->direccion) }}'
        },
        
        // Estados
        isSubmitting: false,
        hasChanges: false,
        nombreValid: true,
        nombreMessage: '',
        emailValid: true,
        emailMessage: '',
        phoneValid: true,
        phoneMessage: '',
        characterCount: '{{ old('direccion', $proveedor->direccion) }}'.length,
        
        // Detectar cambios
        detectChanges() {
            this.hasChanges = 
                this.form.nombre !== this.originalData.nombre ||
                this.form.contacto !== this.originalData.contacto ||
                this.form.telefono !== this.originalData.telefono ||
                this.form.direccion !== this.originalData.direccion;
        },
        
        // Validación Nombre
        validateNombre() {
            const nombre = this.form.nombre.trim();
            
            if (nombre.length === 0) {
                this.nombreValid = false;
                this.nombreMessage = 'El nombre es obligatorio';
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
                this.emailValid = true;
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
            return this.form.nombre.trim().length >= 3 && 
                   (this.form.contacto === '' || this.emailValid);
        },
        
        // Verificar antes de salir
        checkBeforeLeaving(event) {
            if (this.hasChanges) {
                event.preventDefault();
                Swal.fire({
                    title: '¿Salir sin guardar?',
                    text: 'Tienes cambios sin guardar que se perderán',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, salir',
                    cancelButtonText: 'Continuar editando',
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#218786',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = event.target.href;
                    }
                });
            }
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
            
            // Verificar que haya cambios
            if (!this.hasChanges) {
                this.showNotification('No hay cambios para guardar', 'info');
                return;
            }
            
            // Mostrar resumen de cambios
            let changesHtml = '<div class="text-left space-y-2 text-sm">';
            changesHtml += '<p class="font-semibold mb-2">Cambios realizados:</p>';
            
            if (this.form.nombre !== this.originalData.nombre) {
                changesHtml += `
                    <div class="bg-blue-50 p-2 rounded">
                        <p class="font-medium text-blue-800">Nombre:</p>
                        <p class="text-gray-500 line-through">${this.originalData.nombre}</p>
                        <p class="text-blue-600">→ ${this.form.nombre}</p>
                    </div>
                `;
            }
            
            if (this.form.contacto !== this.originalData.contacto) {
                changesHtml += `
                    <div class="bg-blue-50 p-2 rounded">
                        <p class="font-medium text-blue-800">Email:</p>
                        <p class="text-gray-500 line-through">${this.originalData.contacto || '(vacío)'}</p>
                        <p class="text-blue-600">→ ${this.form.contacto || '(vacío)'}</p>
                    </div>
                `;
            }
            
            if (this.form.telefono !== this.originalData.telefono) {
                changesHtml += `
                    <div class="bg-blue-50 p-2 rounded">
                        <p class="font-medium text-blue-800">Teléfono:</p>
                        <p class="text-gray-500 line-through">${this.originalData.telefono || '(vacío)'}</p>
                        <p class="text-blue-600">→ ${this.form.telefono || '(vacío)'}</p>
                    </div>
                `;
            }
            
            if (this.form.direccion !== this.originalData.direccion) {
                changesHtml += `
                    <div class="bg-blue-50 p-2 rounded">
                        <p class="font-medium text-blue-800">Dirección:</p>
                        <p class="text-gray-500 line-through text-xs">${this.originalData.direccion ? (this.originalData.direccion.substring(0, 40) + '...') : '(vacío)'}</p>
                        <p class="text-blue-600 text-xs">→ ${this.form.direccion ? (this.form.direccion.substring(0, 40) + '...') : '(vacío)'}</p>
                    </div>
                `;
            }
            
            changesHtml += '</div>';
            
            const result = await Swal.fire({
                title: '¿Actualizar proveedor?',
                html: changesHtml,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Revisar',
                confirmButtonColor: '#218786',
                cancelButtonColor: '#6b7280',
                reverseButtons: true,
                width: '600px'
            });
            
            if (!result.isConfirmed) return;
            
            this.isSubmitting = true;
            
            // Mostrar loading
            Swal.fire({
                title: 'Actualizando proveedor...',
                html: 'Por favor espera mientras guardamos los cambios',
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
            // Validar datos iniciales
            this.validateNombre();
            if (this.form.contacto) {
                this.validateEmail();
            }
            if (this.form.telefono) {
                this.validatePhone();
            }
            
            // Detectar cambios al cargar
            this.detectChanges();
            
            // Auto-focus en primer campo después de la animación
            setTimeout(() => {
                const firstInput = document.getElementById('nombre');
                if (firstInput) firstInput.focus();
            }, 600);
        }
    }
}

// Manejo de eventos del documento
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Actualizado exitosamente!',
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
            title: 'Error al actualizar',
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
});

// Advertencia antes de salir si hay cambios
window.addEventListener('beforeunload', function (e) {
    const alpineData = Alpine.$data(document.querySelector('[x-data]'));
    
    if (alpineData && alpineData.hasChanges && !formSubmitted) {
        e.preventDefault();
        e.returnValue = '';
    }
});
</script>
@endpush

@endsection