@extends('layouts.app')


@section('title', isset($cliente) ? 'Editar Cliente' : 'Nuevo Cliente')


@section('breadcrumbs')
<nav class="flex items-center space-x-2 text-sm text-gray-500">
    <a href="{{ route('dashboard') }}" class="hover:text-[#218786] transition-colors flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/></svg>
        Dashboard
    </a>
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
    <a href="{{ route('clientes.index') }}" class="hover:text-[#218786] transition-colors">Clientes</a>
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
   
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
   
    /* Estilos para inputs mejorados */
    .input-group { position: relative; }
   
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
    .input-valid { border-color: #10b981; background-color: #ecfdf5; }
    .input-invalid { border-color: #ef4444; background-color: #fef2f2; }
   
    /* Focus ring personalizado */
    input:focus { --tw-ring-color: #218786; border-color: #218786; }
</style>
@endpush


@section('content')
<div class="max-w-4xl mx-auto">
   
    <!-- Header con información -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 form-section">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-[#e6f7f6] rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                    <svg class="w-6 h-6 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ isset($cliente) ? 'Editar Cliente' : 'Registrar Nuevo Cliente' }}
                    </h1>
                    <p class="text-gray-600 mt-1 text-sm">
                        {{ isset($cliente) ? 'Modifica los datos del cliente seleccionado' : 'Ingresa la información para un nuevo cliente' }}
                    </p>
                </div>
            </div>
           
            @if(isset($cliente))
                <div class="text-right text-xs text-gray-500 bg-gray-50 p-2 rounded-lg border border-gray-100">
                    <p>Registro: {{ $cliente->created_at->format('d/m/Y') }}</p>
                    <p>Última act.: {{ $cliente->updated_at->diffForHumans() }}</p>
                </div>
            @endif
        </div>
    </div>


    <form id="clienteForm" action="{{ isset($cliente) ? route('clientes.update', $cliente->id) : route('clientes.store') }}"
          method="POST" class="space-y-6">
        @csrf
        @if(isset($cliente))
            @method('PUT')
        @endif


        <!-- Sección: Datos Principales -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 form-section">
            <div class="flex items-center mb-6 border-b border-gray-100 pb-4">
                <div class="w-8 h-8 bg-blue-50 rounded-full flex items-center justify-center mr-3">
                    <span class="text-blue-600 font-bold text-sm">1</span>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Información Personal</h2>
            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
               
                <!-- DNI -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        DNI <span class="text-red-500">*</span>
                    </label>
                    <div class="input-group">
                        <input type="text"
                               id="dni"
                               name="dni"
                               maxlength="8"
                               value="{{ old('dni', $cliente->dni ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-colors duration-200"
                               placeholder="8 dígitos"
                               required>
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 011-1h2a2 2 0 011 1v2m-4 0a2 2 0 01-1 1h-2a2 2 0 01-1-1m0 0V4a2 2 0 011-1h2a2 2 0 011 1v2"/>
                        </svg>
                    </div>
                    @error('dni')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>


                <!-- Nombre Completo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre Completo <span class="text-red-500">*</span>
                    </label>
                    <div class="input-group">
                        <input type="text"
                               name="nombre"
                               value="{{ old('nombre', $cliente->nombre ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-colors duration-200"
                               placeholder="Nombres y Apellidos"
                               required>
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    @error('nombre')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>


        <!-- Sección: Información de Contacto -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 form-section">
            <div class="flex items-center mb-6 border-b border-gray-100 pb-4">
                <div class="w-8 h-8 bg-green-50 rounded-full flex items-center justify-center mr-3">
                    <span class="text-green-600 font-bold text-sm">2</span>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Datos de Contacto</h2>
            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Teléfono -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Teléfono / Celular <span class="text-red-500">*</span>
                    </label>
                    <div class="input-group">
                        <input type="tel"
                               name="telefono"
                               value="{{ old('telefono', $cliente->telefono ?? '') }}"
                               maxlength="9"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-colors duration-200"
                               placeholder="Ej: 987654321"
                               required>
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    @error('telefono')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
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
                               value="{{ old('email', $cliente->email ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-colors duration-200"
                               placeholder="ejemplo@correo.com">
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>


                <!-- Dirección -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Dirección
                    </label>
                    <div class="input-group">
                        <input type="text"
                               name="direccion"
                               value="{{ old('direccion', $cliente->direccion ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-colors duration-200"
                               placeholder="Dirección completa (Calle, Número, Distrito)">
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    @error('direccion')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>


        <!-- Botones de acción -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 form-section">
            <div class="flex flex-col-reverse sm:flex-row justify-end items-stretch sm:items-center gap-4">
                <a href="{{ route('clientes.index') }}"
                   class="px-8 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors duration-200 flex justify-center items-center text-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cancelar
                </a>
               
                <button type="submit"
                        id="btnGuardar"
                        class="px-8 py-3 bg-gradient-to-r from-[#218786] to-[#1d7874] text-white font-semibold rounded-lg hover:shadow-lg hover:scale-105 transition-all duration-200 flex justify-center items-center text-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ isset($cliente) ? 'Actualizar Cliente' : 'Guardar Cliente' }}</span>
                </button>
            </div>
        </div>
    </form>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('clienteForm');
    const btnGuardar = document.getElementById('btnGuardar');
    const dniInput = document.getElementById('dni');
   
    // Validación de DNI en tiempo real
    if (dniInput) {
        dniInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').substring(0, 8);
        });
    }
   
    // Validación de teléfono
    const telefonoInput = document.querySelector('input[name="telefono"]');
    if (telefonoInput) {
        telefonoInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').substring(0, 9);
        });
    }
   
    // Manejo del envío del formulario
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
           
            // Validaciones
            const dni = dniInput.value.trim();
            const nombre = document.querySelector('input[name="nombre"]').value.trim();
            const telefono = telefonoInput.value.trim();
           
            if (!dni || dni.length < 8) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    text: 'El DNI debe tener 8 dígitos',
                    confirmButtonColor: '#218786'
                });
                return;
            }
           
            if (!nombre) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    text: 'El nombre es obligatorio',
                    confirmButtonColor: '#218786'
                });
                return;
            }
           
            if (!telefono || telefono.length < 7) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    text: 'El teléfono debe tener al menos 7 dígitos',
                    confirmButtonColor: '#218786'
                });
                return;
            }
           
            // Deshabilitar botón para evitar doble envío
            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Guardando...</span>';
           
            // Mostrar loading
            const actionText = '{{ isset($cliente) ? "Actualizando" : "Registrando" }}';
            Swal.fire({
                title: `${actionText} cliente...`,
                html: 'Por favor espera un momento',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
           
            // Enviar formulario
            form.submit();
        });
    }
});
</script>
@endpush


@endsection