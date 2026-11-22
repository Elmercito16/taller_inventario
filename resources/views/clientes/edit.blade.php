@extends('layouts.app')

@section('title', 'Editar ' . $cliente->nombre)
@section('breadcrumb')
    <a href="{{ route('clientes.index') }}" class="hover:text-gray-900">Clientes</a>
    <span class="text-gray-300">/</span>
    <span class="text-gray-900">Editar</span>
@endsection
@section('page-title', $cliente->nombre)
@section('page-description', 'Cliente desde el ' . $cliente->created_at?->format('d/m/Y'))

@section('page-actions')
    <a href="{{ route('clientes.historial', $cliente->id) }}" class="btn-secondary">Historial de compras</a>
@endsection

@section('content')
<form action="{{ route('clientes.update', $cliente->id) }}" method="POST" class="space-y-6 max-w-4xl">
    @csrf
    @method('PUT')
    @include('clientes._form')

    <div class="flex justify-end gap-3">
        <a href="{{ route('clientes.index') }}" class="btn-secondary">Cancelar</a>
        <button type="submit" class="btn-primary">Guardar cambios</button>
    </div>
</form>
@endsection
