@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-700 mb-6">Lista de Repuestos</h1>

    <!-- Barra de acciones -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <!-- Botón agregar -->
        <a href="{{ route('repuestos.create') }}" 
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 w-full md:w-auto text-center">
            + Agregar Repuesto
        </a>

        <!-- Buscador actualizado -->
        <form action="{{ route('repuestos.index') }}" method="GET" class="relative w-full md:w-1/2 lg:w-1/3">
            <input 
                type="text" 
                name="q" 
                value="{{ request('q') }}"
                placeholder="Buscar repuesto por nombre o marca..."
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10"
            >

            <!-- Botón X dentro del input -->
            @if(request('q'))
                <a 
                    href="{{ route('repuestos.index') }}" 
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 cursor-pointer"
                >
                    ✕
                </a>
            @endif
        </form>
    </div>

    <!-- Grid de repuestos -->
    <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        @forelse ($repuestos as $repuesto)
            <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-4 flex flex-col items-center">
                <!-- Imagen -->
                @if($repuesto->imagen)
                    <img src="{{ asset('storage/' . $repuesto->imagen) }}" 
                         alt="Imagen {{ $repuesto->nombre }}" 
                         class="w-32 h-32 object-cover rounded-lg mb-3">
                @else
                    <span class="text-gray-400 italic mb-3">Sin imagen</span>
                @endif

                <!-- Nombre -->
                <h2 class="text-lg font-semibold text-gray-800 text-center mb-1">
                    {{ $repuesto->nombre }}
                </h2>

                <!-- Precio -->
                <p class="text-blue-600 font-bold text-sm mb-1">
                    S/ {{ number_format($repuesto->precio_unitario, 2) }}
                </p>

                <!-- Cantidad -->
                <p class="text-gray-600 text-sm mb-3">
                    Stock: 
                    @if($repuesto->cantidad <= $repuesto->minimo_stock)
                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded-lg">
                            {{ $repuesto->cantidad }}
                        </span>
                    @else
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-lg">
                            {{ $repuesto->cantidad }}
                        </span>
                    @endif
                </p>

                <!-- Botón ver más -->
                <button onclick="openModal({{ $repuesto->id }})"
                        class="bg-indigo-500 text-white px-4 py-1 rounded-lg text-sm hover:bg-indigo-600 transition">
                    Ver más
                </button>
            </div>

            <!-- Modal -->
            <div id="modal-{{ $repuesto->id }}" 
                 class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
                <div class="bg-white rounded-2xl shadow-lg w-11/12 md:w-2/3 lg:w-1/2 p-6 relative animate-fadeIn">
                    
                    <!-- Cerrar -->
                    <button onclick="closeModal({{ $repuesto->id }})" 
                            class="absolute top-3 right-3 text-gray-600 hover:text-gray-800 text-xl">
                        &times;
                    </button>

                    <!-- Contenido -->
                    <div class="flex flex-col md:flex-row gap-6">
                        <!-- Imagen -->
                        <div class="flex-shrink-0">
                            @if($repuesto->imagen)
                                <img src="{{ asset('storage/' . $repuesto->imagen) }}" 
                                     alt="Imagen {{ $repuesto->nombre }}" 
                                     class="w-48 h-48 object-cover rounded-lg">
                            @else
                                <span class="text-gray-400 italic">Sin imagen</span>
                            @endif
                        </div>

                        <!-- Detalles -->
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $repuesto->nombre }}</h2>
                            <p class="text-gray-600 mb-2"><strong>Código:</strong> {{ $repuesto->codigo }}</p>
                            <p class="text-gray-600 mb-2"><strong>Marca:</strong> {{ $repuesto->marca }}</p>
                            <p class="text-gray-600 mb-2"><strong>Descripción:</strong> {{ $repuesto->descripcion }}</p>
                            <p class="text-gray-600 mb-2"><strong>Proveedor:</strong> {{ $repuesto->proveedor->nombre ?? 'Sin proveedor' }}</p>
                            <p class="text-gray-600 mb-2"><strong>Categoría:</strong> {{ $repuesto->categoria->nombre ?? 'Sin categoría' }}</p>
                            <p class="text-gray-600 mb-2"><strong>Fecha ingreso:</strong> {{ $repuesto->fecha_ingreso }}</p>
                            <p class="text-gray-600 mb-2"><strong>Stock mínimo:</strong> {{ $repuesto->minimo_stock }}</p>
                            <p class="text-gray-600 mb-4"><strong>Precio unitario:</strong> 
                                S/ {{ number_format($repuesto->precio_unitario, 2) }}
                            </p>

                            <!-- Acciones -->
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('repuestos.edit', $repuesto->id) }}" 
                                   class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition text-sm">
                                    Editar
                                </a>

                                <form action="{{ route('repuestos.destroy', $repuesto->id) }}" method="POST" class="block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-sm"
                                            onclick="return confirm('¿Seguro que deseas eliminar este repuesto?')">
                                        Eliminar
                                    </button>
                                </form>

                                <button onclick="agregarCantidad({{ $repuesto->id }}, '{{ $repuesto->nombre }}')"
                                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm">
                                    Agregar Stock
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-500 col-span-full text-center">No se encontraron repuestos.</p>
        @endforelse
    </div>

    <!-- Paginación -->
    @if(!request('q'))
        <div class="mt-6">
            {{ $repuestos->links() }}
        </div>
    @endif
</div>

<!-- Script SweetAlert2 para agregar stock -->
<script>
function agregarCantidad(id, nombre) {
    Swal.fire({
        title: 'Agregar Cantidad',
        text: `Ingrese la cantidad a agregar para "${nombre}"`,
        input: 'number',
        inputAttributes: { min: 1, step: 1 },
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
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/repuestos/${id}/cantidad`;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

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

// Abrir y cerrar modales
function openModal(id) {
    document.getElementById('modal-' + id).classList.remove('hidden');
    document.getElementById('modal-' + id).classList.add('flex');
}

function closeModal(id) {
    document.getElementById('modal-' + id).classList.remove('flex');
    document.getElementById('modal-' + id).classList.add('hidden');
}
</script>

<!-- Animación modal -->
<style>
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}
.animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
}
</style>
@endsection
