@extends('layouts.app')

@section('title', 'Configuración')
@section('page-title', 'Configuración de Empresa')
@section('page-description', 'Gestiona los datos de tu organización para la facturación y reportes')

@section('content')
<div class="max-w-7xl mx-auto">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Columna Izquierda: Tarjeta de Presentación -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Tarjeta de Logo/Resumen Mejorada -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <!-- Header con gradiente -->
                <div class="bg-gradient-to-br from-[#218786] to-[#1a6d6c] p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-white/10 rounded-full"></div>
                    <div class="absolute bottom-0 left-0 -mb-6 -ml-6 w-24 h-24 bg-white/5 rounded-full"></div>
                    
                    <div class="relative z-10 text-center">
                        <div class="mx-auto w-24 h-24 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-xl ring-4 ring-white/30">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Contenido -->
                <div class="p-6 text-center">
                    <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $empresa->nombre }}</h2>
                    <p class="text-sm text-gray-500 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        {{ $empresa->ruc ?? 'RUC no registrado' }}
                    </p>
                    
                    <!-- Stats -->
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4">
                                <span class="block text-2xl font-bold text-blue-700">{{ $empresa->usuarios->count() }}</span>
                                <span class="text-xs text-blue-600 font-medium">Usuarios</span>
                            </div>
                            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4">
                                <span class="block text-2xl font-bold text-green-700">Activo</span>
                                <span class="text-xs text-green-600 font-medium">Estado</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta de Información Rápida Mejorada -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 border border-blue-200 shadow-sm">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-blue-900 font-bold mb-2">Información Importante</h4>
                        <p class="text-sm text-blue-700 leading-relaxed">
                            Estos datos aparecerán automáticamente en la cabecera de tus reportes PDF y boletas de venta. Asegúrate de que sean correctos.
                        </p>
                    </div>
                </div>
            </div>


        </div>

        <!-- Columna Derecha: Formulario de Edición -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <!-- Header mejorado -->
                <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-[#218786] to-[#1a6d6c] rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg">Editar Información</h3>
                                <p class="text-xs text-gray-500">Actualiza los datos de tu empresa</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('empresa.update') }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Nombre -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700 flex items-center">
                            Nombre del Negocio 
                            <span class="ml-1 text-red-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-hover:text-[#218786] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <input type="text" name="nombre" value="{{ old('nombre', $empresa->nombre) }}" required
                                   class="block w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all hover:border-gray-300" 
                                   placeholder="Ej: Taller Mecánico El Rayo">
                        </div>
                        @error('nombre') <p class="text-red-500 text-xs mt-1.5 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- RUC -->
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">RUC / ID Fiscal</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 group-hover:text-[#218786] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <input type="text" name="ruc" value="{{ old('ruc', $empresa->ruc) }}" 
                                       class="block w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all hover:border-gray-300" 
                                       placeholder="20123456789">
                            </div>
                        </div>

                        <!-- Teléfono -->
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Teléfono</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 group-hover:text-[#218786] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <input type="text" name="telefono" value="{{ old('telefono', $empresa->telefono) }}" 
                                       class="block w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all hover:border-gray-300" 
                                       placeholder="999 999 999">
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">Correo Electrónico</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-hover:text-[#218786] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="email" name="email" value="{{ old('email', $empresa->email) }}" 
                                   class="block w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all hover:border-gray-300" 
                                   placeholder="contacto@miempresa.com">
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">Dirección Completa</label>
                        <div class="relative group">
                            <div class="absolute top-3 left-3 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-hover:text-[#218786] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <textarea name="direccion" rows="3"
                                      class="block w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all resize-none hover:border-gray-300"
                                      placeholder="Av. Principal 123, Ciudad">{{ old('direccion', $empresa->direccion) }}</textarea>
                        </div>
                    </div>

                    <!-- Footer con botones -->
                    <div class="pt-6 flex items-center justify-between border-t border-gray-100">
                        <p class="text-sm text-gray-500 flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Tus datos están seguros
                        </p>
                        <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-[#218786] to-[#1a6d6c] hover:from-[#1d7874] hover:to-[#165b5c] text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
