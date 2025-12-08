@extends('layouts.app')

@section('title', 'Nueva Categoría')
@section('page-title', 'Registrar Nueva Categoría')
@section('page-description', 'Agrega una nueva categoría para clasificar tus repuestos')

@push('styles')
<style>
    .form-section {
        animation: slideUp 0.5s ease-out forwards;
    }
    
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
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto" x-data="categoriaCreateForm()">
    
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <a href="{{ route('categorias.index') }}" class="hover:text-primary-600 transition-colors">Categorías</a>
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <span class="font-medium text-gray-900">Nueva Categoría</span>
    </nav>

    <form action="{{ route('categorias.store') }}" method="POST" @submit.prevent="submitForm" class="space-y-6">
        @csrf

        <!-- Formulario Principal -->
        <div class="form-section bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-[#218786] to-[#1a6d6c] rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Información de la Categoría</h2>
                        <p class="text-sm text-gray-600">Complete los datos para crear la categoría</p>
                    </div>
                </div>
            </div>

            <!-- Contenido del Formulario -->
            <div class="p-6 space-y-6">
                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Nombre de la Categoría <span class="text-red-500">*</span>
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-hover:text-[#218786] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <input type="text" 
                               id="nombre" 
                               name="nombre"
                               x-model="form.nombre"
                               @input="validateNombre()"
                               value="{{ old('nombre') }}"
                               class="w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all hover:border-gray-300 @error('nombre') border-red-300 bg-red-50 @enderror"
                               placeholder="Ej: Neumáticos, Filtros, Lubricantes, Frenos"
                               required
                               autofocus>
                    </div>
                    
                    <!-- Mensaje de validación -->
                    <div class="mt-2" x-show="nombreMessage" x-transition>
                        <div class="flex items-center" :class="nombreValid ? 'text-green-600' : 'text-red-600'">
                            <svg x-show="nombreValid" class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <svg x-show="!nombreValid && nombreMessage" class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <p class="text-sm font-medium" x-text="nombreMessage"></p>
                        </div>
                    </div>
                    
                    @error('nombre')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Descripción -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Descripción (Opcional)
                    </label>
                    <div class="relative group">
                        <div class="absolute top-3 left-3 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-hover:text-[#218786] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                            </svg>
                        </div>
                        <textarea 
                            name="descripcion"
                            x-model="form.descripcion"
                            @input="countCharacters()"
                            rows="4"
                            maxlength="500"
                            class="w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all resize-none hover:border-gray-300 @error('descripcion') border-red-300 bg-red-50 @enderror"
                            placeholder="Descripción detallada de la categoría (opcional)">{{ old('descripcion') }}</textarea>
                    </div>
                    <div class="mt-2 flex justify-between items-center">
                        <p class="text-xs text-gray-500">Información adicional sobre esta categoría</p>
                        <p class="text-xs font-medium text-gray-600" x-text="`${characterCount}/500`"></p>
                    </div>
                    @error('descripcion')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Footer con botones -->
            <div class="px-6 py-5 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Presiona <kbd class="px-2 py-1 text-xs font-semibold text-gray-800 bg-white border border-gray-200 rounded shadow-sm">Ctrl+S</kbd> para guardar</span>
                </div>
                
                <div class="flex items-center space-x-3">
                    <a href="{{ route('categorias.index') }}" 
                       class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancelar
                    </a>
                    
                    <button type="submit" 
                            :disabled="isSubmitting || !isFormValid"
                            class="px-6 py-3 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-bold rounded-xl transition-all duration-200 flex items-center shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed hover:from-[#1d7874] hover:to-[#165b5c] transform hover:-translate-y-0.5">
                        <svg x-show="!isSubmitting" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg x-show="isSubmitting" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                            ircle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isSubmitting ? 'Guardando...' : 'Guardar Categoría'"></span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function categoriaCreateForm() {
    return {
        form: {
            nombre: '{{ old("nombre") }}',
            descripcion: '{{ old("descripcion") }}'
        },
        
        isSubmitting: false,
        nombreValid: false,
        nombreMessage: '',
        characterCount: '{{ old("descripcion") }}'.length || 0,
        
        validateNombre() {
            const nombre = this.form.nombre.trim();
            
            if (!nombre) {
                this.nombreValid = false;
                this.nombreMessage = '';
                return;
            }
            
            if (nombre.length < 2) {
                this.nombreValid = false;
                this.nombreMessage = 'El nombre debe tener al menos 2 caracteres';
                return;
            }
            
            if (nombre.length > 255) {
                this.nombreValid = false;
                this.nombreMessage = 'El nombre no puede exceder 255 caracteres';
                return;
            }
            
            this.nombreValid = true;
            this.nombreMessage = '✓ Nombre válido';
        },
        
        countCharacters() {
            this.characterCount = this.form.descripcion.length;
        },
        
        get isFormValid() {
            return this.form.nombre.trim().length >= 2;
        },
        
        async submitForm(event) {
            if (this.isSubmitting) return;
            
            const nombre = this.form.nombre.trim();
            
            if (!nombre) {
                this.showNotification('Por favor ingresa el nombre de la categoría', 'error');
                document.getElementById('nombre').focus();
                return;
            }
            
            if (nombre.length < 2) {
                this.showNotification('El nombre debe tener al menos 2 caracteres', 'error');
                document.getElementById('nombre').focus();
                return;
            }
            
            const result = await Swal.fire({
                title: '¿Registrar categoría?',
                html: `
                    <div class="text-left bg-gray-50 rounded-lg p-4 space-y-2">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-gray-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Nombre</p>
                                <p class="font-semibold text-gray-900">${this.form.nombre}</p>
                            </div>
                        </div>
                        ${this.form.descripcion ? `
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-gray-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Descripción</p>
                                <p class="text-gray-700">${this.form.descripcion}</p>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, registrar',
                cancelButtonText: 'Revisar',
                confirmButtonColor: '#218786',
                cancelButtonColor: '#6b7280',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl'
                }
            });
            
            if (!result.isConfirmed) return;
            
            this.isSubmitting = true;
            
            Swal.fire({
                title: 'Registrando categoría...',
                html: 'Por favor espera mientras procesamos la información',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            event.target.submit();
        },
        
        showNotification(message, type = 'info') {
            const config = {
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                text: message
            };
            
            switch (type) {
                case 'success':
                    config.icon = 'success';
                    config.background = '#e6f7f6';
                    config.color = '#134e4a';
                    break;
                case 'error':
                    config.icon = 'error';
                    config.background = '#fef2f2';
                    config.color = '#dc2626';
                    break;
                default:
                    config.icon = 'info';
                    config.background = '#f0f9ff';
                    config.color = '#1e40af';
            }
            
            Swal.fire(config);
        },
        
        init() {
            if (this.form.nombre) {
                this.validateNombre();
            }
            
            // Auto-guardar en localStorage
            this.$watch('form', (value) => {
                localStorage.setItem('categoria_draft', JSON.stringify(value));
            }, { deep: true });
            
            // Recuperar borrador
            const draft = localStorage.getItem('categoria_draft');
            if (draft && !this.form.nombre) {
                try {
                    const parsedDraft = JSON.parse(draft);
                    if (parsedDraft.nombre) {
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
                                this.form = parsedDraft;
                                document.getElementById('nombre').value = parsedDraft.nombre;
                                if (parsedDraft.descripcion) {
                                    document.querySelector('textarea[name="descripcion"]').value = parsedDraft.descripcion;
                                }
                                this.validateNombre();
                                this.countCharacters();
                            } else {
                                localStorage.removeItem('categoria_draft');
                            }
                        });
                    }
                } catch (e) {
                    localStorage.removeItem('categoria_draft');
                }
            }
        }
    }
}

