@extends('layouts.app')

@section('title', 'Nueva Categoría')
@section('page-title', 'Registrar Nueva Categoría')
@section('page-description', 'Agrega una nueva categoría para clasificar tus repuestos')

@section('breadcrumbs')
<nav class="flex items-center space-x-2 text-sm text-gray-500">
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
@endsection

@push('styles')
<style>
    .form-section {
        animation: slideUp 0.5s ease-out;
        animation-fill-mode: both;
    }
    
    .form-section:nth-child(1) { animation-delay: 0.1s; }
    .form-section:nth-child(2) { animation-delay: 0.2s; }
    
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
<div class="max-w-4xl mx-auto" x-data="categoriaCreateForm()">
    <!-- Header con información -->
    <div class="form-section bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-primary-500 to-primary-600 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Registrar Nueva Categoría</h1>
                    <p class="text-gray-600 mt-1">Complete los datos para crear una nueva categoría de repuestos</p>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('categorias.store') }}" 
          method="POST" @submit.prevent="submitForm" class="space-y-6">
        @csrf

        <!-- Sección: Información de la Categoría -->
        <div class="form-section bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Información de la Categoría</h2>
                    <p class="text-sm text-gray-600">Datos principales para clasificación</p>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre de la Categoría <span class="text-red-500">*</span>
                    </label>
                    <div class="input-group">
                        <input type="text" 
                               id="nombre" 
                               name="nombre"
                               x-model="form.nombre"
                               @input="validateNombre()"
                               value="{{ old('nombre') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('nombre') input-invalid @enderror"
                               placeholder="Ej: Neumáticos, Filtros, Lubricantes, Frenos"
                               required>
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
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

                <!-- Descripción -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                            </svg>
                            Descripción (Opcional)
                        </span>
                    </label>
                    <div class="input-group">
                        <textarea 
                            name="descripcion"
                            x-model="form.descripcion"
                            @input="countCharacters()"
                            rows="4"
                            maxlength="500"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 resize-none @error('descripcion') input-invalid @enderror"
                            placeholder="Descripción detallada de la categoría (opcional)">{{ old('descripcion') }}</textarea>
                        <svg class="input-icon textarea-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                    </div>
                    <div class="mt-1 flex justify-between items-center">
                        <p class="text-xs text-gray-500">Agrega información adicional sobre esta categoría</p>
                        <p class="text-xs text-gray-500" x-text="`${characterCount}/500 caracteres`"></p>
                    </div>
                    @error('descripcion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Ejemplos de categorías comunes -->
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-indigo-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-medium text-indigo-900">Ejemplos de categorías populares:</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <button type="button" @click="fillExample('Neumáticos')" class="px-3 py-1 bg-white border border-indigo-300 rounded-full text-xs text-indigo-700 hover:bg-indigo-100 transition-colors">Neumáticos</button>
                        <button type="button" @click="fillExample('Filtros')" class="px-3 py-1 bg-white border border-indigo-300 rounded-full text-xs text-indigo-700 hover:bg-indigo-100 transition-colors">Filtros</button>
                        <button type="button" @click="fillExample('Lubricantes')" class="px-3 py-1 bg-white border border-indigo-300 rounded-full text-xs text-indigo-700 hover:bg-indigo-100 transition-colors">Lubricantes</button>
                        <button type="button" @click="fillExample('Frenos')" class="px-3 py-1 bg-white border border-indigo-300 rounded-full text-xs text-indigo-700 hover:bg-indigo-100 transition-colors">Frenos</button>
                        <button type="button" @click="fillExample('Sistema Eléctrico')" class="px-3 py-1 bg-white border border-indigo-300 rounded-full text-xs text-indigo-700 hover:bg-indigo-100 transition-colors">Sistema Eléctrico</button>
                        <button type="button" @click="fillExample('Suspensión')" class="px-3 py-1 bg-white border border-indigo-300 rounded-full text-xs text-indigo-700 hover:bg-indigo-100 transition-colors">Suspensión</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información importante -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-medium">Información importante</p>
                <p class="text-blue-700 mt-1">Las categorías te ayudarán a organizar mejor tus repuestos. Puedes crear categorías según el tipo de producto, marca, uso o cualquier criterio que necesites.</p>
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
                <a href="{{ route('categorias.index') }}" 
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
                    <span x-text="isSubmitting ? 'Guardando...' : 'Guardar Categoría'"></span>
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function categoriaCreateForm() {
    return {
        form: {
            nombre: '{{ old('nombre') }}',
            descripcion: '{{ old('descripcion') }}'
        },
        
        isSubmitting: false,
        nombreValid: false,
        nombreMessage: '',
        characterCount: '{{ old('descripcion') }}'.length,
        
        validateNombre() {
            const nombre = this.form.nombre.trim();
            
            if (nombre.length === 0) {
                this.nombreValid = false;
                this.nombreMessage = '';
            } else if (nombre.length < 2) {
                this.nombreValid = false;
                this.nombreMessage = 'El nombre debe tener al menos 2 caracteres';
            } else if (nombre.length > 255) {
                this.nombreValid = false;
                this.nombreMessage = 'El nombre no puede exceder 255 caracteres';
            } else {
                this.nombreValid = true;
                this.nombreMessage = 'Nombre válido';
            }
        },
        
        countCharacters() {
            this.characterCount = this.form.descripcion.length;
        },
        
        fillExample(nombre) {
            this.form.nombre = nombre;
            document.getElementById('nombre').value = nombre;
            this.validateNombre();
            
            const descriptions = {
                'Neumáticos': 'Llantas y neumáticos para diferentes tipos de vehículos',
                'Filtros': 'Filtros de aire, aceite, combustible y cabina',
                'Lubricantes': 'Aceites, grasas y lubricantes automotrices',
                'Frenos': 'Pastillas, discos, tambores y componentes del sistema de frenos',
                'Sistema Eléctrico': 'Baterías, alternadores, motores de arranque y componentes eléctricos',
                'Suspensión': 'Amortiguadores, resortes y componentes del sistema de suspensión'
            };
            
            if (descriptions[nombre]) {
                this.form.descripcion = descriptions[nombre];
                document.querySelector('textarea[name="descripcion"]').value = descriptions[nombre];
                this.countCharacters();
            }
            
            this.showNotification(`Ejemplo "${nombre}" aplicado`, 'success');
        },
        
        get isFormValid() {
            return this.form.nombre.trim().length >= 2;
        },
        
        async submitForm(event) {
            if (this.isSubmitting) return;
            
            if (!this.form.nombre.trim()) {
                this.showNotification('Por favor ingresa el nombre de la categoría', 'error');
                document.getElementById('nombre').focus();
                return;
            }
            
            if (this.form.nombre.trim().length < 2) {
                this.showNotification('El nombre debe tener al menos 2 caracteres', 'error');
                document.getElementById('nombre').focus();
                return;
            }
            
            const result = await Swal.fire({
                title: '¿Registrar categoría?',
                html: `
                    <div class="text-left space-y-2">
                        <p><strong>Nombre:</strong> ${this.form.nombre}</p>
                        ${this.form.descripcion ? `<p><strong>Descripción:</strong> ${this.form.descripcion}</p>` : ''}
                    </div>
                `,
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
                    config.background = '#f0fdf4';
                    config.color = '#166534';
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
            
            this.$watch('form', (value) => {
                localStorage.setItem('categoria_draft', JSON.stringify(value));
            }, { deep: true });
            
            const draft = localStorage.getItem('categoria_draft');
            if (draft) {
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
                                this.form = { ...this.form, ...parsedDraft };
                                Object.keys(parsedDraft).forEach(key => {
                                    const input = document.querySelector(`[name="${key}"]`);
                                    if (input) input.value = parsedDraft[key];
                                });
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
            
            setTimeout(() => {
                const firstInput = document.getElementById('nombre');
                if (firstInput && !firstInput.value) firstInput.focus();
            }, 600);
        }
    }
}

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
});

let formSubmitted = false;
document.addEventListener('submit', function(e) {
    if (formSubmitted) {
        e.preventDefault();
        return false;
    }
    formSubmitted = true;
    setTimeout(() => { formSubmitted = false; }, 5000);
});

document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        const form = document.querySelector('form');
        if (form) {
            form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
        }
    }
});
</script>
@endpush

@endsection