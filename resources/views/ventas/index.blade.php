@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-8 px-4">

    <!-- Título -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">📋 Listado de Ventas</h1>
        <a href="{{ route('ventas.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg shadow-md flex items-center gap-2 transition">
            ➕ Nueva Venta
        </a>
    </div>

    <!-- Ventas -->
    <div class="space-y-6">
        @foreach ($ventas as $venta)
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-800">
                        <!-- Fecha, Cliente y Total -->
                        <span class="block sm:inline">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</span> — 
                        <span class="text-blue-600">{{ $venta->cliente->nombre ?? 'Sin cliente' }}</span> — 
                        <span class="font-semibold text-green-600">S/ {{ number_format($venta->total, 2) }}</span>
                    </h2>
                    <button onclick="openModal({{ $venta->id }})"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow-md transition text-xs">
                        Ver más detalle
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Modal de Detalle de Venta -->
<div id="detailModal" class="fixed inset-0 hidden z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity duration-300 px-4">
    <div id="modalContent" class="bg-white rounded-xl p-6 w-full max-w-md max-h-[85vh] overflow-y-auto shadow-2xl transform scale-95 opacity-0 transition-all duration-300">
        <h2 class="text-xl font-bold text-gray-800 mb-4 text-center border-b pb-2">Detalle de la Venta</h2>
        <div id="modalVentaDetails" class="text-sm text-gray-700">
            <!-- Los detalles de la venta se cargarán aquí -->
        </div>
        <div class="flex justify-end mt-4">
            <button id="closeModal" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 w-1/3">
                Cerrar
            </button>
        </div>
    </div>
</div>

<script>
    // Modal de detalles de la venta
    function openModal(ventaId) {
        const modal = document.getElementById("detailModal");
        const modalContent = document.getElementById("modalContent");
        const modalVentaDetails = document.getElementById("modalVentaDetails");

        // Obtener la venta usando AJAX (opcional, pero más eficiente)
        fetch(`/ventas/${ventaId}/detalles`)
            .then(response => response.json())
            .then(data => {
                // Llenar el modal con los detalles de la venta
                modalVentaDetails.innerHTML = `
                    <p><strong>Fecha:</strong> ${data.fecha}</p>
                    <p><strong>Total:</strong> S/ ${data.total}</p>
                    <p><strong>Estado:</strong> ${data.estado}</p>
                    <p><strong>Cliente:</strong> ${data.cliente.nombre}</p>

                    <h3 class="font-semibold mt-4">Productos Vendidos:</h3>
                    <ul>
                        ${data.detalles.map(detalle => `
                            <li class="flex justify-between">
                                <span>${detalle.repuesto.nombre}</span>
                                <span>S/ ${detalle.subtotal}</span>
                            </li>
                        `).join('')}
                    </ul>
                `;
            });

        // Mostrar el modal
        modal.classList.remove("hidden");
        setTimeout(() => {
            modalContent.classList.remove("scale-95", "opacity-0");
            modalContent.classList.add("scale-100", "opacity-100");
        }, 10);
    }

    // Cerrar el modal
    document.getElementById("closeModal").addEventListener("click", () => {
        const modal = document.getElementById("detailModal");
        const modalContent = document.getElementById("modalContent");

        modalContent.classList.remove("scale-100", "opacity-100");
        modalContent.classList.add("scale-95", "opacity-0");
        setTimeout(() => modal.classList.add("hidden"), 300);
    });
</script>
@endsection
