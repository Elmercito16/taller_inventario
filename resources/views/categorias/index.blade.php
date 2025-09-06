@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold mb-4">Categorías</h1>

    {{-- Mensajes de éxito --}}
    @if(session('success'))
        <div class="bg-green-200 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('categorias.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">+ Nueva Categoría</a>

    <table class="table-auto w-full mt-4 border">
        <thead class="bg-gray-200">
            <tr>
                <th class="px-4 py-2 border">ID</th>
                <th class="px-4 py-2 border">Nombre</th>
                <th class="px-4 py-2 border">Descripción</th>
                <th class="px-4 py-2 border">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categorias as $categoria)
                <tr>
                    <td class="border px-4 py-2">{{ $categoria->id }}</td>
                    <td class="border px-4 py-2">{{ $categoria->nombre }}</td>
                    <td class="border px-4 py-2">{{ $categoria->descripcion }}</td>
                    <td class="border px-4 py-2 flex gap-2">
                        <a href="{{ route('categorias.edit', $categoria) }}" class="bg-yellow-500 text-white px-3 py-1 rounded">Editar</a>

                        <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" onsubmit="return confirm('¿Eliminar esta categoría?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
