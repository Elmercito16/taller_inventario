@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto mt-10">
    <div class="bg-white shadow-xl rounded-2xl p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">
            Editar Cliente
        </h1>

        {{-- Errores de validación --}}
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('clientes.update', $cliente->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- DNI -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">DNI</label>
                <input type="text" name="dni" maxlength="8"
                       value="{{ old('dni', $cliente->dni) }}"
                       class="w-full border border-gray-300 rounded-xl p-3 shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                       placeholder="Ingrese DNI" required>
            </div>

            <!-- Nombre -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input type="text" name="nombre"
                       value="{{ old('nombre', $cliente->nombre) }}"
                       class="w-full border border-gray-300 rounded-xl p-3 shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                       placeholder="Ingrese nombre" required>
            </div>

            <!-- Teléfono -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                <input type="text" name="telefono"
                       value="{{ old('telefono', $cliente->telefono) }}"
                       class="w-full border border-gray-300 rounded-xl p-3 shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                       placeholder="Ingrese teléfono" required>
            </div>

            <!-- Dirección -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                <input type="text" name="direccion"
                       value="{{ old('direccion', $cliente->direccion) }}"
                       class="w-full border border-gray-300 rounded-xl p-3 shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                       placeholder="Ingrese dirección">
            </div>

            <!-- Correo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                <input type="email" name="email"
                       value="{{ old('email', $cliente->email) }}"
                       class="w-full border border-gray-300 rounded-xl p-3 shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                       placeholder="Ingrese correo electrónico">
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('clientes.index') }}" 
                   class="px-5 py-3 bg-gray-200 text-gray-800 rounded-xl hover:bg-gray-300 transition font-medium">
                   Cancelar
                </a>
                <button type="submit"
                        class="px-5 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition font-medium">
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