// Mensajes de sesión
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        localStorage.removeItem('categoria_draft');
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#218786',
            confirmButtonText: 'Aceptar',
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: 'rounded-2xl'
            }
        });
    @endif
    
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: "{{ session('error') }}",
            confirmButtonColor: '#218786',
            confirmButtonText: 'Aceptar',
            customClass: {
                popup: 'rounded-2xl'
            }
        });
    @endif
    
    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Errores en el formulario',
            html: '<ul class="text-left list-disc pl-5 space-y-1">@foreach($errors->all() as $error)<li class="text-sm">{{ $error }}</li>@endforeach</ul>',
            confirmButtonColor: '#218786',
            confirmButtonText: 'Aceptar',
            customClass: {
                popup: 'rounded-2xl'
            }
        });
    @endif
});

// Prevenir envío múltiple
let formSubmitted = false;
document.addEventListener('submit', function(e) {
    if (formSubmitted) {
        e.preventDefault();
        return false;
    }
    formSubmitted = true;
    setTimeout(() => { formSubmitted = false; }, 5000);
});

// Atajo de teclado Ctrl+S
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        const form = document.querySelector('form');
        if (form) {
            const event = new Event('submit', { cancelable: true, bubbles: true });
            form.dispatchEvent(event);
        }
    }
});
</script>
@endpush

@endsection
