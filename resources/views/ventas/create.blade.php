@extends('layouts.app')

@section('title', 'Nueva Venta')

@section('breadcrumbs')
<nav class="flex items-center space-x-2 text-sm text-gray-500">
    <a href="{{ route('dashboard') }}" class="hover:text-[#218786]">Dashboard</a>
    <span>/</span>
    <a href="{{ route('ventas.index') }}" class="hover:text-[#218786]">Ventas</a>
    <span>/</span>
    <span class="font-medium text-gray-900">Nueva Venta</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-4">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Productos Disponibles</p>
            <p class="text-2xl font-bold text-gray-900">{{ $repuestos->where('cantidad', '>', 0)->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Productos en Venta</p>
            <p class="text-2xl font-bold text-[#218786]" id="itemsCount">0</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Total</p>
            <p class="text-2xl font-bold text-green-600" id="totalVenta">S/ 0.00</p>
        </div>
    </div>

    <form id="ventaForm" action="{{ route('ventas.store') }}" method="POST" class="space-y-4">
        @csrf
        
        <!-- Cliente -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-900">Cliente</h2>
                <button type="button" onclick="if(window.abrirModalClientes) window.abrirModalClientes()"
                        class="px-4 py-2 bg-[#218786] text-white rounded hover:bg-[#1d7874]">
                    Buscar Cliente
                </button>
            </div>

            <input type="hidden" name="cliente_id" id="clienteId">

            <div id="clienteSeleccionado" class="hidden p-3 bg-green-50 border border-green-200 rounded">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-bold text-green-900" id="clienteNombre"></p>
                        <p class="text-sm text-green-700">DNI: <span id="clienteDni"></span></p>
                    </div>
                    <button type="button" onclick="if(window.cambiarCliente) window.cambiarCliente()" 
                            class="px-3 py-1 border border-green-500 text-green-700 rounded hover:bg-green-50">
                        Cambiar
                    </button>
                </div>
            </div>

            <div id="sinCliente" class="p-8 text-center border-2 border-dashed border-gray-300 rounded">
                <p class="text-gray-500">No hay cliente seleccionado</p>
            </div>
        </div>

        <!-- Productos -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-900">Productos</h2>
                <button type="button" onclick="if(window.abrirModalRepuestos) window.abrirModalRepuestos()"
                        class="px-4 py-2 bg-[#218786] text-white rounded hover:bg-[#1d7874]">
                    Agregar Producto
                </button>
            </div>
        </div>

        <!-- Carrito -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-900">Carrito</h2>
                <button id="btnLimpiarCarrito" type="button" class="hidden text-red-600 hover:text-red-800 text-sm">
                    Limpiar
                </button>
            </div>

            <table id="carritoTable" class="hidden w-full">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-2 text-left">Producto</th>
                        <th class="px-4 py-2 text-left">Precio</th>
                        <th class="px-4 py-2 text-left">Cantidad</th>
                        <th class="px-4 py-2 text-left">Subtotal</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody id="carritoBody"></tbody>
            </table>
            
            <div id="carritoVacio" class="p-8 text-center">
                <p class="text-gray-500">El carrito está vacío</p>
            </div>
        </div>

        <!-- Total -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-gradient-to-r from-[#218786] to-[#1d7874] p-4 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm opacity-90">Total</p>
                        <p class="text-3xl font-bold" id="totalDisplay">S/ 0.00</p>
                    </div>
                    <div class="text-right text-sm opacity-90">
                        <p><span id="productosCount">0</span> productos</p>
                        <p><span id="articulosCount">0</span> artículos</p>
                    </div>
                </div>
            </div>
            
            <div class="p-4 space-y-4">
                <!-- Método de pago -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Método de Pago *</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="metodo_pago" value="efectivo" class="sr-only metodo-pago" required>
                            <div class="metodo-pago-card p-3 border-2 rounded text-center hover:border-[#218786] border-gray-300">
                                <span class="font-medium">Efectivo</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="metodo_pago" value="tarjeta" class="sr-only metodo-pago" required>
                            <div class="metodo-pago-card p-3 border-2 rounded text-center hover:border-[#218786] border-gray-300">
                                <span class="font-medium">Tarjeta</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex gap-3">
                    <a href="{{ route('ventas.index') }}" 
                       class="flex-1 px-4 py-3 border-2 border-gray-300 text-gray-700 font-bold rounded text-center hover:bg-gray-50">
                        Cancelar
                    </a>
                    
                    <button type="submit" 
                            id="btnConfirmarVenta"
                            disabled
                            class="flex-1 px-4 py-3 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-bold rounded disabled:opacity-50 hover:shadow-lg">
                        Confirmar Venta
                    </button>
                </div>
            </div>
            
            <input type="hidden" name="total" id="totalInput" value="0">
        </div>
    </form>

    <!-- Modales -->
    @include('ventas.modals.buscar-cliente')
    @include('ventas.modals.buscar-repuesto')
    @include('ventas.modals.nuevo-cliente')
</div>

@push('scripts')
<script>
window.carritoItems = [];
let selectedCliente = null;

document.addEventListener('DOMContentLoaded', function() {
    const ventaForm = document.getElementById('ventaForm');
    const btnConfirmarVenta = document.getElementById('btnConfirmarVenta');
    const btnLimpiarCarrito = document.getElementById('btnLimpiarCarrito');
    
    window.selectCliente = function(cliente) {
        selectedCliente = cliente;
        document.getElementById('clienteId').value = cliente.id;
        document.getElementById('clienteNombre').textContent = cliente.nombre;
        document.getElementById('clienteDni').textContent = cliente.dni;
        document.getElementById('clienteSeleccionado').classList.remove('hidden');
        document.getElementById('sinCliente').classList.add('hidden');
        document.getElementById('modalBuscarCliente').classList.add('hidden');
        updateBtnConfirmar();
        Swal.fire({icon: 'success', title: 'Cliente seleccionado', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false});
    };
    
    window.cambiarCliente = function() {
        selectedCliente = null;
        document.getElementById('clienteId').value = '';
        document.getElementById('clienteSeleccionado').classList.add('hidden');
        document.getElementById('sinCliente').classList.remove('hidden');
        updateBtnConfirmar();
        if (window.abrirModalClientes) window.abrirModalClientes();
    };
    
    window.addRepuesto = function(repuesto) {
        if (window.carritoItems.find(item => item.id === repuesto.id)) {
            Swal.fire({icon: 'warning', title: 'Ya está en la lista', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false});
            return;
        }
        
        window.carritoItems.push({
            id: repuesto.id,
            nombre: repuesto.nombre,
            precio: repuesto.precio_unitario,
            stock: repuesto.stock,
            cantidad: 1
        });
        
        renderCarrito();
        Swal.fire({icon: 'success', title: 'Producto agregado', toast: true, position: 'top-end', timer: 1500, showConfirmButton: false});
    };
    
    function renderCarrito() {
        const carritoBody = document.getElementById('carritoBody');
        const carritoTable = document.getElementById('carritoTable');
        const carritoVacio = document.getElementById('carritoVacio');
        
        carritoBody.innerHTML = '';
        
        if (window.carritoItems.length === 0) {
            carritoTable.classList.add('hidden');
            carritoVacio.classList.remove('hidden');
            btnLimpiarCarrito.classList.add('hidden');
        } else {
            carritoTable.classList.remove('hidden');
            carritoVacio.classList.add('hidden');
            btnLimpiarCarrito.classList.remove('hidden');
            
            window.carritoItems.forEach((item, index) => {
                const tr = document.createElement('tr');
                tr.className = 'border-b hover:bg-gray-50';
                tr.innerHTML = `
                    <td class="px-4 py-3">
                        <p class="font-medium text-sm">${item.nombre}</p>
                    </td>
                    <td class="px-4 py-3 text-sm">S/ ${item.precio.toFixed(2)}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="updateCantidad(${item.id}, ${item.cantidad - 1})" ${item.cantidad <= 1 ? 'disabled' : ''}
                                    class="w-7 h-7 rounded bg-gray-200 hover:bg-[#218786] hover:text-white disabled:opacity-50">-</button>
                            <span class="w-8 text-center font-bold">${item.cantidad}</span>
                            <button type="button" onclick="updateCantidad(${item.id}, ${item.cantidad + 1})" ${item.cantidad >= item.stock ? 'disabled' : ''}
                                    class="w-7 h-7 rounded bg-gray-200 hover:bg-[#218786] hover:text-white disabled:opacity-50">+</button>
                        </div>
                    </td>
                    <td class="px-4 py-3 font-bold text-sm">S/ ${(item.precio * item.cantidad).toFixed(2)}</td>
                    <td class="px-4 py-3">
                        <button type="button" onclick="removeItem(${item.id})" class="text-red-600 hover:text-red-800">✕</button>
                    </td>
                    <input type="hidden" name="repuestos[${index}][id]" value="${item.id}">
                    <input type="hidden" name="repuestos[${index}][cantidad]" value="${item.cantidad}">
                    <input type="hidden" name="repuestos[${index}][precio]" value="${item.precio}">`;
                carritoBody.appendChild(tr);
            });
        }
        updateTotales();
    }
    
    window.updateCantidad = function(itemId, nuevaCantidad) {
        const item = window.carritoItems.find(i => i.id === itemId);
        if (!item) return;
        
        if (nuevaCantidad < 1) nuevaCantidad = 1;
        if (nuevaCantidad > item.stock) {
            nuevaCantidad = item.stock;
            Swal.fire({icon: 'warning', title: 'Stock máximo', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false});
        }
        item.cantidad = nuevaCantidad;
        renderCarrito();
    };
    
    window.removeItem = function(itemId) {
        window.carritoItems = window.carritoItems.filter(i => i.id !== itemId);
        renderCarrito();
    };
    
    btnLimpiarCarrito.addEventListener('click', () => {
        if (confirm('¿Limpiar carrito?')) {
            window.carritoItems = [];
            renderCarrito();
        }
    });
    
    function updateTotales() {
        const total = window.carritoItems.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
        const totalCantidad = window.carritoItems.reduce((sum, item) => sum + item.cantidad, 0);
        
        document.getElementById('totalVenta').textContent = `S/ ${total.toFixed(2)}`;
        document.getElementById('totalDisplay').textContent = `S/ ${total.toFixed(2)}`;
        document.getElementById('totalInput').value = total.toFixed(2);
        document.getElementById('itemsCount').textContent = window.carritoItems.length;
        document.getElementById('productosCount').textContent = window.carritoItems.length;
        document.getElementById('articulosCount').textContent = totalCantidad;
        updateBtnConfirmar();
    }
    
    document.querySelectorAll('.metodo-pago').forEach(input => {
        input.addEventListener('change', function() {
            document.querySelectorAll('.metodo-pago-card').forEach(card => {
                card.classList.remove('border-[#218786]', 'bg-[#e6f7f6]');
                card.classList.add('border-gray-300');
            });
            if (this.checked) {
                this.parentElement.querySelector('.metodo-pago-card').classList.remove('border-gray-300');
                this.parentElement.querySelector('.metodo-pago-card').classList.add('border-[#218786]', 'bg-[#e6f7f6]');
            }
            updateBtnConfirmar();
        });
    });
    
    function updateBtnConfirmar() {
        const metodoPago = document.querySelector('input[name="metodo_pago"]:checked');
        btnConfirmarVenta.disabled = !(window.carritoItems.length > 0 && selectedCliente && metodoPago);
    }
    
    ventaForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!selectedCliente || window.carritoItems.length === 0) {
            Swal.fire({icon: 'error', title: 'Error', text: 'Completa todos los datos'});
            return;
        }
        
        const result = await Swal.fire({
            title: '¿Confirmar venta?',
            text: `Total: S/ ${window.carritoItems.reduce((s, i) => s + (i.precio * i.cantidad), 0).toFixed(2)}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#218786',
            confirmButtonText: 'Sí, confirmar'
        });
        
        if (result.isConfirmed) {
            btnConfirmarVenta.disabled = true;
            btnConfirmarVenta.textContent = 'Procesando...';
            this.submit();
        }
    });
});
</script>
@endpush

@endsection