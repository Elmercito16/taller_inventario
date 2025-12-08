@extends('layouts.app')

@section('title', 'Nuevo Proveedor')

@push('styles')
<style>
    .fade-in {
        animation: fadeIn 0.5s ease-out both;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .fade-in:nth-child(1) { animation-delay: 0s; }
    .fade-in:nth-child(2) { animation-delay: 0.1s; }
    .fade-in:nth-child(3) { animation-delay: 0.2s; }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6 space-y-6">
    
    <!-- Breadcrumbs -->
    <nav class="fade-in flex items-center space-x-2 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-[#218786] transition-colors flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <a href="{{ route('proveedores.index') }}" class="hover:text-[#218786] transition-colors">Proveedores</a>
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <span class="font-medium text-gray-900">Nuevo Proveedor</span>
    </nav>

    <!-- Header -->
    <div class="fade-in bg-gradient-to-r from-[#218786] to-[#1a6d6c] rounded-2xl shadow-lg p-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-xl">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-white mb-1">Nuevo Proveedor</h1>
                <p class="text-white/80">Complete la información del proveedor en el sistema</p>
            </div>
        </div>
    </div>

    <form action="{{ route('proveedores.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Card Principal -->
        <div class="fade-in bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            
            <!-- Información Básica -->
            <div class="border-b border-gray-200">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Información Básica</h2>
                            <p class="text-sm text-gray-600">Datos principales del proveedor</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="group">
                        <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Nombre del Proveedor
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="nombre"
                               value="{{ old('nombre') }}"
                               class="w-full px-4 py-3.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all font-medium @error('nombre') border-red-300 bg-red-50 @enderror"
                               placeholder="Ej: Repuestos García SAC"
                               required>
                        @error('nombre')
                            <div class="mt-2 flex items-center gap-2 text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Información de Contacto -->
            <div class="border-b border-gray-200">
                <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4 border-b border-green-200">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Información de Contacto</h2>
                            <p class="text-sm text-gray-600">Medios de comunicación</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Email -->
                    <div class="group">
                        <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Email
                        </label>
                        <input type="email" 
                               name="contacto"
                               value="{{ old('contacto') }}"
                               class="w-full px-4 py-3.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all font-medium @error('contacto') border-red-300 bg-red-50 @enderror"
                               placeholder="correo@ejemplo.com">
                        @error('contacto')
                            <div class="mt-2 flex items-center gap-2 text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Teléfono -->
                    <div class="group">
                        <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            Teléfono
                        </label>
                        <input type="tel" 
                               name="telefono"
                               value="{{ old('telefono') }}"
                               class="w-full px-4 py-3.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all font-medium @error('telefono') border-red-300 bg-red-50 @enderror"
                               placeholder="999 999 999"
                               maxlength="15">
                        @error('telefono')
                            <div class="mt-2 flex items-center gap-2 text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            <div>
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 border-b border-purple-200">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Información Adicional</h2>
                            <p class="text-sm text-gray-600">Datos complementarios (opcional)</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="group">
                        <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Dirección
                            <span class="text-gray-400 text-xs font-normal">(Opcional)</span>
                        </label>
                        <textarea 
                            name="direccion"
                            rows="3"
                            maxlength="250"
                            class="w-full px-4 py-3.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all resize-none font-medium @error('direccion') border-red-300 bg-red-50 @enderror"
                            placeholder="Av. Principal 123, Distrito, Ciudad">{{ old('direccion') }}</textarea>
                        <p class="mt-2 text-xs text-gray-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Máximo 250 caracteres
                        </p>
                        @error('direccion')
                            <div class="mt-2 flex items-center gap-2 text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="fade-in bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
                <a href="{{ route('proveedores.index') }}" 
                   class="inline-flex items-center justify-center px-6 py-3.5 border-2 border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cancelar
                </a>
                
                <button type="submit" 
                        class="inline-flex items-center justify-center px-8 py-3.5 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-bold rounded-xl hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar Proveedor
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Proveedor registrado!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#218786',
            timer: 3000,
            timerProgressBar: true
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Errores en el formulario',
            html: `
                <div class="text-left">
                    <p class="mb-3 text-gray-600">Por favor corrige los siguientes errores:</p>
                    <ul class="list-disc pl-5 space-y-1 text-sm text-gray-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            `,
            confirmButtonColor: '#218786'
        });
    @endif
});
</script>
@endpush

@endsection
