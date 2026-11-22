@extends('layouts.app')

@section('title', 'Proveedores')
@section('page-title', 'Proveedores')
@section('page-description', 'Empresas y personas que te abastecen de mercadería')

@section('page-actions')
    <a href="{{ route('proveedores.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nuevo proveedor
    </a>
@endsection

@section('content')
@php $puedeComprar = in_array(auth()->user()->rol, ['admin', 'encargado']); @endphp
<div class="card-flush" x-data="{ q: '' }">
    <div class="card-head">
        <p class="text-sm text-gray-500">{{ $proveedores->total() }} {{ $proveedores->total() === 1 ? 'proveedor' : 'proveedores' }}</p>
        <div class="input-search w-full sm:w-72">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" x-model="q" class="form-input" placeholder="Filtrar en esta página…">
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th>Proveedor</th>
                    <th class="hidden md:table-cell">Contacto</th>
                    <th class="hidden lg:table-cell">Dirección</th>
                    <th class="num">Productos</th>
                    @if($puedeComprar)<th class="num hidden sm:table-cell">Comprado</th>@endif
                    <th class="w-px"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($proveedores as $proveedor)
                    <tr x-show="!q || @js(mb_strtolower($proveedor->nombre . ' ' . $proveedor->contacto . ' ' . $proveedor->telefono)).includes(q.toLowerCase())">
                        <td>
                            <div class="flex items-center gap-3">
                                <span class="avatar w-9 h-9 text-sm">{{ mb_strtoupper(mb_substr($proveedor->nombre, 0, 1)) }}</span>
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 truncate">{{ $proveedor->nombre }}</p>
                                    <p class="text-xs text-gray-500 md:hidden">{{ $proveedor->telefono ?: 'Sin teléfono' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="hidden md:table-cell">
                            <p class="text-gray-900">{{ $proveedor->contacto ?: '—' }}</p>
                            @if($proveedor->telefono)
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $proveedor->telefono) }}" class="text-xs text-gray-500 hover:text-primary-700">{{ $proveedor->telefono }}</a>
                            @endif
                        </td>
                        <td class="hidden lg:table-cell text-gray-500 max-w-xs truncate">{{ $proveedor->direccion ?: '—' }}</td>
                        <td class="num">{{ $proveedor->repuestos_count }}</td>
                        @if($puedeComprar)
                            <td class="num hidden sm:table-cell">
                                @if($proveedor->compras_count)
                                    <a href="{{ route('compras.index', ['proveedor_id' => $proveedor->id]) }}" class="text-gray-900 hover:text-primary-700">S/ {{ number_format($proveedor->total_comprado ?? 0, 2) }}</a>
                                    <p class="text-xs text-gray-500">{{ $proveedor->compras_count }} {{ $proveedor->compras_count === 1 ? 'compra' : 'compras' }}</p>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        @endif
                        <td>
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('proveedores.edit', $proveedor->id) }}" class="btn-icon" title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </a>
                                <form action="{{ route('proveedores.destroy', $proveedor->id) }}" method="POST"
                                      data-confirm="¿Eliminar a «{{ $proveedor->nombre }}»?" data-confirm-detalle="Esta acción no se puede deshacer."
                                      data-confirm-text="Eliminar" data-confirm-danger>
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-icon-danger" title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-icon"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>
                                <p class="font-medium text-gray-900">Aún no hay proveedores</p>
                                <p class="mt-1">Registra a tus proveedores para asociarlos a tus compras y productos.</p>
                                <a href="{{ route('proveedores.create') }}" class="btn-primary mt-4">Nuevo proveedor</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($proveedores->hasPages())
        <div class="card-foot">{{ $proveedores->links() }}</div>
    @endif
</div>
@endsection
