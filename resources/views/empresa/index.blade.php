@extends('layouts.app')

@section('title', 'Configuración de Facturación')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="container mx-auto px-4">
        
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 flex items-center gap-3">
                <span class="text-5xl">⚙️</span>
                Configuración de Facturación Electrónica
            </h1>
            <p class="text-gray-600 mt-2 ml-16">Configura los datos para emitir comprobantes electrónicos</p>
        </div>

        {{-- Estado Actual --}}
        <div class="mb-6">
            @if($empresa->tieneFacturacionActiva())
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-6 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="text-3xl">✅</div>
                        <div>
                            <h3 class="font-bold text-lg">Facturación Electrónica Activa</h3>
                            <p class="text-sm">Tu sistema está listo para emitir comprobantes electrónicos</p>
                            <p class="text-xs mt-1">
                                Ambiente: <strong>{{ $empresa->ambiente_sunat == 'beta' ? '🧪 Pruebas (Beta)' : '🚀 Producción' }}</strong>
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-6 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="text-3xl">⚠️</div>
                        <div>
                            <h3 class="font-bold text-lg">Facturación Electrónica No Configurada</h3>
                            <p class="text-sm">Completa los datos requeridos para activar la facturación electrónica</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Formulario de Configuración --}}
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            
            <div class="bg-gradient-to-r from-[#218786] to-[#1a6d6c] px-8 py-6">
                <h2 class="text-2xl font-bold text-white">📋 Datos de la Empresa</h2>
            </div>

            <form action="{{ route('empresa.update') }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf
                @method('PUT')

                {{-- Datos Básicos --}}
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-gray-200">
                        🏢 Información Básica
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                RUC <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="ruc" value="{{ old('ruc', $empresa->ruc) }}" 
                                   maxlength="11" pattern="[0-9]{11}"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] @error('ruc') border-red-500 @enderror"
                                   placeholder="20123456789" required>
                            @error('ruc')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Nombre Comercial <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nombre_comercial" value="{{ old('nombre_comercial', $empresa->nombre_comercial ?? $empresa->nombre) }}"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] @error('nombre_comercial') border-red-500 @enderror"
                                   placeholder="MI TALLER" required>
                            @error('nombre_comercial')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Razón Social <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="razon_social" value="{{ old('razon_social', $empresa->razon_social ?? $empresa->nombre) }}"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] @error('razon_social') border-red-500 @enderror"
                                   placeholder="MI TALLER S.A.C." required>
                            @error('razon_social')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Dirección Fiscal --}}
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-gray-200">
                        📍 Dirección Fiscal
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Dirección <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="direccion_fiscal" value="{{ old('direccion_fiscal', $empresa->direccion_fiscal ?? $empresa->direccion) }}"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] @error('direccion_fiscal') border-red-500 @enderror"
                                   placeholder="AV. PRINCIPAL 123" required>
                            @error('direccion_fiscal')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Ubigeo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="ubigeo" value="{{ old('ubigeo', $empresa->ubigeo ?? '150101') }}"
                                   maxlength="6" pattern="[0-9]{6}"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] @error('ubigeo') border-red-500 @enderror"
                                   placeholder="150101" required>
                            @error('ubigeo')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Lima-Lima-Lima: 150101</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Departamento
                            </label>
                            <input type="text" name="departamento" value="{{ old('departamento', $empresa->departamento ?? 'LIMA') }}"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786]"
                                   placeholder="LIMA">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Provincia
                            </label>
                            <input type="text" name="provincia" value="{{ old('provincia', $empresa->provincia ?? 'LIMA') }}"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786]"
                                   placeholder="LIMA">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Distrito
                            </label>
                            <input type="text" name="distrito" value="{{ old('distrito', $empresa->distrito ?? 'LIMA') }}"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786]"
                                   placeholder="LIMA">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Urbanización
                            </label>
                            <input type="text" name="urbanizacion" value="{{ old('urbanizacion', $empresa->urbanizacion ?? '-') }}"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786]"
                                   placeholder="-">
                        </div>

                    </div>
                </div>

                {{-- Datos de Contacto --}}
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-gray-200">
                        📞 Datos de Contacto
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Teléfono
                            </label>
                            <input type="text" name="telefono" value="{{ old('telefono', $empresa->telefono) }}"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786]"
                                   placeholder="999888777">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Email
                            </label>
                            <input type="email" name="email" value="{{ old('email', $empresa->email) }}"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786]"
                                   placeholder="contacto@empresa.com">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Sitio Web
                            </label>
                            <input type="text" name="web" value="{{ old('web', $empresa->web) }}"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786]"
                                   placeholder="www.empresa.com">
                        </div>

                    </div>
                </div>

                {{-- Configuración SUNAT --}}
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-gray-200">
                        📡 Configuración SUNAT
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Ambiente SUNAT <span class="text-red-500">*</span>
                            </label>
                            <select name="ambiente_sunat" 
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786]" required>
                                <option value="beta" {{ old('ambiente_sunat', $empresa->ambiente_sunat) == 'beta' ? 'selected' : '' }}>
                                    🧪 Pruebas (Beta) - Recomendado para iniciar
                                </option>
                                <option value="produccion" {{ old('ambiente_sunat', $empresa->ambiente_sunat) == 'produccion' ? 'selected' : '' }}>
                                    🚀 Producción - Solo cuando tengas certificado real
                                </option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                Inicia en <strong>Beta</strong> para hacer pruebas sin afectar a SUNAT real
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Certificado Digital (.PEM)
                            </label>
                            <input type="file" name="certificado" accept=".pem"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786]">
                            @if($empresa->certificado_path)
                                <p class="text-xs text-green-600 mt-1">
                                    ✅ Certificado actual: {{ $empresa->certificado_path }}
                                </p>
                            @else
                                <p class="text-xs text-yellow-600 mt-1">
                                    ⚠️ No hay certificado cargado
                                </p>
                            @endif
                            <p class="text-xs text-gray-500 mt-1">
                                Sube tu certificado digital en formato .PEM
                            </p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="facturacion_activa" value="1" 
                                       {{ old('facturacion_activa', $empresa->facturacion_activa) ? 'checked' : '' }}
                                       class="w-5 h-5 text-[#218786] rounded focus:ring-2 focus:ring-[#218786]">
                                <span class="font-bold text-gray-700">
                                    Activar Facturación Electrónica
                                </span>
                            </label>
                            <p class="text-xs text-gray-500 ml-8 mt-1">
                                Activa esta opción solo cuando hayas configurado correctamente todos los datos
                            </p>
                        </div>

                    </div>
                </div>

                {{-- Botones --}}
                <div class="flex justify-end gap-4 pt-6 border-t-2">
                    <a href="{{ route('dashboard') }}" 
                       class="px-8 py-3 bg-gray-400 text-white font-bold rounded-xl hover:bg-gray-500 transition-all">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-8 py-3 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-bold rounded-xl hover:shadow-2xl transition-all">
                        💾 Guardar Configuración
                    </button>
                </div>

            </form>

        </div>

        {{-- Información de Ayuda --}}
        <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-6 rounded-xl">
            <h3 class="font-bold text-blue-800 mb-2">📚 Información Importante</h3>
            <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                <li>Para <strong>pruebas</strong>, usa el RUC de prueba: <code class="bg-blue-100 px-2 py-1 rounded">20000000001</code></li>
                <li>Las credenciales SOL de prueba están configuradas automáticamente en el sistema</li>
                <li>El certificado de prueba debe estar en formato <strong>.PEM</strong></li>
                <li>Todos los datos serán convertidos a mayúsculas según lo requiere SUNAT</li>
                <li>Solo activa facturación en <strong>Producción</strong> cuando tengas tu certificado real de SUNAT</li>
            </ul>
        </div>

    </div>
</div>
@endsection
