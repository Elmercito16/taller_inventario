@extends('layouts.app')

@section('title', 'Editar ' . $categoria->nombre)
@section('breadcrumb')
    <a href="{{ route('categorias.index') }}" class="hover:text-gray-900">Categorías</a>
    <span class="text-gray-300">/</span>
    <span class="text-gray-900">Editar</span>
@endsection
@section('page-title', $categoria->nombre)
@section('page-description', 'Creada el ' . $categoria->created_at?->format('d/m/Y') . ' · Última actualización ' . $categoria->updated_at?->diffForHumans())

@section('page-actions')
    <a href="{{ route('repuestos.index', ['categoria_id' => $categoria->id]) }}" class="btn-secondary">Ver productos</a>
@endsection

@section('content')
<form action="{{ route('categorias.update', $categoria) }}" method="POST" class="space-y-6 max-w-4xl">
    @csrf
    @method('PUT')
    @include('categorias._form')

    <div class="flex justify-end gap-3">
        <a href="{{ route('categorias.index') }}" class="btn-secondary">Cancelar</a>
        <button type="submit" class="btn-primary">Guardar cambios</button>
    </div>
</form>
@endsection
