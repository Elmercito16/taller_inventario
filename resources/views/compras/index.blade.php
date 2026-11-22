@extends('layouts.app')

@section('title', 'Compras')
@section('page-title', 'Compras')
@section('page-description', 'Ingreso de mercadería de tus proveedores · Total del filtro: S/ ' . number_format($totalPeriodo, 2))

@section('page-actions')
    <a href="{{ route('compras.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Registrar compra
    </a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Filtros --}}
    <form method="GET" class="card !p-4">
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <div>
                <label class="form-label">Proveedor</label>
                <select name="proveedor_id" class="form-input">
                    <option value="">Todos</option>
                    @foreach($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}" @selected(request('proveedor_id') == $proveedor->id)>{{ $proveedor->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Estado</label>
                <select name="estado" class="form-input">
                    <option value="">Todos</option>
                    <option value="registrada" @selected(request('estado') === 'registrada')>Registrada</option>
                    <option value="anulada" @selected(request('estado') === 'anulada')>Anulada</option>
                </select>
            </div>
            <div>
                <label class="form-label">Desde</label>
                <input type="date" name="desde" value="{{ request('desde') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Hasta</label>
                <input type="date" name="hasta" value="{{ request('hasta') }}" class="form-input">
            </div>
            <div class="flex gap-2 col-span-2 lg:col-span-1">
                <input type="text" name="q" value="{{ request('q') }}" class="form-input" placeholder="N° documento">
                <button class="btn-secondary shrink-0" title="Filtrar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </div>
        </div>
    </form>

    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Compra</th>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th class="hidden md:table-cell">Documento</th>
                        <th class="center hidden sm:table-cell">Ítems</th>
                        <th class="num">Total</th>
                        <th class="center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($compras as $compra)
                        <tr class="cursor-pointer" onclick="window.location='{{ route('compras.show', $compra) }}'">
                            <td class="font-semibold text-gray-900">{{ $compra->numero }}</td>
                            <td>{{ $compra->fecha?->format('d/m/Y') }}</td>
                            <td class="max-w-[200px] truncate">{{ $compra->proveedor->nombre ?? 'Sin proveedor' }}</td>
                            <td class="hidden md:table-cell text-gray-500">{{ $compra->documento_texto }}</td>
                            <td class="center hidden sm:table-cell">{{ $compra->detalles_count }}</td>
                            <td class="text-right font-semibold text-gray-900">S/ {{ number_format($compra->total, 2) }}</td>
                            <td class="center">
                                <span class="badge {{ $compra->estaAnulada() ? 'badge-danger' : 'badge-success' }}">
                                    {{ $compra->estaAnulada() ? 'Anulada' : 'Registrada' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <p class="font-medium text-gray-700">Aún no hay compras registradas</p>
                                    <p class="mt-1">Registra tu primera compra para ingresar mercadería al inventario.</p>
                                    <a href="{{ route('compras.create') }}" class="btn-primary mt-4">Registrar compra</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($compras->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $compras->links() }}</div>
        @endif
    </div>
</div>
@endsection
