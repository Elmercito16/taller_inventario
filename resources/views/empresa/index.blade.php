@extends('layouts.app')

@section('title', 'Configuración')
@section('page-title', 'Configuración de Empresa')
@section('page-description', 'Gestiona los datos de tu organización para la facturación y reportes')

@section('content')
<div class="max-w-4xl mx-auto">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Columna Izquierda: Tarjeta de Presentación -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Tarjeta de Logo/Resumen -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 text-center">
                <div class="mx-auto w-24 h-24 bg-gradient-to-br from-[#218786] to-[#1d7874] rounded-full flex items-center justify-center mb-4 shadow-lg text-white">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">{{ $empresa->nombre }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $empresa->ruc ?? 'RUC no registrado' }}</p>
                
                <div class="mt-6 pt-6 border-t border-gray-100 flex justify-center space-x-4">
                    <div class="text-center">
                        <span class="block text-lg font-bold text-gray-900">{{ $empresa->usuarios->count() }}</span>
                        <span class="text-xs text-gray-500">Usuarios</span>
                    </div>
                    <!-- Aquí podrías agregar más stats si tuvieras -->
                </div>
            </div>

            <!-- Tarjeta de Información Rápida -->
            <div class="bg-blue-50 rounded-xl p-5 border border-blue-100">
                <h4 class="text-blue-800 font-semibold mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Información
                </h4>
                <p class="text-sm text-blue-700 leading-relaxed">
                    Estos datos aparecerán automáticamente en la cabecera de tus reportes PDF y boletas de venta. Asegúrate de que sean correctos.
                </p>
            </div>
        </div>

        <!-- Columna Derecha: Formulario de Edición -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Editar Detalles</h3>
                </div>
                
                <form action="{{ route('empresa.update') }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Nombre -->
                    <div class="space-y-1">
                        <label class="block text-sm font-semibold text-gray-700">Nombre del Negocio <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                            </div>
                            <input type="text" name="nombre" value="{{ old('nombre', $empresa->nombre) }}" required
                                   class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-colors" 
                                   placeholder="Ej: Taller Mecánico El Rayo">
                        </div>
                        @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- RUC -->
                        <div class="space-y-1">
                            <label class="block text-sm font-semibold text-gray-700">RUC / ID Fiscal</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </div>
                                <input type="text" name="ruc" value="{{ old('ruc', $empresa->ruc) }}" 
                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-colors" 
                                       placeholder="20123456789">
                            </div>
                        </div>

                        <!-- Teléfono -->
                        <div class="space-y-1">
                            <label class="block text-sm font-semibold text-gray-700">Teléfono</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                </div>
                                <input type="text" name="telefono" value="{{ old('telefono', $empresa->telefono) }}" 
                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-colors" 
                                       placeholder="999 999 999">
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="space-y-1">
                        <label class="block text-sm font-semibold text-gray-700">Correo Electrónico</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <input type="email" name="email" value="{{ old('email', $empresa->email) }}" 
                                   class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-colors" 
                                   placeholder="contacto@miempresa.com">
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div class="space-y-1">
                        <label class="block text-sm font-semibold text-gray-700">Dirección Completa</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 pt-3 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <textarea name="direccion" rows="3"
                                      class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-colors resize-none"
                                      placeholder="Av. Principal 123, Ciudad">{{ old('direccion', $empresa->direccion) }}</textarea>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-end border-t border-gray-100 mt-6">
                        <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-[#218786] hover:bg-[#1d7874] text-white text-sm font-medium rounded-xl shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection