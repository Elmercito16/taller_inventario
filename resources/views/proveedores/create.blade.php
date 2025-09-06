@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-gray-700">Agregar Proveedor</h2>

    <form action="{{ route('proveedores.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-gray-600 font-medium mb-2">Nombre</label>
                <input type="text" name="nombre" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>
            <div>
                <label class="block text-gray-600 font-medium mb-2">Teléfono</label>
                <input type="text" name="telefono" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-gray-600 font-medium mb-2">Email</label>
                <input type="email" name="email" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="md:col-span-2">
                <label class="block text-gray-600 font-medium mb-2">Dirección</label>
                <textarea name="direccion" rows="3" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('proveedores.index') }}" class="px-6 py-2 rounded bg-gray-400 text-white hover:bg-gray-500">Cancelar</a>
            <button type="submit" class="px-6 py-2 rounded bg-green-600 text-white hover:bg-green-700">Guardar</button>
        </div>
    </form>
</div>
@endsection
