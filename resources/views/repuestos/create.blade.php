@extends('layouts.app')

@section('title', 'Nuevo producto')
@section('breadcrumb')
    <a href="{{ route('repuestos.index') }}" class="hover:text-gray-900">Productos</a>
    <span class="text-gray-300">/</span>
    <span class="text-gray-900">Nuevo</span>
@endsection
@section('page-title', 'Nuevo producto')
@section('page-description', 'Registra un producto o servicio en tu catálogo')

@section('content')
<form action="{{ route('repuestos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{ enviando: false }" @submit="enviando = true">
    @csrf
    @include('repuestos._form')

    <div class="flex justify-end gap-3">
        <a href="{{ route('repuestos.index') }}" class="btn-secondary">Cancelar</a>
        <button type="submit" class="btn-primary" :disabled="enviando">
            <span x-text="enviando ? 'Guardando…' : 'Guardar producto'">Guardar producto</span>
        </button>
    </div>
</form>
@endsection
