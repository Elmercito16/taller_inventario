@extends('layouts.app')

@php
    $periodos = ['hoy' => 'Hoy', 'semana' => 'Esta semana', 'mes_actual' => 'Este mes', 'anio_actual' => 'Este año', 'personalizado' => 'Personalizado'];
    $tiposIngreso = ['Servicios', 'Instalación', 'Mantenimiento', 'Alquiler de equipo', 'Intereses', 'Alquiler de espacio', 'Recuperación de deudas', 'Venta de activos', 'Aporte de capital', 'Reembolso', 'Otros'];
    $categoriasGasto = ['Servicios', 'Alquiler', 'Planilla', 'Mantenimiento', 'Mercadería', 'Impuestos', 'Transporte', 'Otros'];
    $tipoMovimiento = request('tipo_movimiento', 'todos');
    $margen = $totalIngresos > 0 ? ($balance / $totalIngresos) * 100 : null;
@endphp

@section('title', 'Finanzas')
@section('page-title', 'Finanzas')
@section('page-description', 'Ingresos, egresos y flujo de caja · ' . $fechaInicio->format('d/m/Y') . ' al ' . $fechaFin->format('d/m/Y'))

@section('page-actions')
    <button type="button" class="btn-secondary" onclick="window.dispatchEvent(new CustomEvent('abrir-modal', { detail: 'ingreso' }))">
        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Registrar ingreso
    </button>
    <button type="button" class="btn-primary" onclick="window.dispatchEvent(new CustomEvent('abrir-modal', { detail: 'gasto' }))">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
        Registrar gasto
    </button>
@endsection

