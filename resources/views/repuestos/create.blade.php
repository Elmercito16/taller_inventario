@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-gray-700">Agregar Repuesto</h2>

    <form action="{{ route('repuestos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Código oculto generado automáticamente -->
        <input type="hidden" name="codigo" value="{{ $nuevoCodigo }}">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nombre -->
            <div>
                <label class="block text-gray-600 font-medium mb-2">Nombre</label>
                <input type="text" name="nombre" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <!-- Marca -->
            <div>
                <label class="block text-gray-600 font-medium mb-2">Marca</label>
                <input type="text" name="marca" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <!-- Cantidad -->
            <div>
                <label class="block text-gray-600 font-medium mb-2">Cantidad</label>
                <input type="number" name="cantidad" min="0" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <!-- Precio Unitario -->
            <div>
                <label class="block text-gray-600 font-medium mb-2">Precio Unitario</label>
                <input type="number" name="precio_unitario" step="0.01" min="0" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <!-- Proveedor -->
            <div>
                <label class="block text-gray-600 font-medium mb-2">Proveedor</label>
                <select name="proveedor_id" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">Seleccione un proveedor</option>
                    @foreach($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Categoría -->
            <div>
                <label class="block text-gray-600 font-medium mb-2">Categoría</label>
                <select name="categoria_id" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-green-400" required>
                    <option value="">Seleccione una categoría</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Descripción -->
            <div class="md:col-span-2">
                <label class="block text-gray-600 font-medium mb-2">Descripción</label>
                <textarea name="descripcion" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" rows="4"></textarea>
            </div>

            <!-- Fecha de Ingreso -->
            <div>
                <label class="block text-gray-600 font-medium mb-2">Fecha de Ingreso</label>
                <input type="date" name="fecha_ingreso" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- Mínimo Stock -->
            <div>
                <label class="block text-gray-600 font-medium mb-2">Mínimo Stock</label>
                <input type="number" name="minimo_stock" value="0" min="0" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- Imagen -->
            <div>
                <label class="block text-gray-600 font-medium mb-2">Imagen</label>
                <input type="file" name="imagen" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" accept="image/*">
            </div>
        </div>

        <!-- Botones -->
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('repuestos.index') }}" class="px-6 py-2 rounded bg-gray-400 text-white hover:bg-gray-500">Cancelar</a>
            <button type="submit" class="px-6 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Guardar</button>
        </div>
    </form>
</div>
@endsection
