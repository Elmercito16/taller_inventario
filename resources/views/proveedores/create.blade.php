@extends('layouts.app')

@section('title', 'Nuevo proveedor')
@section('breadcrumb')
    <a href="{{ route('proveedores.index') }}" class="hover:text-gray-900">Proveedores</a>
    <span class="text-gray-300">/</span>
    <span class="text-gray-900">Nuevo</span>
@endsection
@section('page-title', 'Nuevo proveedor')

@section('content')
<form action="{{ route('proveedores.store') }}" method="POST" class="space-y-6 max-w-4xl">
    @csrf
    @include('proveedores._form')

    <div class="flex justify-end gap-3">
        <a href="{{ route('proveedores.index') }}" class="btn-secondary">Cancelar</a>
        <button type="submit" class="btn-primary">Guardar proveedor</button>
    </div>
</form>
@endsection
