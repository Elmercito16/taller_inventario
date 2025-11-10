@extends('layouts.app')

@section('title', 'Editar Cliente')

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
    <span class="font-medium text-gray-900">Editar Cliente</span>
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
    
    .input-group input:focus + .input-icon {
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
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto" x-data="editClienteForm()">
    <!-- Header con información del cliente -->
    <div class="form-section bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-primary-500 to-primary-600 rounded-lg flex items-center justify-center mr-4">
                    <span class="text-white font-bold text-xl">{{ strtoupper(substr($cliente->nombre, 0, 1)) }}</span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Editar Cliente</h1>
                    <p class="text-gray-600 mt-1">Actualiza la información de {{ $cliente->nombre }}</p>
                </div>
            </div>
            
            <div class="text-right text-sm text-gray-500">
                <p><strong>DNI:</strong> {{ $cliente->dni }}</p>
                <p><strong>Registrado:</strong> {{ $cliente->created_at->format('d/m/Y') }}</p>
                <p><strong>Actualizado:</strong> {{ $cliente->updated_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>

    <!-- Alertas de validación -->
    @if ($errors->any())
        <div class="form-section bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <h3 class="text-sm font-semibold text-red-800">Errores de validación</h3>
            </div>
            <ul class="text-sm text-red-700 space-y-1">
                @foreach ($errors->all() as $error)
                    <li class="flex items-center">
                        <span class="w-1 h-1 bg-red-600 rounded-full mr-2"></span>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('clientes.update', $cliente->id) }}" 
          method="POST" 
          @submit.prevent="submitForm"
          class="space-y-6">
        @csrf
        @method('PUT')

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
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Documento Nacional de Identidad (DNI) <span class="text-red-500">*</span>
                    </label>
                    <div class="input-group">
                        <input type="text" 
                               name="dni" 
                               x-model="form.dni"
                               @input="validateDni"
                               maxlength="8"
                               pattern="[0-9]{8}"
                               value="{{ old('dni', $cliente->dni) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 pr-12 @error('dni') input-invalid @enderror"
                               placeholder="Ingrese DNI (8 dígitos)"
                               required>
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 011-1h2a2 2 0 011 1v2m-4 0a2 2 0 01-1 1h-2a2 2 0 01-1-1m0 0V4a2 2 0 011-1h2a2 2 0 011 1v2"/>
                        </svg>
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
                </div>

                <!-- Nombre -->
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre Completo <span class="text-red-500">*</span>
                    </label>
                    <div class="input-group">
                        <input type="text" 
                               name="nombre"
                               x-model="form.nombre"
                               value="{{ old('nombre', $cliente->nombre) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 pr-12 @error('nombre') input-invalid @enderror"
                               placeholder="Nombres y apellidos completos"
                               required>
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
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
                               value="{{ old('telefono', $cliente->telefono) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 pr-12 @error('telefono') input-invalid @enderror"
                               placeholder="Ej: 987654321"
                               maxlength="9"
                               pattern="[0-9]{9}"
                               required>
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
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
                               value="{{ old('email', $cliente->email) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 pr-12 @error('email') input-invalid @enderror"
                               placeholder="ejemplo@correo.com">
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
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
                           value="{{ old('direccion', $cliente->direccion) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 pr-12 @error('direccion') input-invalid @enderror"
                           placeholder="Ingrese dirección completa">
                    <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Resumen de cambios -->
        <div class="form-section bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-6" x-show="hasChanges">
            <div class="flex items-center mb-4">
                <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-semibold text-yellow-800">Cambios Detectados</h3>
            </div>
            <p class="text-yellow-700 text-sm">Se han detectado cambios en la información del cliente. Revisa los datos antes de guardar.</p>
        </div>

        <!-- Botones de acción -->
        <div class="flex justify-between items-center bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center text-sm text-gray-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Los campos marcados con <span class="text-red-500 font-medium">*</span> son obligatorios
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
    <!-- Botón Cancelar -->
    <a href="{{ route('clientes.index') }}" 
       class="flex items-center justify-center px-6 py-3 border border-[#1b8c72ff] text-[#1b8c72ff] font-medium rounded-lg bg-white hover:bg-[#1b8c7210] transition-all duration-200 w-full sm:w-auto">
        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        Cancelar
    </a>

    <!-- Botón Actualizar -->
    <button type="submit"
            :disabled="isSubmitting || !hasChanges"
            :class="isSubmitting || !hasChanges ? 'opacity-50 cursor-not-allowed' : 'hover:from-[#15745f] hover:to-[#0e5c4a]'}"
            class="flex items-center justify-center px-6 py-3 bg-gradient-to-r from-[#1b8c72ff] to-[#15745fff] text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md w-full sm:w-auto">
        <!-- Icono check -->
        <svg x-show="!isSubmitting" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>

        <!-- Spinner -->
        <svg x-show="isSubmitting" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>

        <span x-text="isSubmitting ? 'Actualizando...' : 'Actualizar Cliente'"></span>
    </button>
</div>

        </div>
    </form>
</div>

@push('scripts')
<script>
function editClienteForm() {
    return {
        // Formulario
        form: {
            dni: '{{ old('dni', $cliente->dni) }}',
            nombre: '{{ old('nombre', $cliente->nombre) }}',
            telefono: '{{ old('telefono', $cliente->telefono) }}',
            email: '{{ old('email', $cliente->email) }}',
            direccion: '{{ old('direccion', $cliente->direccion) }}'
        },
        
        // Valores originales para comparación
        originalForm: {
            dni: '{{ $cliente->dni }}',
            nombre: '{{ $cliente->nombre }}',
            telefono: '{{ $cliente->telefono }}',
            email: '{{ $cliente->email }}',
            direccion: '{{ $cliente->direccion }}'
        },
        
        // Estados
        isSubmitting: false,
        dniValid: true, // Asumimos que el DNI actual es válido
        dniMessage: '',
        
        // Computed
        get hasChanges() {
            return JSON.stringify(this.form) !== JSON.stringify(this.originalForm);
        },
        
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
            if (/^(.)\1{7}$/.test(dni)) return false; // No todos iguales
            if (dni[0] === '0') return false;         // No empiece con 0
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
            this.form.email = this.form.email.trim();
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
            
            if (!this.hasChanges) {
                this.showNotification('No se han detectado cambios para guardar', 'warning');
                return;
            }
            
            // Mostrar confirmación
            const result = await Swal.fire({
                title: '¿Actualizar cliente?',
                html: `
                    <div class="text-left space-y-2">
                        <p class="font-semibold mb-3">Se actualizarán los siguientes datos:</p>
                        ${this.form.dni !== this.originalForm.dni ? `<p><strong>DNI:</strong> ${this.originalForm.dni} → ${this.form.dni}</p>` : ''}
                        ${this.form.nombre !== this.originalForm.nombre ? `<p><strong>Nombre:</strong> ${this.originalForm.nombre} → ${this.form.nombre}</p>` : ''}
                        ${this.form.telefono !== this.originalForm.telefono ? `<p><strong>Teléfono:</strong> ${this.originalForm.telefono} → ${this.form.telefono}</p>` : ''}
                        ${this.form.email !== this.originalForm.email ? `<p><strong>Email:</strong> ${this.originalForm.email || 'N/A'} → ${this.form.email || 'N/A'}</p>` : ''}
                        ${this.form.direccion !== this.originalForm.direccion ? `<p><strong>Dirección:</strong> ${this.originalForm.direccion || 'N/A'} → ${this.form.direccion || 'N/A'}</p>` : ''}
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#218786',
                cancelButtonColor: '#6b7280'
            });
            
            if (!result.isConfirmed) return;
            
            this.isSubmitting = true;
            
            // Mostrar loading
            Swal.fire({
                title: 'Actualizando cliente...',
                html: 'Por favor espera mientras guardamos los cambios',
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
            // Validar DNI inicial
            this.validateDni();
            
            // Detectar cambios y mostrar advertencia al salir
            window.addEventListener('beforeunload', (e) => {
                if (this.hasChanges && !this.isSubmitting) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        }
    }
}

// Formatear inputs en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    // Formatear DNI
    const dniInput = document.querySelector('input[name="dni"]');
    if (dniInput) {
        dniInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').substr(0, 8);
        });
    }
    
    // Formatear teléfono
    const telefonoInput = document.querySelector('input[name="telefono"]');
    if (telefonoInput) {
        telefonoInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').substr(0, 9);
        });
    }
});

// Atajos de teclado
document.addEventListener('keydown', function(e) {
    // Ctrl + S para guardar
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        const form = document.querySelector('form');
        if (form) form.dispatchEvent(new Event('submit', { cancelable: true }));
    }
    
    // Escape para cancelar con confirmación si hay cambios
    if (e.key === 'Escape') {
        const alpineComponent = Alpine.getScope(document.querySelector('[x-data]'));
        if (alpineComponent && alpineComponent.hasChanges) {
            Swal.fire({
                title: '¿Descartar cambios?',
                text: 'Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, salir',
                cancelButtonText: 'Continuar editando',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route("clientes.index") }}';
                }
            });
        } else {
            window.location.href = '{{ route("clientes.index") }}';
        }
    }
});
</script>
@endpush

@endsection