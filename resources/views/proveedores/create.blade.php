@extends('layouts.app')

@section('title', 'Nuevo Proveedor')
@section('page-title', 'Registrar Nuevo Proveedor')
@section('page-description', 'Agrega un nuevo proveedor a tu base de datos')

@section('breadcrumbs')
<nav class="flex items-center space-x-2 text-sm text-gray-500">
    <a href="{{ route('dashboard') }}" class="hover:text-primary-600 transition-colors flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/></svg>
        Dashboard
    </a>
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
    <a href="{{ route('proveedores.index') }}" class="hover:text-primary-600 transition-colors">Proveedores</a>
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
    <span class="font-medium text-gray-900">Nuevo Proveedor</span>
</nav>
@endsection

@push('styles')
{{-- Dejamos solo las animaciones, que no afectan el layout responsive --}}
<style>
    .form-section {
        animation: slideUp 0.5s ease-out;
        animation-fill-mode: both;
    }
    .form-section:nth-child(1) { animation-delay: 0.1s; }
    .form-section:nth-child(2) { animation-delay: 0.2s; }
    .form-section:nth-child(3) { animation-delay: 0.3s; }
    
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Estados de validación (los mantenemos) */
    .input-valid {
        border-color: #10b981; /* green-500 */
        background-color: #ecfdf5; /* green-50 */
    }
    .input-invalid {
        border-color: #ef4444; /* red-500 */
        background-color: #fef2f2; /* red-50 */
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto" x-data="proveedorForm()">
    
    <div class="form-section bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-gradient-to-r from-primary-500 to-primary-600 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">
                    Registrar Nuevo Proveedor
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    Complete los datos para registrar un nuevo proveedor
                </p>
            </div>
        </div>
    </div>

    {{-- El <form> envuelve TODAS las secciones, incluyendo los botones --}}
    <form action="{{ route('proveedores.store') }}" 
          method="POST" @submit.prevent="submitForm" class="space-y-6">
        @csrf

        <div class="form-section bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Información Básica</h2>
                        <p class="text-sm text-gray-600">Datos principales del proveedor</p>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre del Proveedor <span class="text-red-500">*</span>
                </label>
                {{-- Usamos 'relative' de Tailwind en lugar de 'input-group' --}}
                <div class="relative">
                    <input type="text" 
                           id="nombre" 
                           name="nombre"
                           x-model="form.nombre"
                           @input="validateNombre"
                           value="{{ old('nombre', '') }}"
                           class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('nombre') input-invalid @enderror"
                           placeholder="Ej: Repuestos García SAC"
                           required>
                    {{-- Usamos 'absolute' de Tailwind en lugar de 'input-icon' --}}
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
                {{-- Mensajes de validación de Alpine.js --}}
                <div class="mt-1 flex items-center" x-show="nombreMessage" x-transition>
                    <svg x-show="nombreValid" class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <svg x-show="!nombreValid && nombreMessage" class="w-4 h-4 text-red-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <p class="text-sm" :class="nombreValid ? 'text-green-600' : 'text-red-600'" x-text="nombreMessage"></p>
                </div>
                {{-- Mensaje de validación de Laravel --}}
                @error('nombre')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="form-section bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
             <div class="p-4 sm:p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Información de Contacto</h2>
                        <p class="text-sm text-gray-600">Datos para comunicarse con el proveedor</p>
                    </div>
                </div>
            </div>

            {{-- Grid responsive para los campos de contacto --}}
            <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contacto / Email</label>
                    <div class="relative">
                        <input type="email" 
                               name="contacto"
                               x-model="form.contacto"
                               @input="validateEmail"
                               value="{{ old('contacto', '') }}"
                               class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('contacto') input-invalid @enderror"
                               placeholder="correo@ejemplo.com">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-1 flex items-center" x-show="emailMessage" x-transition>
                        <svg x-show="emailValid" class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <svg x-show="!emailValid && emailMessage" class="w-4 h-4 text-orange-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <p class="text-sm" :class="emailValid ? 'text-green-600' : 'text-orange-600'" x-text="emailMessage"></p>
                    </div>
                    @error('contacto')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                    <div class="relative">
                        <input type="tel" 
                               name="telefono"
                               x-model="form.telefono"
                               @input="validatePhone"
                               value="{{ old('telefono', '') }}"
                               class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('telefono') input-invalid @enderror"
                               placeholder="999 999 999"
                               maxlength="15">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-1 flex items-center" x-show="phoneMessage" x-transition>
                        <svg x-show="phoneValid" class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <p class="text-sm text-green-600" x-text="phoneMessage"></p>
                    </div>
                    @error('telefono')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-section bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
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
            </div>

            <div class="p-4 sm:p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                <div class="relative">
                    <textarea 
                        name="direccion"
                        x-model="form.direccion"
                        @input="countCharacters"
                        rows="3"
                        maxlength="250"
                        class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 resize-none @error('direccion') input-invalid @enderror"
                        placeholder="Dirección completa del proveedor">{{ old('direccion', '') }}</textarea>
                    <div class="absolute top-0 right-0 pt-3 pr-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-1 text-xs text-gray-500 text-right" x-text="`${characterCount}/250 caracteres`"></div>
                @error('direccion')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="form-section bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div class="hidden sm:flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Presiona <kbd class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded">Ctrl+S</kbd> para guardar</span>
                </div>
                
                <div class="flex items-center space-x-4 w-full sm:w-auto">
                    <a href="{{ route('proveedores.index') }}" 
                       class="inline-flex justify-center w-full sm:w-auto px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        Cancelar
                    </a>
                    
                    <button type="submit" 
                            :disabled="isSubmitting || !isFormValid"
                            :class="isSubmitting ? 'opacity-50 cursor-not-allowed' : 'hover:from-primary-600 hover:to-primary-700'"
                            class="inline-flex justify-center w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-medium rounded-lg transition-all duration-200 flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg x-show="!isSubmitting" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <svg x-show="isSubmitting" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isSubmitting ? 'Guardando...' : 'Guardar Proveedor'"></span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
{{-- SweetAlert2 se carga desde app.blade.php y maneja los mensajes de sesión --}}
<script>
function proveedorForm() {
    return {
        // Valores iniciales para 'create'
        form: {
            nombre: '{{ old('nombre', '') }}',
            contacto: '{{ old('contacto', '') }}',
            telefono: '{{ old('telefono', '') }}',
            direccion: '{{ old('direccion', '') }}'
        },
        
        isSubmitting: false,
        nombreValid: false,
        nombreMessage: '',
        emailValid: false,
        emailMessage: '',
        phoneValid: false,
        phoneMessage: '',
        characterCount: '{{ old('direccion', '') }}'.length,
        
        // Validación Nombre
        validateNombre() {
            const nombre = this.form.nombre.trim();
            if (nombre.length === 0) {
                this.nombreValid = false; this.nombreMessage = '';
            } else if (nombre.length < 3) {
                this.nombreValid = false; this.nombreMessage = 'El nombre debe tener al menos 3 caracteres';
            } else {
                this.nombreValid = true; this.nombreMessage = 'Nombre válido';
            }
        },
        
        // Validación Email
        validateEmail() {
            const email = this.form.contacto.trim();
            this.form.contacto = email;
            if (email.length === 0) {
                this.emailValid = true; this.emailMessage = ''; return; // Email es opcional
            }
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                this.emailValid = false; this.emailMessage = 'Formato de correo inválido';
            } else {
                this.emailValid = true; this.emailMessage = 'Correo válido';
            }
        },
        
        // Validación Teléfono
        validatePhone() {
            let phone = this.form.telefono.replace(/\D/g, '');
            if (phone.length > 15) phone = phone.substr(0, 15);
            this.form.telefono = phone;
            if (phone.length > 0 && phone.length < 7) {
                this.phoneValid = false; this.phoneMessage = 'Teléfono inválido';
            } else {
                this.phoneValid = true; this.phoneMessage = phone.length >= 7 ? 'Teléfono válido' : '';
            }
        },
        
        countCharacters() {
            this.characterCount = this.form.direccion.length;
        },
        
        // El formulario es válido si el nombre tiene 3+ caracteres
        get isFormValid() {
            return this.form.nombre.trim().length >= 3;
        },
        
        // Envío del formulario
        async submitForm(event) {
            if (this.isSubmitting) return;
            
            // Re-validar todo por si acaso
            this.validateNombre();
            this.validateEmail();
            
            if (!this.isFormValid) {
                this.showNotification('Por favor ingresa un nombre válido (mín. 3 caracteres)', 'error');
                document.getElementById('nombre').focus();
                return;
            }
            
            if (this.form.contacto && !this.emailValid) {
                this.showNotification('Por favor ingresa un correo electrónico válido', 'error');
                return;
            }
            
            // Confirmación de SweetAlert
            let confirmHtml = `<div class="text-left space-y-2"><p><strong>Nombre:</strong> ${this.form.nombre}</p>`;
            if (this.form.contacto) { confirmHtml += `<p><strong>Email:</strong> ${this.form.contacto}</p>`; }
            if (this.form.telefono) { confirmHtml += `<p><strong>Teléfono:</strong> ${this.form.telefono}</p>`; }
            confirmHtml += '</div>';
            
            const result = await Swal.fire({
                title: '¿Registrar este proveedor?',
                html: confirmHtml,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, registrar',
                cancelButtonText: 'Revisar',
                confirmButtonColor: '#218786',
                cancelButtonColor: '#6b7280',
                reverseButtons: true
            });
            
            if (!result.isConfirmed) return;
            
            this.isSubmitting = true;
            
            Swal.fire({
                title: 'Registrando proveedor...',
                html: 'Por favor espera...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            
            // Limpiar el borrador antes de enviar
            localStorage.removeItem('proveedor_draft');
            event.target.submit();
        },
        
        // Notificación de toast
        showNotification(message, type = 'info') {
            const config = {
                toast: true, position: 'top-end', showConfirmButton: false,
                timer: 4000, timerProgressBar: true, text: message, icon: type
            };
            if (type === 'error') { config.background = '#fef2f2'; config.color = '#dc2626'; }
            Swal.fire(config);
        },
        
        // Inicialización del componente Alpine
        init() {
            // Lógica para recuperar borrador
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
                                // Re-validar campos recuperados
                                this.validateNombre();
                                this.validateEmail();
                                this.validatePhone();
                                this.countCharacters();
                            } else {
                                localStorage.removeItem('proveedor_draft');
                            }
                        });
                    }
                } catch (e) { localStorage.removeItem('proveedor_draft'); }
            }
            
            // Auto-save en localStorage
            this.$watch('form', (value) => {
                localStorage.setItem('proveedor_draft', JSON.stringify(value));
            }, { deep: true });
            
            // Auto-focus en primer campo
            setTimeout(() => {
                const firstInput = document.getElementById('nombre');
                if (firstInput && !firstInput.value) firstInput.focus();
            }, 600);
        }
    }
}

// Limpiar borrador si el guardado fue exitoso (esto es global, se ejecuta si la página recarga con un mensaje de éxito)
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        localStorage.removeItem('proveedor_draft');
    @endif
});

// Prevenir envío duplicado
let formSubmitted = false;
document.addEventListener('submit', function(e) {
    if (formSubmitted) {
        e.preventDefault();
        return false;
    }
    formSubmitted = true;
    setTimeout(() => { formSubmitted = false; }, 5000); // Resetear por si falla la validación
});

// Atajos de teclado
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        const form = document.querySelector('form');
        if (form) {
            // Disparar el evento 'submit' de Alpine.js, no el del form
            const alpineComponent = form.closest('[x-data]');
            if (alpineComponent && alpineComponent.__x) {
                alpineComponent.__x.dataStack[0].submitForm(new Event('submit'));
            } else {
                form.submit(); // Fallback por si Alpine falla
            }
        }
    }
});
</script>
@endpush