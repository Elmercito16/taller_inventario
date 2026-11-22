@extends('layouts.app')

@section('title', 'Clientes')
@section('page-title', 'Clientes')
@section('page-description', 'Tu cartera de clientes y su historial de compras')

@section('page-actions')
    <a href="{{ request()->fullUrlWithQuery(['exportar' => 'csv']) }}" class="btn-secondary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Exportar
    </a>
    <a href="{{ route('clientes.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nuevo cliente
    </a>
@endsection

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="stat-card">
            <p class="stat-label">Clientes registrados</p>
            <p class="stat-value">{{ number_format($resumen['total']) }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Empresas (con RUC)</p>
            <p class="stat-value">{{ number_format($resumen['empresas']) }}</p>
            <p class="stat-foot">Pueden recibir factura</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Nuevos este mes</p>
            <p class="stat-value">{{ number_format($resumen['nuevos_mes']) }}</p>
        </div>
    </div>

    <div class="card-flush">
        <form method="GET" class="toolbar">
            <div class="flex-1">
                <label class="form-label">Buscar</label>
                <div class="input-search">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-input" placeholder="Nombre, DNI, RUC, teléfono o email">
                </div>
            </div>
            <div class="lg:w-48">
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-input" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <option value="persona" @selected(request('tipo') === 'persona')>Personas</option>
                    <option value="empresa" @selected(request('tipo') === 'empresa')>Empresas (RUC)</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button class="btn-secondary">Buscar</button>
                @if(request()->filled('q') || request()->filled('tipo'))
                    <a href="{{ route('clientes.index') }}" class="btn-ghost">Limpiar</a>
                @endif
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th class="hidden md:table-cell">Contacto</th>
                        <th class="num">Compras</th>
                        <th class="num hidden sm:table-cell">Total comprado</th>
                        <th class="hidden lg:table-cell">Registrado</th>
                        <th class="w-px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cliente)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3 min-w-[220px]">
                                    <span class="avatar w-9 h-9 text-sm">{{ mb_strtoupper(mb_substr($cliente->nombre, 0, 1)) }}</span>
                                    <div class="min-w-0">
                                        <a href="{{ route('clientes.historial', $cliente->id) }}" class="font-medium text-gray-900 hover:text-primary-700 truncate block">{{ $cliente->nombre }}</a>
                                        <p class="text-xs text-gray-500">
                                            @if($cliente->tieneRuc())
                                                <span class="badge badge-info !py-0 mr-1">RUC</span>{{ $cliente->ruc }}
                                            @else
                                                DNI {{ $cliente->dni ?: '—' }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="hidden md:table-cell">
                                @if($cliente->telefono)
                                    <a href="https://wa.me/51{{ preg_replace('/\D/', '', $cliente->telefono) }}" target="_blank" class="flex items-center gap-1.5 text-gray-900 hover:text-primary-700">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        {{ $cliente->telefono }}
                                    </a>
                                @endif
                                @if($cliente->email)
                                    <a href="mailto:{{ $cliente->email }}" class="text-xs text-gray-500 hover:text-primary-700 truncate block max-w-[220px]">{{ $cliente->email }}</a>
                                @endif
                                @if(! $cliente->telefono && ! $cliente->email)<span class="text-gray-400">—</span>@endif
                            </td>
                            <td class="num">{{ $cliente->ventas_count }}</td>
                            <td class="num hidden sm:table-cell font-medium text-gray-900">S/ {{ number_format((float) $cliente->total_comprado, 2) }}</td>
                            <td class="hidden lg:table-cell text-gray-500 whitespace-nowrap">{{ $cliente->created_at?->format('d/m/Y') }}</td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('clientes.historial', $cliente->id) }}" class="btn-icon" title="Historial de compras">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </a>
                                    <a href="{{ route('clientes.edit', $cliente) }}" class="btn-icon" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>
                                    <form action="{{ route('clientes.destroy', $cliente) }}" method="POST"
                                          data-confirm="¿Eliminar a «{{ $cliente->nombre }}»?"
                                          data-confirm-detalle="{{ $cliente->ventas_count ? 'Tiene ventas registradas, por lo que no se podrá eliminar.' : 'Esta acción no se puede deshacer.' }}"
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
                                    <div class="empty-icon"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                                    @if(request()->filled('q') || request()->filled('tipo'))
                                        <p class="font-medium text-gray-900">Sin resultados</p>
                                        <p class="mt-1">No encontramos clientes con esa búsqueda.</p>
                                    @else
                                        <p class="font-medium text-gray-900">Aún no tienes clientes</p>
                                        <p class="mt-1">Regístralos aquí o directamente desde el punto de venta.</p>
                                        <a href="{{ route('clientes.create') }}" class="btn-primary mt-4">Nuevo cliente</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clientes->hasPages())
            <div class="card-foot">{{ $clientes->links() }}</div>
        @endif
    </div>
</div>
@endsection