@section('content')
<div class="space-y-6" x-data="{ modal: @js($errors->has('tipo_ingreso') ? 'ingreso' : ($errors->any() ? 'gasto' : null)) }" @abrir-modal.window="modal = $event.detail" @keydown.escape.window="modal = null">

    {{-- Periodo --}}
    <form method="GET" class="card !p-4" x-data="{ filtro: @js($filtro) }">
        <div class="flex flex-col lg:flex-row lg:items-end gap-3">
            <div>
                <p class="form-label">Periodo</p>
                <div class="tabs">
                    @foreach($periodos as $valor => $texto)
                        <label class="tab cursor-pointer" :class="filtro === '{{ $valor }}' && 'tab-active'">
                            <input type="radio" name="filtro" value="{{ $valor }}" x-model="filtro" class="sr-only" @change="filtro !== 'personalizado' && $el.form.submit()">
                            {{ $texto }}
                        </label>
                    @endforeach
                </div>
            </div>
            <template x-if="filtro === 'personalizado'">
                <div class="flex flex-col sm:flex-row gap-3 sm:items-end">
                    <div>
                        <label class="form-label">Desde</label>
                        <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio', $fechaInicio->toDateString()) }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Hasta</label>
                        <input type="date" name="fecha_fin" value="{{ request('fecha_fin', $fechaFin->toDateString()) }}" class="form-input" required>
                    </div>
                    <button class="btn-secondary">Aplicar</button>
                </div>
            </template>
            <input type="hidden" name="tipo_movimiento" value="{{ $tipoMovimiento }}">
        </div>
    </form>

    {{-- Totales --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="stat-card">
            <div class="flex items-center gap-2">
                <span class="dot bg-emerald-500"></span>
                <p class="stat-label">Ingresos</p>
            </div>
            <p class="stat-value">S/ {{ number_format($totalIngresos, 2) }}</p>
            <p class="stat-foot">Ventas pagadas y otros ingresos</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2">
                <span class="dot bg-red-500"></span>
                <p class="stat-label">Egresos</p>
            </div>
            <p class="stat-value">S/ {{ number_format($totalGastos, 2) }}</p>
            <p class="stat-foot">Gastos y compras de mercadería</p>
        </div>
        <div class="stat-card {{ $balance < 0 ? '!border-red-200 !bg-red-50/40' : '!border-primary-200 !bg-primary-50/40' }}">
            <p class="stat-label">Balance del periodo</p>
            <p class="stat-value {{ $balance < 0 ? '!text-red-700' : '!text-primary-700' }}">{{ $balance < 0 ? '−' : '' }}S/ {{ number_format(abs($balance), 2) }}</p>
            <p class="stat-foot">
                @if($margen !== null) Margen sobre ingresos: {{ number_format($margen, 1) }}% @else Sin ingresos en el periodo @endif
            </p>
        </div>
    </div>

    {{-- Flujo de caja --}}
    <div class="card-flush">
        <div class="card-head">
            <div>
                <h2 class="card-title">Flujo de caja</h2>
                <p class="card-subtitle">{{ $cajaFlujo->total() }} movimientos</p>
            </div>
            <div class="tabs">
                @foreach(['todos' => 'Todos', 'ingresos' => 'Ingresos', 'gastos' => 'Egresos'] as $valor => $texto)
                    <a href="{{ request()->fullUrlWithQuery(['tipo_movimiento' => $valor, 'page' => null]) }}" class="tab {{ $tipoMovimiento === $valor ? 'tab-active' : '' }}">{{ $texto }}</a>
                @endforeach
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Concepto</th>
                        <th class="hidden md:table-cell">Origen</th>
                        <th class="num">Monto</th>
                        <th class="w-px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cajaFlujo as $movimiento)
                        @php $esIngreso = $movimiento['tipo'] === 'ingreso'; @endphp
                        <tr>
                            <td class="whitespace-nowrap text-gray-500">{{ \Carbon\Carbon::parse($movimiento['fecha'])->format('d/m/Y') }}</td>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <span class="dot {{ $esIngreso ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                    <span class="text-gray-900">{{ $movimiento['descripcion'] }}</span>
                                </div>
                            </td>
                            <td class="hidden md:table-cell">
                                <span class="badge badge-gray">{{ ['Venta' => 'Venta', 'Ticket' => 'Ticket', 'Ingreso' => 'Otro ingreso', 'Gasto' => 'Gasto', 'Compra' => 'Compra'][$movimiento['modelo']] ?? $movimiento['modelo'] }}</span>
                            </td>
                            <td class="num font-medium whitespace-nowrap {{ $esIngreso ? 'text-emerald-700' : 'text-red-700' }}">
                                {{ $esIngreso ? '+' : '−' }} S/ {{ number_format($movimiento['monto'], 2) }}
                            </td>
                            <td>
                                <div class="flex justify-end">
                                    @if($movimiento['modelo'] === 'Venta')
                                        <a href="{{ route('ventas.show', $movimiento['id']) }}" class="btn-icon" title="Ver venta"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                                    @elseif($movimiento['modelo'] === 'Ticket')
                                        <a href="{{ route('tickets.show', $movimiento['id']) }}" class="btn-icon" title="Ver ticket"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                                    @elseif($movimiento['modelo'] === 'Compra')
                                        <a href="{{ route('compras.show', $movimiento['id']) }}" class="btn-icon" title="Ver compra"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                                    @else
                                        <form action="{{ route($movimiento['modelo'] === 'Ingreso' ? 'finanzas.anularIngreso' : 'finanzas.anularGasto', $movimiento['id']) }}" method="POST"
                                              data-confirm="¿Anular este {{ $movimiento['modelo'] === 'Ingreso' ? 'ingreso' : 'gasto' }}?" data-confirm-detalle="{{ $movimiento['descripcion'] }} · S/ {{ number_format($movimiento['monto'], 2) }}"
                                              data-confirm-text="Anular" data-confirm-danger>
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-icon-danger" title="Anular">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <div class="empty-icon"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                                    <p class="font-medium text-gray-900">Sin movimientos en este periodo</p>
                                    <p class="mt-1">Las ventas, compras, gastos e ingresos aparecerán aquí.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($cajaFlujo->hasPages())
            <div class="card-foot">{{ $cajaFlujo->links() }}</div>
        @endif
    </div>

    {{-- Modal: ingreso / gasto --}}
    <div x-show="modal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="modal = null"></div>

        <form x-show="modal === 'ingreso'" method="POST" action="{{ route('finanzas.storeIngreso') }}" class="relative w-full max-w-md card space-y-4" x-transition>
            @csrf
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Registrar ingreso</h3>
                <p class="text-sm text-gray-500">Ingresos que no provienen de ventas.</p>
            </div>
            <div>
                <label class="form-label">Concepto <span class="text-red-500">*</span></label>
                <input type="text" name="descripcion" class="form-input" maxlength="255" required placeholder="Ej.: Alquiler de vitrina" value="{{ old('descripcion') }}">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="form-label">Monto <span class="text-red-500">*</span></label>
                    <div class="input-addon"><span>S/</span><input type="number" name="monto" step="0.01" min="0.01" class="form-input" required value="{{ old('monto') }}"></div>
                </div>
                <div>
                    <label class="form-label">Fecha <span class="text-red-500">*</span></label>
                    <input type="date" name="fecha" class="form-input" required value="{{ old('fecha', now()->toDateString()) }}">
                </div>
            </div>
            <div>
                <label class="form-label">Tipo <span class="text-red-500">*</span></label>
                <select name="tipo_ingreso" class="form-input" required>
                    <option value="">Selecciona…</option>
                    @foreach($tiposIngreso as $tipo)<option value="{{ $tipo }}" @selected(old('tipo_ingreso') === $tipo)>{{ $tipo }}</option>@endforeach
                </select>
            </div>
            @if($errors->any())<p class="form-error">{{ $errors->first() }}</p>@endif
            <div class="flex justify-end gap-3">
                <button type="button" class="btn-secondary" @click="modal = null">Cancelar</button>
                <button class="btn-primary">Guardar ingreso</button>
            </div>
        </form>

        <form x-show="modal === 'gasto'" method="POST" action="{{ route('finanzas.storeGasto') }}" class="relative w-full max-w-md card space-y-4" x-transition>
            @csrf
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Registrar gasto</h3>
                <p class="text-sm text-gray-500">Las compras de mercadería se registran en Compras.</p>
            </div>
            <div>
                <label class="form-label">Concepto <span class="text-red-500">*</span></label>
                <input type="text" name="descripcion" class="form-input" maxlength="255" required placeholder="Ej.: Pago de luz" value="{{ old('descripcion') }}">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="form-label">Monto <span class="text-red-500">*</span></label>
                    <div class="input-addon"><span>S/</span><input type="number" name="monto" step="0.01" min="0.01" class="form-input" required value="{{ old('monto') }}"></div>
                </div>
                <div>
                    <label class="form-label">Fecha <span class="text-red-500">*</span></label>
                    <input type="date" name="fecha" class="form-input" required value="{{ old('fecha', now()->toDateString()) }}">
                </div>
            </div>
            <div>
                <label class="form-label">Categoría</label>
                <select name="categoria" class="form-input">
                    <option value="">Sin categoría</option>
                    @foreach($categoriasGasto as $categoria)<option value="{{ $categoria }}" @selected(old('categoria') === $categoria)>{{ $categoria }}</option>@endforeach
                </select>
            </div>
            @if($errors->any())<p class="form-error">{{ $errors->first() }}</p>@endif
            <div class="flex justify-end gap-3">
                <button type="button" class="btn-secondary" @click="modal = null">Cancelar</button>
                <button class="btn-primary">Guardar gasto</button>
            </div>
        </form>
    </div>
</div>
@endsection
