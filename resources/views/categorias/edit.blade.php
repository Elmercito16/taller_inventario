@extends('layouts.app')

@section('title', 'Editar Categoría')
@section('page-title', 'Editar Categoría')
@section('page-description', 'Modifica los datos de la categoría')

@push('styles')
<style>
    .form-section {
        animation: slideUp 0.5s ease-out both;
    }
    
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .input-group { position: relative; }
    .input-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        pointer-events: none;
    }
    .input-group textarea + .input-icon {
        top: 16px;
        transform: none;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto" x-data="categoriaEditForm()">
    <!-- Header -->
    <div class="form-section bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-gradient-to-r from-[#218786] to-[#1a6d6c] rounded-lg flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Categoría</h1>
                <p class="text-gray-600 mt-1">Actualiza los datos de: <strong class="text-[#218786]">{{ $categoria->nombre }}</strong></p>
            </div>
        </div>
    </div>

    <form action="{{ route('categorias.update', $categoria) }}" 
          method="POST" 
          @submit.prevent="submitForm">
        @csrf
        @method('PUT')

        <!-- Formulario -->
        <div class="form-section bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Información de la Categoría</h2>
                    <p class="text-sm text-gray-600">Actualiza los datos necesarios</p>
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
                               name="nombre"
                               x-model="form.nombre"
                               value="{{ old('nombre', $categoria->nombre) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all @error('nombre') border-red-500 bg-red-50 @enderror"
                               placeholder="Nombre de la categoría"
                               required>
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    @error('nombre')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Descripción -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Descripción (Opcional)
                    </label>
                    <div class="input-group">
                        <textarea 
                            name="descripcion"
                            x-model="form.descripcion"
                            @input="characterCount = $event.target.value.length"
                            rows="4"
                            maxlength="500"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all resize-none @error('descripcion') border-red-500 bg-red-50 @enderror"
                            placeholder="Descripción detallada (opcional)">{{ old('descripcion', $categoria->descripcion) }}</textarea>
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                    </div>
                    <div class="mt-1 flex justify-between text-xs text-gray-500">
                        <span>Información adicional sobre la categoría</span>
                        <span x-text="`${characterCount}/500`"></span>
                    </div>
                    @error('descripcion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Info adicional -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div class="text-sm">
                <p class="font-semibold text-blue-900">Registro</p>
                <p class="text-blue-700 mt-1">
                    Creada el {{ $categoria->created_at->format('d/m/Y') }} • 
                    Última actualización: {{ $categoria->updated_at->diffForHumans() }}
                </p>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-sm text-gray-500">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Los campos marcados con <span class="text-red-500 font-semibold">*</span> son obligatorios
                </p>
                
                <div class="flex gap-3 w-full sm:w-auto">
                    <a href="{{ route('categorias.index') }}" 
                       class="flex-1 sm:flex-none px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all text-center">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancelar
                    </a>
                    
                    <button type="submit" 
                            :disabled="isSubmitting"
                            :class="isSubmitting ? 'opacity-60 cursor-not-allowed' : 'hover:shadow-lg transform hover:-translate-y-0.5'"
                            class="flex-1 sm:flex-none px-6 py-3 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-bold rounded-lg transition-all flex items-center justify-center gap-2">
                        <svg x-show="!isSubmitting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg x-show="isSubmitting" x-cloak class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                            ircle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isSubmitting ? 'Actualizando...' : 'Actualizar Categoría'">Actualizar Categoría</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('categoriaEditForm', () => ({
        form: {
            nombre: '{{ old('nombre', $categoria->nombre) }}',
            descripcion: '{{ old('descripcion', $categoria->descripcion) }}'
        },
        isSubmitting: false,
        characterCount: '{{ old('descripcion', $categoria->descripcion) }}'.length,
        
        async submitForm(e) {
            if (this.isSubmitting) return;
            
            if (!this.form.nombre.trim()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El nombre de la categoría es obligatorio',
                    confirmButtonColor: '#218786'
                });
                return;
            }
            
            const result = await Swal.fire({
                title: '¿Actualizar categoría?',
                html: `
                    <div class="text-left bg-gray-50 p-4 rounded-lg space-y-2">
                        <p class="text-sm"><strong class="text-gray-700">Nombre:</strong> <span class="text-gray-900">${this.form.nombre}</span></p>
                        ${this.form.descripcion ? `<p class="text-sm"><strong class="text-gray-700">Descripción:</strong> <span class="text-gray-900">${this.form.descripcion}</span></p>` : ''}
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
            
            this.isSubmitting = true;
            
            Swal.fire({
                title: 'Actualizando categoría...',
                html: '<div class="flex justify-center mt-4"><div class="animate-spin w-10 h-10 border-4 border-[#218786] border-t-transparent rounded-full"></div></div>',
                allowOutsideClick: false,
                showConfirmButton: false
            });
            
            e.target.submit();
        },
        
        init() {
            console.log('📝 Categoría cargada:', {
                nombre: this.form.nombre,
                descripcion: this.form.descripcion || 'Sin descripción'
            });
        }
    }));
});

// Mensajes de sesión
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#218786',
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-lg px-6 py-3'
            }
        });
    @endif
    
    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Errores en el formulario',
            html: '<ul class="text-left list-disc pl-5 space-y-1">@foreach($errors->all() as $error)<li class="text-sm">{{ $error }}</li>@endforeach</ul>',
            confirmButtonColor: '#218786',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-lg px-6 py-3'
            }
        });
    @endif
});

// Atajo Ctrl+S
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        document.querySelector('form').dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
    }
});
</script>
@endpush
@endsection
