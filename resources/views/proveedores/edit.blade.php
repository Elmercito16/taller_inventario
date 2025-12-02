@extends('layouts.app')

@section('title', 'Editar Proveedor')

@push('styles')
<style>
    .slide-up { animation: slideUp 0.4s ease-out; }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    
    <!-- Header -->
    <div class="slide-up mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-gradient-to-br from-[#218786] to-[#1a6d6c] rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Editar Proveedor</h1>
                    <p class="text-sm text-gray-600">Actualiza la información del proveedor</p>
                </div>
            </div>
            <div class="text-xs text-gray-500 text-right">
                <p class="font-medium">Creado: {{ $proveedor->created_at->format('d/m/Y') }}</p>
                <p>Última actualización: {{ $proveedor->updated_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('proveedores.update', $proveedor->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Card Principal -->
        <div class="slide-up bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            
            <!-- Información Básica -->
            <div class="border-b border-gray-200">
                <div class="p-5 bg-gradient-to-r from-blue-50 to-blue-50/50 border-b border-blue-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Información Básica</h2>
                            <p class="text-sm text-gray-600">Datos principales</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nombre del Proveedor <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="nombre"
                           value="{{ old('nombre', $proveedor->nombre) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-transparent @error('nombre') border-red-300 bg-red-50 @enderror"
                           placeholder="Ej: Repuestos García SAC"
                           required>
                    @error('nombre')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Información de Contacto -->
            <div class="border-b border-gray-200">
                <div class="p-5 bg-gradient-to-r from-green-50 to-green-50/50 border-b border-green-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Información de Contacto</h2>
                            <p class="text-sm text-gray-600">Email y teléfono</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input type="email" 
                               name="contacto"
                               value="{{ old('contacto', $proveedor->contacto) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-transparent @error('contacto') border-red-300 bg-red-50 @enderror"
                               placeholder="correo@ejemplo.com">
                        @error('contacto')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
                        <input type="tel" 
                               name="telefono"
                               value="{{ old('telefono', $proveedor->telefono) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-transparent @error('telefono') border-red-300 bg-red-50 @enderror"
                               placeholder="999 999 999"
                               maxlength="15">
                        @error('telefono')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            <div>
                <div class="p-5 bg-gradient-to-r from-purple-50 to-purple-50/50 border-b border-purple-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Dirección</label>
                    <textarea 
                        name="direccion"
                        rows="3"
                        maxlength="250"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-transparent resize-none @error('direccion') border-red-300 bg-red-50 @enderror"
                        placeholder="Dirección completa del proveedor (opcional)">{{ old('direccion', $proveedor->direccion) }}</textarea>
                    @error('direccion')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
                <a href="{{ route('proveedores.index') }}" 
                   class="inline-flex items-center justify-center px-6 py-3.5 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cancelar
                </a>
                
                <button type="submit" 
                        class="inline-flex items-center justify-center px-8 py-3.5 bg-[#218786] hover:bg-[#1a6d6c] text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl hover:scale-105 active:scale-95">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Actualizar Proveedor
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
            title: '¡Actualizado!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#218786',
            timer: 3000
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            confirmButtonColor: '#218786'
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Errores en el formulario',
            html: '<ul class="text-left list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
            confirmButtonColor: '#218786'
        });
    @endif
});
</script>
@endpush

@endsection