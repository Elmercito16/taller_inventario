@extends('layouts.app')

@section('title', 'Categorías')
@section('page-title', 'Categorías')
@section('page-description', 'Organiza tu catálogo en grupos')

@section('page-actions')
    <a href="{{ route('categorias.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nueva categoría
    </a>
@endsection

@section('content')
<div class="card-flush" x-data="{ q: '' }">
    <div class="card-head">
        <p class="text-sm text-gray-500">{{ $categorias->total() }} {{ $categorias->total() === 1 ? 'categoría' : 'categorías' }}</p>
        <div class="input-search w-full sm:w-72">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" x-model="q" class="form-input" placeholder="Filtrar en esta página…">
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th class="hidden md:table-cell">Descripción</th>
                    <th class="num">Productos</th>
                    <th class="hidden sm:table-cell">Actualizada</th>
                    <th class="w-px"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($categorias as $categoria)
                    <tr x-show="!q || @js(mb_strtolower($categoria->nombre . ' ' . $categoria->descripcion)).includes(q.toLowerCase())">
                        <td>
                            <div class="flex items-center gap-3">
                                <span class="avatar w-9 h-9 text-sm !rounded-lg">{{ mb_strtoupper(mb_substr($categoria->nombre, 0, 1)) }}</span>
                                <a href="{{ route('repuestos.index', ['categoria_id' => $categoria->id]) }}" class="font-medium text-gray-900 hover:text-primary-700">{{ $categoria->nombre }}</a>
                            </div>
                        </td>
                        <td class="hidden md:table-cell text-gray-500 max-w-md truncate">{{ $categoria->descripcion ?: '—' }}</td>
                        <td class="num">
                            <a href="{{ route('repuestos.index', ['categoria_id' => $categoria->id]) }}" class="font-medium text-gray-900 hover:text-primary-700">{{ $categoria->repuestos_count }}</a>
                        </td>
                        <td class="hidden sm:table-cell text-gray-500 whitespace-nowrap">{{ $categoria->updated_at?->diffForHumans() }}</td>
                        <td>
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('categorias.edit', $categoria) }}" class="btn-icon" title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </a>
                                <form action="{{ route('categorias.destroy', $categoria) }}" method="POST"
                                      data-confirm="¿Eliminar la categoría «{{ $categoria->nombre }}»?"
                                      data-confirm-detalle="{{ $categoria->repuestos_count ? 'Tiene ' . $categoria->repuestos_count . ' producto(s): primero muévelos a otra categoría.' : 'Esta acción no se puede deshacer.' }}"
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
                        <td colspan="5">
                            <div class="empty-state">
                                <div class="empty-icon"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg></div>
                                <p class="font-medium text-gray-900">Aún no hay categorías</p>
                                <p class="mt-1">Crea categorías como "Bebidas" o "Limpieza" para ordenar tu catálogo.</p>
                                <a href="{{ route('categorias.create') }}" class="btn-primary mt-4">Nueva categoría</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categorias->hasPages())
        <div class="card-foot">{{ $categorias->links() }}</div>
    @endif
</div>
@endsection
