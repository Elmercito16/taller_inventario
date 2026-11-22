@extends('layouts.app')

@section('title', 'Nuevo cliente')
@section('breadcrumb')
    <a href="{{ route('clientes.index') }}" class="hover:text-gray-900">Clientes</a>
    <span class="text-gray-300">/</span>
    <span class="text-gray-900">Nuevo</span>
@endsection
@section('page-title', 'Nuevo cliente')

@section('content')
<form action="{{ route('clientes.store') }}" method="POST" class="space-y-6 max-w-4xl">
    @csrf
    @include('clientes._form')

    <div class="flex justify-end gap-3">
        <a href="{{ route('clientes.index') }}" class="btn-secondary">Cancelar</a>
        <button type="submit" class="btn-primary">Guardar cliente</button>
    </div>
</form>
@endsection
