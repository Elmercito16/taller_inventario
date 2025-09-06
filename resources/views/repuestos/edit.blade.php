@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-gray-700">Editar Repuesto</h2>

    {{-- Mostrar errores de validación --}}
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('repuestos.update', $repuesto->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Código (oculto) -->
            <input type="hidden" name="codigo" value="{{ $repuesto->codigo }}">

            <!-- Nombre -->
            <div>
                <label class="block text-gray-600 font-medium mb-2">Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre', $repuesto->nombre) }}" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <!-- Marca -->
            <div>
                <label class="block text-gray-600 font-medium mb-2">Marca</label>
                <input type="text" name="marca" value="{{ old('marca', $repuesto->marca) }}" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <!-- Cantidad -->
            <div>
                <label class="block text-gray-600 font-medium mb-2">Cantidad</label>
                <input type="number" name="cantidad" value="{{ old('cantidad', $repuesto->cantidad) }}" min="0" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <!-- Precio Unitario -->
            <div>
                <label class="block text-gray-600 font-medium mb-2">Precio Unitario</label>
                <input type="number" name="precio_unitario" step="0.01" min="0" value="{{ old('precio_unitario', $repuesto->precio_unitario) }}" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <!-- Proveedor -->
            <div>
                <label class="block text-gray-600 font-medium mb-2">Proveedor</label>
                <select name="proveedor_id" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">Seleccione un proveedor</option>
                    @foreach($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}" {{ old('proveedor_id', $repuesto->proveedor_id) == $proveedor->id ? 'selected' : '' }}>
                            {{ $proveedor->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Categoría -->
            <div>
                <label class="block text-gray-600 font-medium mb-2">Categoría</label>
                <select name="categoria_id" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-green-400" required>
                    <option value="">Seleccione una categoría</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}" {{ old('categoria_id', $repuesto->categoria_id) == $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Descripción -->
            <div class="md:col-span-2">
                <label class="block text-gray-600 font-medium mb-2">Descripción</label>
                <textarea name="descripcion" rows="4" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('descripcion', $repuesto->descripcion) }}</textarea>
            </div>

            <!-- Fecha de Ingreso -->
            <div>
                <label class="block text-gray-600 font-medium mb-2">Fecha de Ingreso</label>
                <input type="date" name="fecha_ingreso" value="{{ old('fecha_ingreso', $repuesto->fecha_ingreso) }}" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- Mínimo Stock -->
            <div>
                <label class="block text-gray-600 font-medium mb-2">Mínimo Stock</label>
                <input type="number" name="minimo_stock" value="{{ old('minimo_stock', $repuesto->minimo_stock) }}" min="0" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- Imagen -->
            <div class="md:col-span-2">
                <label class="block text-gray-600 font-medium mb-2">Imagen</label>
                @if($repuesto->imagen)
                    <img src="{{ asset('storage/' . $repuesto->imagen) }}" alt="{{ $repuesto->nombre }}" class="w-24 h-24 rounded object-cover mb-2">
                @endif
                <input type="file" name="imagen" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" accept="image/*">
            </div>
        </div>

        <!-- Botones -->
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('repuestos.index') }}" class="px-6 py-2 rounded bg-gray-400 text-white hover:bg-gray-500">Cancelar</a>
            <button type="submit" class="px-6 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Actualizar</button>
        </div>
    </form>
</div>
@endsection
