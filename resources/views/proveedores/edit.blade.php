@extends('layouts.app')

@section('title', 'Editar ' . $proveedor->nombre)
@section('breadcrumb')
    <a href="{{ route('proveedores.index') }}" class="hover:text-gray-900">Proveedores</a>
    <span class="text-gray-300">/</span>
    <span class="text-gray-900">Editar</span>
@endsection
@section('page-title', $proveedor->nombre)
@section('page-description', 'Proveedor desde el ' . $proveedor->created_at?->format('d/m/Y'))

@section('page-actions')
    @if(in_array(auth()->user()->rol, ['admin', 'encargado']))
        <a href="{{ route('compras.index', ['proveedor_id' => $proveedor->id]) }}" class="btn-secondary">Ver compras</a>
    @endif
@endsection

@section('content')
<form action="{{ route('proveedores.update', $proveedor->id) }}" method="POST" class="space-y-6 max-w-4xl">
    @csrf
    @method('PUT')
    @include('proveedores._form')

    <div class="flex justify-end gap-3">
        <a href="{{ route('proveedores.index') }}" class="btn-secondary">Cancelar</a>
        <button type="submit" class="btn-primary">Guardar cambios</button>
    </div>
</form>
@endsection
