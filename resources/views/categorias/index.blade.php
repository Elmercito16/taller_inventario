@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold mb-4">Categorías</h1>

    {{-- Mensajes de éxito --}}
    @if(session('success'))
        <div class="bg-green-200 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-end mb-4">
        <a href="{{ route('categorias.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
            + Nueva Categoría
        </a>
    </div>

    <!-- Grid de categorías -->
    <div class="grid gap-6 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
        @forelse($categorias as $categoria)
            <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-4 flex flex-col">
                <h2 class="text-xl font-semibold text-gray-800 mb-2">{{ $categoria->nombre }}</h2>
                <p class="text-gray-600 mb-4">{{ $categoria->descripcion ?? 'Sin descripción' }}</p>

                <!-- Botón desplegar acciones -->
                <button onclick="toggleActions({{ $categoria->id }})" 
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-1 rounded w-full text-left flex justify-between items-center">
                    Acciones
                    <span id="icon-{{ $categoria->id }}">▼</span>
                </button>

                <!-- Contenedor de acciones oculto -->
                <div id="actions-{{ $categoria->id }}" class="hidden mt-2 flex flex-col gap-2">
                    <a href="{{ route('categorias.edit', $categoria) }}" 
                       class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition text-center">
                        Editar
                    </a>

                    <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" onsubmit="return confirm('¿Eliminar esta categoría?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition w-full text-center">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="col-span-full text-center text-gray-500">No hay categorías disponibles.</p>
        @endforelse
    </div>
</div>

<script>
function toggleActions(id) {
    const actions = document.getElementById('actions-' + id);
    const icon = document.getElementById('icon-' + id);
    if (actions.classList.contains('hidden')) {
        actions.classList.remove('hidden');
        icon.innerText = '▲';
    } else {
        actions.classList.add('hidden');
        icon.innerText = '▼';
    }
}
</script>
@endsection
