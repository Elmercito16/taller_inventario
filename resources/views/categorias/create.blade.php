@extends('layouts.app')

@section('title', 'Nueva categoría')
@section('breadcrumb')
    <a href="{{ route('categorias.index') }}" class="hover:text-gray-900">Categorías</a>
    <span class="text-gray-300">/</span>
    <span class="text-gray-900">Nueva</span>
@endsection
@section('page-title', 'Nueva categoría')

@section('content')
<form action="{{ route('categorias.store') }}" method="POST" class="space-y-6 max-w-4xl">
    @csrf
    @include('categorias._form')

    <div class="flex justify-end gap-3">
        <a href="{{ route('categorias.index') }}" class="btn-secondary">Cancelar</a>
        <button type="submit" class="btn-primary">Guardar categoría</button>
    </div>
</form>
@endsection
