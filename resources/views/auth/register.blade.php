@extends('layouts.auth')

@section('title', 'Crear Cuenta')

@section('content')
<div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">
    
    <!-- Título -->
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">
        Crear Cuenta
    </h2>

    <!-- Errores -->
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulario de registro -->
    <form action="{{ route('register.store') }}" method="POST" class="space-y-5">
        @csrf

        <!-- Nombre -->
        <div>
            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" 
                   class="w-full border border-gray-300 rounded-xl p-3 shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                   placeholder="Tu nombre completo" required>
        </div>

        <!-- Correo -->
        <div>
            <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
            <input type="email" name="correo" id="correo" value="{{ old('correo') }}" 
                   class="w-full border border-gray-300 rounded-xl p-3 shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                   placeholder="ejemplo@correo.com" required>
        </div>

        <!-- Contraseña -->
        <div>
            <label for="contraseña" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
            <input type="password" name="contraseña" id="contraseña" 
                   class="w-full border border-gray-300 rounded-xl p-3 shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                   placeholder="********" required>
        </div>

        <!-- Rol -->
        <div>
            <label for="rol" class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
            <select name="rol" id="rol" 
                    class="w-full border border-gray-300 rounded-xl p-3 shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400" required>
                <option value="">Selecciona un rol</option>
                <option value="admin" {{ old('rol') == 'admin' ? 'selected' : '' }}>Administrador</option>
                <option value="usuario" {{ old('rol') == 'usuario' ? 'selected' : '' }}>Usuario</option>
            </select>
        </div>

        <!-- Botón -->
        <div class="pt-2">
            <button type="submit" 
                    class="w-full bg-blue-600 text-white py-3 rounded-xl hover:bg-blue-700 transition font-medium shadow">
                Registrarse
            </button>
        </div>
    </form>

    <!-- Link a login -->
    <p class="text-center text-sm text-gray-600 mt-6">
        ¿Ya tienes cuenta?
        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">Inicia sesión</a>
    </p>
</div>
@endsection
