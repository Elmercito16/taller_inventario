@extends('layouts.auth')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-2xl">

    <!-- Título -->
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">
        Iniciar Sesión
    </h2>

    <!-- Mensajes de error -->
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Mensajes de éxito -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Formulario de login -->
    <form action="{{ route('login') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Correo -->
        <div>
            <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
            <input type="email" name="correo" id="correo"
                   value="{{ old('correo') }}"
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

        <!-- Recordarme -->
        <div class="flex items-center">
            <input type="checkbox" name="remember" id="remember" 
                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
            <label for="remember" class="ml-2 block text-sm text-gray-600">Recordarme</label>
        </div>

        <!-- Botón de inicio -->
        <div class="pt-4">
            <button type="submit"
                    class="w-full px-5 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition font-medium shadow">
                Ingresar
            </button>
        </div>
    </form>

    <!-- Link de registro -->
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
            ¿No tienes cuenta?
            <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800 font-medium">Regístrate aquí</a>
        </p>
    </div>
</div>
@endsection
