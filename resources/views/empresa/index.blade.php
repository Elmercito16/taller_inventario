@extends('layouts.app')

@section('title', 'Configuración de Empresa')
@section('page-title', 'Configuración de Empresa')
@section('page-description', 'Datos del negocio y facturación electrónica SUNAT')

@section('content')
@php
    $errores = $empresa->getErroresConfiguracion();
    $ambiente = old('ambiente_sunat', $empresa->ambiente_sunat ?? 'beta');
@endphp

<div x-data="{ ambiente: @js($ambiente) }" class="space-y-6">

    {{-- Estado actual --}}
    @if($empresa->tieneFacturacionActiva() && empty($errores))
        <div class="alert alert-success">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <p class="font-semibold">Facturación electrónica activa</p>
                <p class="text-sm opacity-90">Ambiente: <strong>{{ $empresa->esBeta() ? 'Pruebas (Beta)' : 'Producción' }}</strong></p>
            </div>
        </div>
    @else
        <div class="alert alert-warning">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            <div>
                <p class="font-semibold">La facturación electrónica aún no está lista</p>
                @if($errores)
                    <ul class="text-sm mt-1 list-disc list-inside opacity-90">
                        @foreach($errores as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <p class="font-semibold">Revisa los campos marcados</p>
                <ul class="text-sm mt-1 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('empresa.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Información básica --}}
        <section class="card">
            <div class="card-header">
                <h2 class="card-title">Información del negocio</h2>
                <p class="card-subtitle">Así aparecerá tu empresa en tickets y comprobantes</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Nombre del negocio <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $empresa->nombre) }}" class="form-input" placeholder="Ej: Bodega Don Juan" required>
                </div>
                <div>
                    <label class="form-label">Nombre comercial</label>
                    <input type="text" name="nombre_comercial" value="{{ old('nombre_comercial', $empresa->nombre_comercial) }}" class="form-input uppercase" placeholder="MI NEGOCIO">
                </div>
                <div>
                    <label class="form-label">RUC</label>
                    <input type="text" name="ruc" value="{{ old('ruc', $empresa->ruc) }}" maxlength="11" inputmode="numeric" class="form-input" placeholder="20123456789">
                    <p class="form-help">Obligatorio para facturación electrónica (11 dígitos).</p>
                </div>
                <div>
                    <label class="form-label">Razón social</label>
                    <input type="text" name="razon_social" value="{{ old('razon_social', $empresa->razon_social) }}" class="form-input uppercase" placeholder="MI NEGOCIO S.A.C.">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Logo</label>
                    <div class="flex items-center gap-4">
                        @if($empresa->logo_url)
                            <img src="{{ $empresa->logo_url }}" alt="Logo" class="w-14 h-14 rounded-xl object-contain border border-gray-200 bg-white">
                        @endif
                        <input type="file" name="logo" accept="image/png,image/jpeg,image/webp" class="form-file">
                    </div>
                    <p class="form-help">PNG, JPG o WEBP, máximo 1 MB.</p>
                </div>
            </div>
        </section>

        {{-- Dirección fiscal --}}
        <section class="card">
            <div class="card-header">
                <h2 class="card-title">Dirección fiscal</h2>
                <p class="card-subtitle">Debe coincidir con la registrada en SUNAT</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="md:col-span-3">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion_fiscal" value="{{ old('direccion_fiscal', $empresa->direccion_fiscal ?? $empresa->direccion) }}" class="form-input uppercase" placeholder="AV. PRINCIPAL 123">
                </div>
                <div>
                    <label class="form-label">Ubigeo</label>
                    <input type="text" name="ubigeo" value="{{ old('ubigeo', $empresa->ubigeo) }}" maxlength="6" inputmode="numeric" class="form-input" placeholder="150101">
                    <p class="form-help">Código INEI de 6 dígitos (Lima: 150101).</p>
                </div>
                <div>
                    <label class="form-label">Departamento</label>
                    <input type="text" name="departamento" value="{{ old('departamento', $empresa->departamento) }}" class="form-input uppercase" placeholder="LIMA">
                </div>
                <div>
                    <label class="form-label">Provincia</label>
                    <input type="text" name="provincia" value="{{ old('provincia', $empresa->provincia) }}" class="form-input uppercase" placeholder="LIMA">
                </div>
                <div>
                    <label class="form-label">Distrito</label>
                    <input type="text" name="distrito" value="{{ old('distrito', $empresa->distrito) }}" class="form-input uppercase" placeholder="LIMA">
                </div>
                <div>
                    <label class="form-label">Urbanización</label>
                    <input type="text" name="urbanizacion" value="{{ old('urbanizacion', $empresa->urbanizacion) }}" class="form-input uppercase" placeholder="-">
                </div>
            </div>
        </section>

        {{-- Contacto --}}
        <section class="card">
            <div class="card-header">
                <h2 class="card-title">Contacto</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $empresa->telefono) }}" class="form-input" placeholder="999888777">
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $empresa->email) }}" class="form-input" placeholder="contacto@empresa.com">
                </div>
                <div>
                    <label class="form-label">Sitio web</label>
                    <input type="text" name="web" value="{{ old('web', $empresa->web) }}" class="form-input" placeholder="www.empresa.com">
                </div>
            </div>
        </section>

        {{-- SUNAT --}}
        <section class="card">
            <div class="card-header">
                <h2 class="card-title">Facturación electrónica (SUNAT)</h2>
                <p class="card-subtitle">Credenciales y certificado digital</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="form-label">Ambiente</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="choice" :class="ambiente === 'beta' && 'choice-active'">
                            <input type="radio" name="ambiente_sunat" value="beta" x-model="ambiente" class="sr-only">
                            <span class="font-semibold text-gray-900">Pruebas (Beta)</span>
                            <span class="text-xs text-gray-500">Los comprobantes no tienen validez. Ideal para empezar.</span>
                        </label>
                        <label class="choice" :class="ambiente === 'produccion' && 'choice-active'">
                            <input type="radio" name="ambiente_sunat" value="produccion" x-model="ambiente" class="sr-only">
                            <span class="font-semibold text-gray-900">Producción</span>
                            <span class="text-xs text-gray-500">Comprobantes reales. Requiere certificado y clave SOL.</span>
                        </label>
                    </div>
                </div>

                <div x-show="ambiente === 'produccion'" x-cloak>
                    <label class="form-label">Usuario SOL</label>
                    <input type="text" name="sol_user" value="{{ old('sol_user', $empresa->sol_user) }}" class="form-input uppercase" placeholder="MIUSUARIO" autocomplete="off">
                    <p class="form-help">Solo el usuario, sin el RUC adelante.</p>
                </div>
                <div x-show="ambiente === 'produccion'" x-cloak>
                    <label class="form-label">Clave SOL</label>
                    <input type="password" name="sol_pass" class="form-input" placeholder="{{ $empresa->sol_pass ? '•••••••• (guardada)' : 'Clave SOL' }}" autocomplete="new-password">
                    <p class="form-help">Déjalo vacío para conservar la clave actual.</p>
                </div>

                <div x-show="ambiente === 'produccion'" x-cloak>
                    <label class="form-label">Certificado digital</label>
                    <input type="file" name="certificado" accept=".pem,.pfx,.p12" class="form-file">
                    @if($empresa->tieneCertificado())
                        <p class="form-help text-emerald-600">Certificado cargado: {{ $empresa->certificado_path }}</p>
                    @else
                        <p class="form-help text-amber-600">No hay certificado cargado.</p>
                    @endif
                    <p class="form-help">Formatos: .pfx / .p12 (se convierte automáticamente) o .pem.</p>
                </div>
                <div x-show="ambiente === 'produccion'" x-cloak>
                    <label class="form-label">Contraseña del certificado</label>
                    <input type="password" name="certificado_password" class="form-input" placeholder="Solo para .pfx / .p12" autocomplete="new-password">
                </div>

                <div class="md:col-span-2" x-show="ambiente === 'beta'">
                    <div class="rounded-xl bg-sky-50 border border-sky-100 p-4 text-sm text-sky-800">
                        En Beta se usan automáticamente las credenciales públicas de pruebas de SUNAT
                        (<code>MODDATOS</code>) y el certificado de prueba del servidor.
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="hidden" name="facturacion_activa" value="0">
                        <input type="checkbox" name="facturacion_activa" value="1"
                               {{ old('facturacion_activa', $empresa->facturacion_activa) ? 'checked' : '' }}
                               class="mt-0.5 w-5 h-5 rounded text-primary-600 focus:ring-primary-500 border-gray-300">
                        <span>
                            <span class="font-semibold text-gray-900">Activar facturación electrónica</span>
                            <span class="block text-xs text-gray-500">Actívala cuando todos los datos estén completos.</span>
                        </span>
                    </label>
                </div>
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <a href="{{ route('dashboard') }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Guardar cambios
            </button>
        </div>
    </form>
</div>
@endsection
