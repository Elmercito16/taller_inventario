@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold mb-4">Nueva Categoría</h1>

    <form action="{{ route('categorias.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block">Nombre:</label>
            <input type="text" name="nombre" class="border rounded p-2 w-full" value="{{ old('nombre') }}" required>
            @error('nombre')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block">Descripción:</label>
            <textarea name="descripcion" class="border rounded p-2 w-full">{{ old('descripcion') }}</textarea>
        </div>

        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Guardar</button>
        <a href="{{ route('categorias.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</a>
    </form>
</div>
@endsection
