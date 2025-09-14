@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
             Historial de compras de <span class="text-blue-600">{{ $cliente->nombre }}</span>
        </h1>
        <button id="openModal" 
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 13v5a1 1 0 01-1.447.894l-4-2A1 1 0 019 16v-3L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            Filtrar compras
        </button>
    </div>

    {{-- Resumen de ventas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-blue-100 rounded-lg shadow-lg p-6 transition transform hover:scale-105 hover:bg-blue-200">
            <p class="text-sm text-gray-600">Total de ventas</p>
            <p class="text-2xl font-bold text-gray-800">{{ $ventas->count() }}</p>
        </div>
        <div class="bg-green-100 rounded-lg shadow-lg p-6 transition transform hover:scale-105 hover:bg-green-200">
            <p class="text-sm text-gray-600">Monto acumulado</p>
            <p class="text-2xl font-bold text-green-600">
                S/ {{ number_format($ventas->sum('total'), 2) }}
            </p>
        </div>
        <div class="bg-yellow-100 rounded-lg shadow-lg p-6 transition transform hover:scale-105 hover:bg-yellow-200">
            <p class="text-sm text-gray-600">Última compra</p>
            <p class="text-lg sm:text-xl font-semibold text-gray-800">
                {{ optional($ventas->first())->fecha ? \Carbon\Carbon::parse($ventas->first()->fecha)->format('d/m/Y H:i') : '—' }}
            </p>
        </div>
    </div>

    {{-- Ventas (accordion-style) --}}
    <div class="space-y-6">
        @foreach ($ventas as $venta)
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex justify-between items-center cursor-pointer"
                     onclick="toggleAccordion('venta-{{ $venta->id }}')">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Compra #{{ $venta->id }}</h2>
                    <svg id="arrow-icon-{{ $venta->id }}" 
                         class="w-5 h-5 transform rotate-0 transition-transform duration-300" 
                         xmlns="http://www.w3.org/2000/svg" fill="none" 
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" 
                              stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                <!-- Detalles de la venta -->
                <div id="venta-{{ $venta->id }}" 
                     class="opacity-0 max-h-0 overflow-hidden transform transition-all duration-500 ease-in-out">
                    <div class="mt-4 text-sm sm:text-base">
                        <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</p>
                        <p><strong>Total:</strong> S/ {{ number_format($venta->total, 2) }}</p>
                        <p><strong>Estado:</strong> 
                            <span class="px-3 py-1 text-xs font-bold rounded-full 
                                {{ $venta->estado === 'pagado' ? 'bg-green-100 text-green-700' : ($venta->estado === 'pendiente' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($venta->estado) }}
                            </span>
                        </p>

                        <div class="mt-4">
                            <a href="{{ route('ventas.show', $venta->id) }}" 
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-xs">
                                Ver detalle
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Modal con animación y responsive --}}
<div id="filterModal" 
     class="fixed inset-0 hidden z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity duration-300 px-4">
    <div id="modalContent" 
         class="bg-white rounded-xl p-6 w-full max-w-md max-h-[85vh] overflow-y-auto shadow-2xl transform scale-95 opacity-0 transition-all duration-300">
        <h2 class="text-xl font-bold text-gray-800 mb-4 text-center border-b pb-2">🔎 Selecciona un filtro</h2>
        
        <form method="GET" action="{{ route('ventas.historial', $cliente->id) }}" class="space-y-4">
            <div class="space-y-3">
                @php
                    $filtros = [
                        'today' => 'Hoy',
                        'yesterday' => 'Ayer',
                        'last_7_days' => 'Últimos 7 días',
                        'last_30_days' => 'Últimos 30 días',
                        'this_week' => 'Esta semana',
                        'last_week' => 'Semana pasada',
                        'this_month' => 'Este mes',
                        'last_month' => 'Mes pasado',
                        'this_year' => 'Este año',
                        'last_year' => 'El año pasado',
                        'custom' => 'Fecha personalizada'
                    ];
                @endphp

                @foreach ($filtros as $value => $label)
                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 cursor-pointer">
                        <input type="radio" name="filter" value="{{ $value }}" class="text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700 font-medium">{{ $label }}</span>
                    </label>
                @endforeach
            </div>

            {{-- Fechas personalizadas --}}
            <div id="custom-date-filters" class="flex flex-col sm:flex-row gap-2 mt-3 hidden">
                <input type="date" name="start_date" 
                       class="px-3 py-2 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-200">
                <input type="date" name="end_date" 
                       class="px-3 py-2 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-200">
            </div>

            <div class="flex justify-between gap-4 mt-6">
                <button id="closeModal" type="button" 
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 w-1/2">
                    Cerrar
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 w-1/2">
                    Aplicar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const openModal = document.getElementById("openModal");
    const closeModal = document.getElementById("closeModal");
    const modal = document.getElementById("filterModal");
    const modalContent = document.getElementById("modalContent");
    const radios = document.querySelectorAll('input[name="filter"]');
    const customDateFilters = document.getElementById("custom-date-filters");

    openModal.addEventListener("click", () => {
        modal.classList.remove("hidden");
        setTimeout(() => {
            modalContent.classList.remove("scale-95", "opacity-0");
            modalContent.classList.add("scale-100", "opacity-100");
        }, 10);
    });

    closeModal.addEventListener("click", () => {
        modalContent.classList.remove("scale-100", "opacity-100");
        modalContent.classList.add("scale-95", "opacity-0");
        setTimeout(() => modal.classList.add("hidden"), 300);
    });

    radios.forEach(radio => {
        radio.addEventListener("change", function() {
            if (this.value === "custom") {
                customDateFilters.classList.remove("hidden");
            } else {
                customDateFilters.classList.add("hidden");
            }
        });
    });

    // Accordion para ventas
    function toggleAccordion(ventaId) {
        const details = document.getElementById(ventaId);
        const arrow = document.getElementById('arrow-icon-' + ventaId.split('-')[1]);

        if (details.classList.contains('opacity-0')) {
            details.classList.remove('opacity-0', 'max-h-0');
            details.classList.add('opacity-100', 'max-h-[1000px]');
            arrow.classList.add('rotate-180');
        } else {
            details.classList.add('opacity-0', 'max-h-0');
            details.classList.remove('opacity-100', 'max-h-[1000px]');
            arrow.classList.remove('rotate-180');
        }
    }
</script>
@endsection
