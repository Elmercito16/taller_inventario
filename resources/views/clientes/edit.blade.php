@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('breadcrumbs')
<nav class="flex items-center space-x-2 text-sm text-gray-500">
    <a href="{{ route('dashboard') }}" class="hover:text-[#218786] transition-colors">Dashboard</a>
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
    <a href="{{ route('clientes.index') }}" class="hover:text-[#218786] transition-colors">Clientes</a>
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
<div class="max-w-4xl mx-auto">
    <!-- Header con información del cliente -->
    <div class="form-section bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-[#218786] to-[#1d7874] rounded-lg flex items-center justify-center mr-4">
                    <span class="text-white font-bold text-xl">{{ strtoupper(substr($cliente->nombre, 0, 1)) }}</span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Editar Cliente</h1>
                    <p class="text-gray-600 mt-1">Actualiza la información de {{ $cliente->nombre }}</p>
                </div>
            </div>
            
            <div class="text-right text-sm text-gray-500 bg-gray-50 p-3 rounded-lg border border-gray-100">
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

    <form id="editClienteForm" action="{{ route('clientes.update', $cliente->id) }}" 
          method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Sección: Información de Identidad -->
        <div class="form-section bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6 border-b border-gray-100 pb-4">
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
                        DNI <span class="text-red-500">*</span>
                    </label>
                    <div class="input-group">
                        <input type="text" 
                               id="dni"
                               name="dni" 
                               maxlength="8"
                               value="{{ old('dni', $cliente->dni) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-colors duration-200 pr-12 @error('dni') input-invalid @enderror"
                               placeholder="Ingrese DNI (8 dígitos)"
                               required>
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 011-1h2a2 2 0 011 1v2m-4 0a2 2 0 01-1 1h-2a2 2 0 01-1-1m0 0V4a2 2 0 011-1h2a2 2 0 011 1v2"/>
                        </svg>
                    </div>
                    @error('dni')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nombre -->
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre Completo <span class="text-red-500">*</span>
                    </label>
                    <div class="input-group">
                        <input type="text" 
                               id="nombre"
                               name="nombre"
                               value="{{ old('nombre', $cliente->nombre) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-colors duration-200 pr-12 @error('nombre') input-invalid @enderror"
                               placeholder="Nombres y apellidos completos"
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
        <div class="form-section bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6 border-b border-gray-100 pb-4">
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
                               id="telefono"
                               name="telefono"
                               value="{{ old('telefono', $cliente->telefono) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-colors duration-200 pr-12 @error('telefono') input-invalid @enderror"
                               placeholder="Ej: 987654321"
                               maxlength="9"
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
                               id="email"
                               name="email"
                               value="{{ old('email', $cliente->email) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-colors duration-200 pr-12 @error('email') input-invalid @enderror"
                               placeholder="ejemplo@correo.com">
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Sección: Información Adicional -->
        <div class="form-section bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6 border-b border-gray-100 pb-4">
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
                           id="direccion"
                           name="direccion"
                           value="{{ old('direccion', $cliente->direccion) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-colors duration-200 pr-12 @error('direccion') input-invalid @enderror"
                           placeholder="Ingrese dirección completa">
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

        <!-- Botones de acción -->
        <div class="form-section bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Los campos marcados con <span class="text-red-500 font-medium">*</span> son obligatorios
                </div>
                
                <div class="flex flex-col-reverse sm:flex-row gap-3 w-full sm:w-auto">
                    <!-- Botón Cancelar -->
                    <a href="{{ route('clientes.index') }}" 
                       class="flex items-center justify-center px-6 py-3 border-2 border-[#218786] text-[#218786] font-semibold rounded-lg hover:bg-[#21878610] transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancelar
                    </a>

                    <!-- Botón Actualizar -->
                    <button type="submit"
                            id="btnActualizar"
                            class="flex items-center justify-center px-6 py-3 bg-gradient-to-r from-[#218786] to-[#1d7874] text-white font-semibold rounded-lg hover:shadow-lg hover:scale-105 transition-all duration-200">
                        <svg id="iconCheck" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        
                        <svg id="iconSpinner" class="animate-spin w-5 h-5 mr-2 hidden" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        
                        <span id="btnText">Actualizar Cliente</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editClienteForm');
    const btnActualizar = document.getElementById('btnActualizar');
    const btnText = document.getElementById('btnText');
    const iconCheck = document.getElementById('iconCheck');
    const iconSpinner = document.getElementById('iconSpinner');
    
    // Inputs
    const dniInput = document.getElementById('dni');
    const nombreInput = document.getElementById('nombre');
    const telefonoInput = document.getElementById('telefono');
    const emailInput = document.getElementById('email');
    const direccionInput = document.getElementById('direccion');
    
    // Valores originales para detectar cambios
    const originalValues = {
        dni: dniInput.value,
        nombre: nombreInput.value,
        telefono: telefonoInput.value,
        email: emailInput.value,
        direccion: direccionInput.value
    };
    
    // Validación de DNI en tiempo real
    dniInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g, '').substring(0, 8);
    });
    
    // Validación de teléfono en tiempo real
    telefonoInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g, '').substring(0, 9);
    });
    
    // Detectar cambios en los campos
    function hasChanges() {
        return dniInput.value !== originalValues.dni ||
               nombreInput.value !== originalValues.nombre ||
               telefonoInput.value !== originalValues.telefono ||
               emailInput.value !== originalValues.email ||
               direccionInput.value !== originalValues.direccion;
    }
    
    // Manejo del envío del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validaciones
        const dni = dniInput.value.trim();
        const nombre = nombreInput.value.trim();
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
        
        // Verificar si hay cambios
        if (!hasChanges()) {
            Swal.fire({
                icon: 'info',
                title: 'Sin cambios',
                text: 'No se han detectado cambios en los datos del cliente',
                confirmButtonColor: '#218786'
            });
            return;
        }
        
        // Mostrar resumen de cambios
        let cambiosHtml = '<div class="text-left space-y-2">';
        cambiosHtml += '<p class="font-semibold mb-3 text-gray-700">Se actualizarán los siguientes datos:</p>';
        
        if (dni !== originalValues.dni) {
            cambiosHtml += `<p class="text-sm"><strong>DNI:</strong> <span class="text-red-600">${originalValues.dni}</span> → <span class="text-green-600">${dni}</span></p>`;
        }
        if (nombre !== originalValues.nombre) {
            cambiosHtml += `<p class="text-sm"><strong>Nombre:</strong> <span class="text-red-600">${originalValues.nombre}</span> → <span class="text-green-600">${nombre}</span></p>`;
        }
        if (telefono !== originalValues.telefono) {
            cambiosHtml += `<p class="text-sm"><strong>Teléfono:</strong> <span class="text-red-600">${originalValues.telefono}</span> → <span class="text-green-600">${telefono}</span></p>`;
        }
        if (emailInput.value !== originalValues.email) {
            cambiosHtml += `<p class="text-sm"><strong>Email:</strong> <span class="text-red-600">${originalValues.email || 'N/A'}</span> → <span class="text-green-600">${emailInput.value || 'N/A'}</span></p>`;
        }
        if (direccionInput.value !== originalValues.direccion) {
            cambiosHtml += `<p class="text-sm"><strong>Dirección:</strong> <span class="text-red-600">${originalValues.direccion || 'N/A'}</span> → <span class="text-green-600">${direccionInput.value || 'N/A'}</span></p>`;
        }
        
        cambiosHtml += '</div>';
        
        // Confirmación de actualización
        Swal.fire({
            title: '¿Actualizar cliente?',
            html: cambiosHtml,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, actualizar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#218786',
            cancelButtonColor: '#6b7280'
        }).then((result) => {
            if (result.isConfirmed) {
                // Deshabilitar botón
                btnActualizar.disabled = true;
                iconCheck.classList.add('hidden');
                iconSpinner.classList.remove('hidden');
                btnText.textContent = 'Actualizando...';
                
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
                form.submit();
            }
        });
    });
    
    // Advertencia al salir si hay cambios
    window.addEventListener('beforeunload', function(e) {
        if (hasChanges()) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
    
    // Atajo Ctrl + S para guardar
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            form.dispatchEvent(new Event('submit'));
        }
    });
});
</script>
@endpush

@endsection