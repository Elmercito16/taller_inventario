@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-8 px-4">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Registrar Nueva Venta</h1>

    <div class="bg-white shadow-lg rounded-lg p-6">
        {{-- Errores --}}
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('ventas.store') }}" method="POST" id="ventaForm">
            @csrf

            {{-- Cliente (opcional) --}}
            <div class="mb-4">
                <label for="cliente_id" class="block text-sm font-medium text-gray-700 mb-2">Cliente (opcional)</label>
                <select name="cliente_id" id="cliente_id" class="w-full border rounded px-3 py-2">
                    <option value="">-- Sin cliente --</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }} — {{ $cliente->dni }}</option>
                    @endforeach
                </select>
            </div>

            {{-- BUSCADOR --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar repuesto</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1011.5 19.5a7.5 7.5 0 005.15-2.85z"/>
                        </svg>
                    </span>
                    <input id="searchInput" type="text" placeholder="Escribe nombre o categoría..."
                           class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           autocomplete="off">
                    <ul id="resultsList"
                        class="absolute z-50 mt-1 w-full bg-white border rounded shadow max-h-60 overflow-auto hidden"></ul>
                </div>
            </div>

            {{-- Lista de repuestos añadidos --}}
            <div class="mb-4">
                <h2 class="text-lg font-semibold mb-2">Repuestos en la venta</h2>
                <table class="w-full border text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 border">Repuesto</th>
                            <th class="px-3 py-2 border">Precio</th>
                            <th class="px-3 py-2 border">Cantidad</th>
                            <th class="px-3 py-2 border">Subtotal</th>
                            <th class="px-3 py-2 border">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="ventaItems"></tbody>
                </table>
            </div>

            {{-- Total --}}
            <div class="mb-4 text-right">
                <p class="text-lg font-semibold">Total: <span id="ventaTotal">S/ 0.00</span></p>
                <input type="hidden" name="total" id="totalInput" value="0">
            </div>

            {{-- Método de pago --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Método de pago</label>
                <select name="metodo_pago" class="w-full border rounded px-3 py-2">
                    <option value="efectivo">Efectivo</option>
                </select>
            </div>

            <div class="flex justify-end">
                <button id="submitBtn" type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow disabled:opacity-60"
                        disabled>
                    Confirmar Venta
                </button>
            </div>
        </form>
    </div>
</div>

@php
    $repuestos_js = $repuestos->map(function($r){
        return [
            'id' => (int) $r->id,
            'nombre' => (string) $r->nombre,
            'categoria' => $r->categoria ? (string) $r->categoria->nombre : '',
            'precio_unitario' => (float) ($r->precio_unitario ?? 0),
            'stock' => (int) ($r->cantidad ?? 0),
        ];
    })->values()->toArray();
@endphp

<script>
    const repuestos = @json($repuestos_js);

    const searchInput = document.getElementById('searchInput');
    const resultsList = document.getElementById('resultsList');
    const ventaItems = document.getElementById('ventaItems');
    const totalInput = document.getElementById('totalInput');
    const ventaTotal = document.getElementById('ventaTotal');
    const submitBtn = document.getElementById('submitBtn');

    let items = [];

    function escapeHtml(text) {
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");
    }

    function showResults(query) {
        resultsList.innerHTML = '';
        const q = query.trim().toLowerCase();
        if (!q) {
            resultsList.classList.add('hidden');
            return;
        }

        const matches = repuestos.filter(r =>
            (r.nombre && r.nombre.toLowerCase().includes(q)) ||
            (r.categoria && r.categoria.toLowerCase().includes(q))
        ).slice(0, 12);

        if (matches.length === 0) {
            resultsList.innerHTML = '<li class="px-4 py-3 text-sm text-gray-500">No se encontraron repuestos</li>';
            resultsList.classList.remove('hidden');
            return;
        }

        for (const r of matches) {
            const li = document.createElement('li');
            li.className = 'px-4 py-3 cursor-pointer hover:bg-gray-100 flex justify-between items-center';
            li.innerHTML = `
                <div>
                    <div class="font-semibold">${escapeHtml(r.nombre)}</div>
                    <div class="text-xs text-gray-500">${escapeHtml(r.categoria || '')} • Stock: ${r.stock}</div>
                </div>
                <div class="text-sm text-green-600 font-semibold">S/ ${Number(r.precio_unitario).toFixed(2)}</div>
            `;
            li.addEventListener('click', () => addItem(r));
            resultsList.appendChild(li);
        }
        resultsList.classList.remove('hidden');
    }

    function addItem(r) {
        // evitar duplicados
        if (items.find(i => i.id === r.id)) {
            alert('Ya añadiste este repuesto.');
            return;
        }

        const item = {
            id: r.id,
            nombre: r.nombre,
            precio: r.precio_unitario,
            stock: r.stock,
            cantidad: 1
        };
        items.push(item);
        renderItems();
        resultsList.classList.add('hidden');
        searchInput.value = '';
    }

    function updateCantidad(id, nuevaCantidad) {
        const item = items.find(i => i.id === id);
        if (!item) return;
        if (nuevaCantidad < 1) nuevaCantidad = 1;
        if (nuevaCantidad > item.stock) nuevaCantidad = item.stock;
        item.cantidad = nuevaCantidad;
        renderItems();
    }

    function removeItem(id) {
        items = items.filter(i => i.id !== id);
        renderItems();
    }

    function renderItems() {
        ventaItems.innerHTML = '';
        let total = 0;

        items.forEach(item => {
            const subtotal = item.precio * item.cantidad;
            total += subtotal;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-3 py-2 border">${escapeHtml(item.nombre)}</td>
                <td class="px-3 py-2 border">S/ ${item.precio.toFixed(2)}</td>
                <td class="px-3 py-2 border">
                    <input type="number" min="1" max="${item.stock}" value="${item.cantidad}"
                        class="w-16 border rounded px-2 py-1 text-center"
                        onchange="updateCantidad(${item.id}, this.value)">
                </td>
                <td class="px-3 py-2 border">S/ ${subtotal.toFixed(2)}</td>
                <td class="px-3 py-2 border text-center">
                    <button type="button" onclick="removeItem(${item.id})"
                        class="text-red-600 hover:underline">Eliminar</button>
                </td>
            `;

            // inputs ocultos
            const hiddenInputs = `
                <input type="hidden" name="repuestos[${item.id}][id]" value="${item.id}">
                <input type="hidden" name="repuestos[${item.id}][cantidad]" value="${item.cantidad}">
                <input type="hidden" name="repuestos[${item.id}][precio]" value="${item.precio}">
            `;
            tr.innerHTML += hiddenInputs;

            ventaItems.appendChild(tr);
        });

        ventaTotal.textContent = `S/ ${total.toFixed(2)}`;
        totalInput.value = total.toFixed(2);

        submitBtn.disabled = items.length === 0;
    }

    searchInput.addEventListener('input', (e) => showResults(e.target.value));

    document.addEventListener('click', (e) => {
        if (!resultsList.contains(e.target) && e.target !== searchInput) {
            resultsList.classList.add('hidden');
        }
    });
</script>
@endsection
