@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-700 mb-6">Lista de Repuestos</h1>

    <div class="mb-4">
        <a href="{{ route('repuestos.create') }}" 
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + Agregar Repuesto
        </a>
    </div>

    <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
        <table class="min-w-full text-sm text-left border border-gray-200">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-3 border-b">Imagen</th>
                    <th class="px-4 py-3 border-b">Código</th>
                    <th class="px-4 py-3 border-b">Nombre / Marca</th>
                    <th class="px-4 py-3 border-b">Descripción</th>
                    <th class="px-4 py-3 border-b">Cantidad</th>
                    <th class="px-4 py-3 border-b">Stock Mínimo</th>
                    <th class="px-4 py-3 border-b">Precio Unitario</th>
                    <th class="px-4 py-3 border-b">Proveedor</th>
                    <th class="px-4 py-3 border-b">Categoría</th>
                    <th class="px-4 py-3 border-b">Fecha Ingreso</th>
                    <th class="px-4 py-3 border-b">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($repuestos as $repuesto)
                    <tr class="border-b hover:bg-gray-50">
                        <!-- Imagen -->
                        <td class="px-4 py-3">
                            @if($repuesto->imagen)
                                <img src="{{ asset('storage/' . $repuesto->imagen) }}" 
                                     alt="Imagen {{ $repuesto->nombre }}" 
                                     class="w-16 h-16 object-cover rounded-lg shadow-sm">
                            @else
                                <span class="text-gray-400 italic">Sin imagen</span>
                            @endif
                        </td>

                        <!-- Código -->
                        <td class="px-4 py-3 font-mono">{{ $repuesto->codigo }}</td>

                        <!-- Nombre y marca -->
                        <td class="px-4 py-3">
                            <span class="font-semibold">{{ $repuesto->nombre }}</span><br>
                            <span class="text-gray-500 text-xs">{{ $repuesto->marca }}</span>
                        </td>

                        <!-- Descripción -->
                        <td class="px-4 py-3 text-gray-600">{{ Str::limit($repuesto->descripcion, 40) }}</td>

                        <!-- Cantidad con alerta -->
                        <td class="px-4 py-3">
                            @if($repuesto->cantidad <= $repuesto->minimo_stock)
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-lg">
                                    {{ $repuesto->cantidad }}
                                </span>
                            @else
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-lg">
                                    {{ $repuesto->cantidad }}
                                </span>
                            @endif
                        </td>

                        <!-- Stock mínimo -->
                        <td class="px-4 py-3 text-gray-600">{{ $repuesto->minimo_stock }}</td>

                        <!-- Precio -->
                        <td class="px-4 py-3 font-semibold text-gray-800">S/ {{ number_format($repuesto->precio_unitario, 2) }}</td>

                        <!-- Proveedor -->
                        <td class="px-4 py-3">{{ $repuesto->proveedor->nombre ?? 'Sin proveedor' }}</td>

                        <!-- Categoría -->
                        <td class="px-4 py-3">{{ $repuesto->categoria->nombre ?? 'Sin categoría' }}</td>

                        <!-- Fecha ingreso -->
                        <td class="px-4 py-3 text-gray-500">{{ $repuesto->fecha_ingreso }}</td>

                        <!-- Acciones -->
                        <td class="px-4 py-3 space-y-1">
                            <a href="{{ route('repuestos.edit', $repuesto->id) }}" 
                               class="block px-3 py-1 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 text-center">
                                Editar
                            </a>

                            <form action="{{ route('repuestos.destroy', $repuesto->id) }}" method="POST" class="block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700"
                                        onclick="return confirm('¿Seguro que deseas eliminar este repuesto?')">
                                    Eliminar
                                </button>
                            </form>

                            <!-- Botón SweetAlert2 para agregar stock -->
                            <button onclick="agregarCantidad({{ $repuesto->id }}, '{{ $repuesto->nombre }}')"
                                    class="w-full px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 text-center">
                                Agregar
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Script SweetAlert2 para agregar stock -->
<script>
function agregarCantidad(id, nombre) {
    Swal.fire({
        title: 'Agregar Cantidad',
        text: `Ingrese la cantidad a agregar para "${nombre}"`,
        input: 'number',
        inputAttributes: {
            min: 1,
            step: 1
        },
        showCancelButton: true,
        confirmButtonText: 'Agregar',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value || value < 1) {
                return 'Por favor ingresa una cantidad válida';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Crear un formulario temporal y enviarlo
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/repuestos/${id}/cantidad`;

            // CSRF token
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            // Cantidad
            const cantidad = document.createElement('input');
            cantidad.type = 'hidden';
            cantidad.name = 'cantidad';
            cantidad.value = result.value;
            form.appendChild(cantidad);

            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection
