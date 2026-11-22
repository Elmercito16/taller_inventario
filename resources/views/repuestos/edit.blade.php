@extends('layouts.app')

@section('title', 'Editar ' . $repuesto->nombre)
@section('breadcrumb')
    <a href="{{ route('repuestos.index') }}" class="hover:text-gray-900">Productos</a>
    <span class="text-gray-300">/</span>
    <span class="text-gray-900 truncate">{{ $repuesto->codigo }}</span>
@endsection
@section('page-title', $repuesto->nombre)
@section('page-description', 'Editar producto · Stock actual: ' . $repuesto->stock_formateado)

@section('page-actions')
    @unless($repuesto->esServicio())
        <a href="{{ route('kardex.show', $repuesto) }}" class="btn-secondary">Ver kardex</a>
    @endunless
@endsection

@section('content')
<form action="{{ route('repuestos.update', $repuesto->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{ enviando: false }" @submit="enviando = true">
    @csrf
    @method('PUT')
    @include('repuestos._form')

    <div class="flex justify-end gap-3">
        <a href="{{ route('repuestos.index') }}" class="btn-secondary">Cancelar</a>
        <button type="submit" class="btn-primary" :disabled="enviando">
            <span x-text="enviando ? 'Guardando…' : 'Guardar cambios'">Guardar cambios</span>
        </button>
    </div>
</form>
@endsection
