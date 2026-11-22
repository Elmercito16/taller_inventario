@extends('layouts.auth')

@section('title', 'Iniciar sesión')

@section('content')
<div class="lg:hidden mb-8 flex items-center gap-3">
    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
    </div>
    <span class="text-lg font-semibold text-gray-900">MultiSoft</span>
</div>

<h1 class="text-2xl font-semibold tracking-tight text-gray-900">Bienvenido de nuevo</h1>
<p class="mt-1 text-sm text-gray-500">Ingresa con tu cuenta para continuar.</p>

@if(session('success'))
    <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
@endif

<form action="{{ route('login') }}" method="POST" class="mt-8 space-y-5" x-data="{ ver: false, enviando: false }" @submit="enviando = true">
    @csrf
    <div>
        <label for="correo" class="form-label">Correo electrónico</label>
        <input id="correo" type="email" name="correo" value="{{ old('correo') }}" class="form-input" placeholder="tu@correo.com" autocomplete="email" required autofocus>
    </div>

    <div>
        <label for="contrasena" class="form-label">Contraseña</label>
        <div class="relative">
            <input id="contrasena" :type="ver ? 'text' : 'password'" name="contraseña" class="form-input pr-10" placeholder="••••••••" autocomplete="current-password" required minlength="6">
            <button type="button" @click="ver = !ver" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600" :title="ver ? 'Ocultar' : 'Mostrar'">
                <svg x-show="!ver" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <svg x-show="ver" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
            </button>
        </div>
    </div>

    <button type="submit" class="btn-primary w-full" :disabled="enviando">
        <svg x-show="enviando" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/><path fill="currentColor" class="opacity-75" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
        <span x-text="enviando ? 'Ingresando…' : 'Iniciar sesión'">Iniciar sesión</span>
    </button>
</form>

<p class="mt-8 text-center text-xs text-gray-400">¿No tienes acceso? Pide al administrador de tu negocio que te cree un usuario.</p>
@endsection
