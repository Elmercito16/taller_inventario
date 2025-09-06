@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 shadow-md rounded-lg">
    <h1 class="text-2xl font-bold mb-4 text-gray-700">
        Agregar Cantidad al Repuesto: {{ $repuesto->nombre }}
    </h1>

    <!-- Formulario para actualizar la cantidad -->
    <form action="{{ route('repuestos.updateCantidad') }}" method="POST">
        @csrf

        <!-- ID oculto del repuesto -->
        <input type="hidden" name="id" value="{{ $repuesto->id }}">

        <!-- Campo de cantidad -->
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Cantidad a agregar</label>
            <input type="number" name="cantidad" min="1" 
                   class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200"
                   required>
        </div>

        <!-- Botón -->
        <div class="flex justify-end space-x-2">
            <a href="{{ route('repuestos.index') }}" 
               class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Actualizar
            </button>
        </div>
    </form>
</div>
@endsection
