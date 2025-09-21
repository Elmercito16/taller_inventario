@extends('layouts.app')

@section('title', isset($cliente) ? 'Editar Cliente' : 'Nuevo Cliente')
@section('page-title', isset($cliente) ? 'Editar Cliente' : 'Registrar Nuevo Cliente')
@section('page-description', isset($cliente) ? 'Actualiza la información del cliente' : 'Agrega un nuevo cliente a tu base de datos')

@section('breadcrumbs')
<nav class="flex items-center space-x-2 text-sm text-gray-500">
    <a href="{{ route('dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
    <a href="{{ route('clientes.index') }}" class="hover:text-primary-600 transition-colors">Clientes</a>
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
    <span class="font-medium text-gray-900">{{ isset($cliente) ? 'Editar' : 'Nuevo Cliente' }}</span>
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
    .input-group select:focus + .input-icon {
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
    
    /* Estados de validación */
    .input-valid {
        border-color: #10b981;
        background-color: #ecfdf5;
    }
    
    .input-invalid {
        border-color: #ef4444;
        background-color: #fef2f2;
    }
    
    /* Animación del botón de búsqueda */
    .btn-searching {
        background: linear-gradient(90deg, #218786, #1d7874, #218786);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }
    
    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto" x-data="clienteForm()">
    <!-- Header con información -->
    <div class="form-section bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-primary-500 to-primary-600 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ isset($cliente) ? 'Editar Cliente' : 'Registrar Nuevo Cliente' }}
                    </h1>
                    <p class="text-gray-600 mt-1">
                        {{ isset($cliente) ? 'Actualiza la información del cliente existente' : 'Complete los datos para registrar un nuevo cliente' }}
                    </p>
                </div>
            </div>
            
            @if(isset($cliente))
                <div class="text-right text-sm text-gray-500">
                    <p>Creado: {{ $cliente->created_at->format('d/m/Y') }}</p>
                    <p>Actualizado: {{ $cliente->updated_at->diffForHumans() }}</p>
                </div>
            @endif
        </div>
    </div>

    <form action="{{ isset($cliente) ? route('clientes.update', $cliente->id) : route('clientes.store') }}" 
          method="POST" @submit.prevent="submitForm" class="space-y-6">
        @csrf
        @if(isset($cliente))
            @method('PUT')
        @endif

        <!-- Sección: Información de Identidad -->
        <div class="form-section bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 011-1h2a2 2 0 011 1v2m-4 0a2 2 0 01-1 1h-2a2 2 0 01-1-1m0 0V4a2 2 0 011-1h2a2 2 0 011 1v2"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Información de Identidad</h2>
                    <p class="text-sm text-gray-600">Datos principales del cliente</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- DNI -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Documento Nacional de Identidad (DNI) <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-3">
                        <div class="input-group flex-1">
                            <input type="text" 
                                   id="dni" 
                                   name="dni" 
                                   x-model="form.dni"
                                   @input="validateDni"
                                   maxlength="8"
                                   pattern="[0-9]{8}"
                                   value="{{ old('dni', $cliente->dni ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('dni') input-invalid @enderror"
                                   placeholder="Ingrese DNI (8 dígitos)"
                                   required>
                            <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 011-1h2a2 2 0 011 1v2m-4 0a2 2 0 01-1 1h-2a2 2 0 01-1-1m0 0V4a2 2 0 011-1h2a2 2 0 011 1v2"/>
                            </svg>
                        </div>
                        <button type="button" 
                                @click="buscarDni"
                                :disabled="!dniValid || isSearching"
                                :class="isSearching ? 'btn-searching' : ''"
                                class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-medium rounded-lg hover:from-green-600 hover:to-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center">
                            <svg x-show="!isSearching" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <svg x-show="isSearching" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isSearching ? 'Buscando...' : 'Buscar RENIEC'"></span>
                        </button>
                    </div>
                    <div class="mt-1 flex items-center" x-show="dniMessage" x-transition>
                        <svg x-show="dniValid" class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg x-show="!dniValid && dniMessage" class="w-4 h-4 text-red-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <p class="text-sm" :class="dniValid ? 'text-green-600' : 'text-red-600'" x-text="dniMessage"></p>
                    </div>
                    @error('dni')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nombre -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre Completo <span class="text-red-500">*</span>
                    </label>
                    <div class="input-group">
                        <input type="text" 
                               id="nombre" 
                               name="nombre"
                               x-model="form.nombre"
                               value="{{ old('nombre', $cliente->nombre ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('nombre') input-invalid @enderror"
                               placeholder="Nombres y apellidos completos"
                               required>
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    @error('nombre')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
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
                    <p class="text-sm text-gray-600">Medios para comunicarse con el cliente</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Teléfono -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Teléfono <span class="text-red-500">*</span>
                    </label>
                    <div class="input-group">
                        <input type="tel" 
                               name="telefono"
                               x-model="form.telefono"
                               @input="validatePhone"
                               value="{{ old('telefono', $cliente->telefono ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('telefono') input-invalid @enderror"
                               placeholder="Ej: 987654321"
                               maxlength="9"
                               pattern="[0-9]{9}"
                               required>
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    @error('telefono')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Correo Electrónico
                    </label>
                    <div class="input-group">
                        <input type="email" 
                               name="email"
                               x-model="form.email"
                               @input="validateEmail"
                               value="{{ old('email', $cliente->email ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('email') input-invalid @enderror"
                               placeholder="ejemplo@correo.com">
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    @error('email')
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
                    <p class="text-sm text-gray-600">Datos complementarios del cliente</p>
                </div>
            </div>

            <!-- Dirección -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Dirección
                </label>
                <div class="input-group">
                    <input type="text" 
                           name="direccion"
                           x-model="form.direccion"
                           value="{{ old('direccion', $cliente->direccion ?? '') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('direccion') input-invalid @enderror"
                           placeholder="Ingrese dirección completa">
                    <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                @error('direccion')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
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
                <a href="{{ route('clientes.index') }}" 
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
                    <span x-text="isSubmitting ? 'Guardando...' : '{{ isset($cliente) ? 'Actualizar Cliente' : 'Guardar Cliente' }}'"></span>
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function clienteForm() {
    return {
        form: {
            dni: '{{ old('dni', $cliente->dni ?? '') }}',
            nombre: '{{ old('nombre', $cliente->nombre ?? '') }}',
            telefono: '{{ old('telefono', $cliente->telefono ?? '') }}',
            email: '{{ old('email', $cliente->email ?? '') }}',
            direccion: '{{ old('direccion', $cliente->direccion ?? '') }}'
        },
        
        // Estados
        isSearching: false,
        isSubmitting: false,
        dniValid: false,
        dniMessage: '',
        
        // Validación DNI
        validateDni() {
            const dni = this.form.dni.replace(/\D/g, '');
            this.form.dni = dni;
            
            if (dni.length === 0) {
                this.dniValid = false;
                this.dniMessage = '';
            } else if (dni.length < 8) {
                this.dniValid = false;
                this.dniMessage = 'El DNI debe tener 8 dígitos';
            } else if (dni.length === 8) {
                this.dniValid = this.validateDniAlgorithm(dni);
                this.dniMessage = this.dniValid ? 'DNI válido' : 'DNI no válido';
            }
        },
        
        // Algoritmo de validación DNI peruano
        validateDniAlgorithm(dni) {
            if (dni.length !== 8) return false;
            
            // Verificar que no todos los dígitos sean iguales
            if (/^(.)\1{7}$/.test(dni)) return false;
            
            // Verificar que no empiece con 0
            if (dni[0] === '0') return false;
            
            return true;
        },
        
        // Validación teléfono
        validatePhone() {
            let phone = this.form.telefono.replace(/\D/g, '');
            if (phone.length > 9) phone = phone.substr(0, 9);
            this.form.telefono = phone;
        },
        
        // Validación email
        validateEmail() {
            // Limpiar espacios
            this.form.email = this.form.email.trim();
        },
        
        // Buscar en RENIEC
        async buscarDni() {
            if (!this.dniValid || this.isSearching) return;
            
            this.isSearching = true;
            
            try {
                const response = await fetch(`/clientes/buscar-dni/${this.form.dni}`);
                const data = await response.json();
                
                if (data && data.nombres) {
                    const nombreCompleto = `${data.nombres} ${data.apellidoPaterno || ''} ${data.apellidoMaterno || ''}`.trim();
                    this.form.nombre = nombreCompleto;
                    
                    // Actualizar el input visual
                    document.getElementById('nombre').value = nombreCompleto;
                    
                    this.showNotification('Información encontrada en RENIEC', 'success');
                } else {
                    this.showNotification(data.error || 'No se encontró información para el DNI ingresado', 'warning');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showNotification('Error al consultar RENIEC. Inténtalo de nuevo.', 'error');
            } finally {
                this.isSearching = false;
            }
        },
        
        // Envío del formulario
        async submitForm(event) {
            if (this.isSubmitting) return;
            
            // Validaciones básicas
            if (!this.form.dni || !this.dniValid) {
                this.showNotification('Por favor ingresa un DNI válido', 'error');
                return;
            }
            
            if (!this.form.nombre.trim()) {
                this.showNotification('Por favor ingresa el nombre completo', 'error');
                return;
            }
            
            if (!this.form.telefono.trim()) {
                this.showNotification('Por favor ingresa el teléfono', 'error');
                return;
            }
            
            // Mostrar confirmación
            const action = '{{ isset($cliente) ? 'actualizar' : 'registrar' }}';
            const result = await Swal.fire({
                title: `¿${action === 'actualizar' ? 'Actualizar' : 'Registrar'} cliente?`,
                html: `
                    <div class="text-left space-y-2">
                        <p><strong>DNI:</strong> ${this.form.dni}</p>
                        <p><strong>Nombre:</strong> ${this.form.nombre}</p>
                        <p><strong>Teléfono:</strong> ${this.form.telefono}</p>
                        ${this.form.email ? `<p><strong>Email:</strong> ${this.form.email}</p>` : ''}
                        ${this.form.direccion ? `<p><strong>Dirección:</strong> ${this.form.direccion}</p>` : ''}
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: `Sí, ${action}`,
                cancelButtonText: 'Revisar',
                confirmButtonColor: '#218786',
                cancelButtonColor: '#6b7280'
            });
            
            if (!result.isConfirmed) return;
            
            this.isSubmitting = true;
            
            // Mostrar loading
            Swal.fire({
                title: `${action === 'actualizar' ? 'Actualizando' : 'Registrando'} cliente...`,
                html: 'Por favor espera mientras procesamos la información',
                allowOutsideClick: false,
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
            // Validar DNI inicial si existe
            if (this.form.dni) {
                this.validateDni();
            }
            
            // Auto-save en localStorage (opcional)
            this.$watch('form', (value) => {
                if (!{{ isset($cliente) ? 'true' : 'false' }}) {
                    localStorage.setItem('cliente_draft', JSON.stringify(value));
                }
            }, { deep: true });
            
            // Recuperar borrador si no está editando
            @if(!isset($cliente))
            const draft = localStorage.getItem('cliente_draft');
            if (draft) {
                try {
                    const parsedDraft = JSON.parse(draft);
                    if (parsedDraft.dni || parsedDraft.nombre) {
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
                                // Actualizar inputs visuales
                                Object.keys(parsedDraft).forEach(key => {
                                    const input = document.querySelector(`[name="${key}"]`);
                                    if (input) input.value = parsedDraft[key];
                                });
                                this.validateDni();
                            } else {
                                localStorage.removeItem('cliente_draft');
                            }
                        });
                    }
                } catch (e) {
                    localStorage.removeItem('cliente_draft');
                }
            }
            @endif
        }
    }
}

// Limpiar borrador cuando se guarda exitosamente
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        localStorage.removeItem('cliente_draft');
    @endif
    
    // Formatear inputs en tiempo real
    const dniInput = document.getElementById('dni');
    if (dniInput) {
        dniInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').substr(0, 8);
        });
    }
    
    const telefonoInput = document.querySelector('input[name="telefono"]');
    if (telefonoInput) {
        telefonoInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').substr(0, 9);
        });
    }
    
    // Auto-focus en primer campo
    setTimeout(() => {
        const firstInput = document.getElementById('dni');
        if (firstInput && !firstInput.value) firstInput.focus();
    }, 300);
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
        document.querySelector('form').dispatchEvent(new Event('submit', { cancelable: true }));
    }
    
    // Escape para cancelar
    if (e.key === 'Escape') {
        const confirmExit = confirm('¿Estás seguro de que deseas salir? Los cambios no guardados se perderán.');
        if (confirmExit) {
            window.location.href = '{{ route("clientes.index") }}';
        }
    }
});
</script>
@endpush

@endsection