@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="text-3xl font-bold mb-6 text-gray-700">Sección Proveedores</h2>

    <div class="mb-6">
        <a href="{{ route('proveedores.create') }}" class="px-5 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition">Agregar Proveedor</a>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200">
        <table class="min-w-full text-left text-sm font-medium text-gray-600">
            <thead class="bg-gray-100 uppercase text-gray-500 text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-3">Nombre</th>
                    <th class="px-6 py-3">Teléfono</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Dirección</th>
                    <th class="px-6 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($proveedores as $proveedor)
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4 font-semibold text-gray-700">{{ $proveedor->nombre }}</td>
                    <td class="px-6 py-4">{{ $proveedor->telefono }}</td>
                    <td class="px-6 py-4">{{ $proveedor->contacto }}</td>
                    <td class="px-6 py-4">{{ $proveedor->direccion }}</td>
                    <td class="px-6 py-4 flex gap-2">
                        <a href="{{ route('proveedores.edit', $proveedor->id) }}" class="px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 transition">Editar</a>
                        <form action="{{ route('proveedores.destroy', $proveedor->id) }}" method="POST" onsubmit="return confirm('¿Desea eliminar este proveedor?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 transition">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Aceptar'
        });
    @endif
</script>
@endsection
