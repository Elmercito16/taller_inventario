// public/js/ventas/venta-manager.js

function initVentaManager(repuestos) {
    let todosClientes = [];
    let items = [];
    let selectedCliente = null;
    
    // Elementos DOM
    const modalBuscarCliente = document.getElementById('modalBuscarCliente');
    const modalBuscarRepuesto = document.getElementById('modalBuscarRepuesto');
    const modalNuevoCliente = document.getElementById('modalNuevoCliente');
    
    const btnBuscarCliente = document.getElementById('btnBuscarCliente');
    const btnBuscarRepuesto = document.getElementById('btnBuscarRepuesto');
    const btnCerrarModalCliente = document.getElementById('btnCerrarModalCliente');
    const btnCerrarModalRepuesto = document.getElementById('btnCerrarModalRepuesto');
    const btnNuevoClienteModal = document.getElementById('btnNuevoClienteModal');
    const btnCancelarNuevoCliente = document.getElementById('btnCancelarNuevoCliente');
    const btnGuardarCliente = document.getElementById('btnGuardarCliente');
    
    const overlayCliente = document.getElementById('overlayCliente');
    const overlayRepuesto = document.getElementById('overlayRepuesto');
    const overlayNuevoCliente = document.getElementById('overlayNuevoCliente');
    
    const buscarClienteInput = document.getElementById('buscarClienteInput');
    const buscarRepuestoInput = document.getElementById('buscarRepuestoInput');
    const filtroCategoria = document.getElementById('filtroCategoria');
    
    const listaClientesGrid = document.getElementById('listaClientesGrid');
    const listaRepuestosGrid = document.getElementById('listaRepuestosGrid');
    
    const clienteSeleccionado = document.getElementById('clienteSeleccionado');
    const sinCliente = document.getElementById('sinCliente');
    const btnCambiarCliente = document.getElementById('btnCambiarCliente');
    
    const carritoBody = document.getElementById('carritoBody');
    const carritoTable = document.getElementById('carritoTable');
    const carritoVacio = document.getElementById('carritoVacio');
    const btnLimpiarCarrito = document.getElementById('btnLimpiarCarrito');
    const btnConfirmarVenta = document.getElementById('btnConfirmarVenta');
    const ventaForm = document.getElementById('ventaForm');
    
    // Abrir modales
    btnBuscarCliente.addEventListener('click', () => {
        modalBuscarCliente.classList.remove('hidden');
        cargarTodosClientes();
        setTimeout(() => buscarClienteInput.focus(), 100);
    });
    
    btnBuscarRepuesto.addEventListener('click', () => {
        modalBuscarRepuesto.classList.remove('hidden');
        renderRepuestos(repuestos);
        setTimeout(() => buscarRepuestoInput.focus(), 100);
    });
    
    btnNuevoClienteModal.addEventListener('click', () => {
        modalNuevoCliente.classList.remove('hidden');
        setTimeout(() => document.getElementById('newClientDni').focus(), 100);
    });
    
    // Cerrar modales
    btnCerrarModalCliente.addEventListener('click', () => modalBuscarCliente.classList.add('hidden'));
    btnCerrarModalRepuesto.addEventListener('click', () => modalBuscarRepuesto.classList.add('hidden'));
    btnCancelarNuevoCliente.addEventListener('click', () => {
        modalNuevoCliente.classList.add('hidden');
        clearModalInputs();
    });
    
    overlayCliente.addEventListener('click', () => modalBuscarCliente.classList.add('hidden'));
    overlayRepuesto.addEventListener('click', () => modalBuscarRepuesto.classList.add('hidden'));
    overlayNuevoCliente.addEventListener('click', () => {
        modalNuevoCliente.classList.add('hidden');
        clearModalInputs();
    });
    
    // Cargar todos los clientes
    async function cargarTodosClientes() {
        try {
            const response = await fetch('/clientes/search?q=');
            todosClientes = await response.json();
            renderClientes(todosClientes);
        } catch (error) {
            console.error('Error cargando clientes:', error);
        }
    }
    
    // Buscar clientes
    let timeoutCliente;
    buscarClienteInput.addEventListener('input', function() {
        clearTimeout(timeoutCliente);
        timeoutCliente = setTimeout(() => {
            const query = this.value.toLowerCase().trim();
            if (query === '') {
                renderClientes(todosClientes);
            } else {
                const filtrados = todosClientes.filter(c => 
                    c.nombre.toLowerCase().includes(query) || 
                    c.dni.includes(query)
                );
                renderClientes(filtrados);
            }
        }, 300);
    });
    
    // Render clientes
    function renderClientes(clientes) {
        listaClientesGrid.innerHTML = '';
        const sinResultados = document.getElementById('sinResultadosClientes');
        
        if (clientes.length === 0) {
            sinResultados.classList.remove('hidden');
            return;
        }
        
        sinResultados.classList.add('hidden');
        
        clientes.forEach(cliente => {
            const div = document.createElement('div');
            div.className = 'p-4 border-2 border-gray-200 rounded-xl hover:border-[#218786] cursor-pointer transition-all duration-200 hover:shadow-md';
            div.innerHTML = `
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-[#218786] to-[#1d7874] text-white flex items-center justify-center mr-3 font-bold text-lg flex-shrink-0">
                        ${cliente.nombre.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-900 truncate">${cliente.nombre}</p>
                        <p class="text-sm text-gray-600">DNI: ${cliente.dni}</p>
                        ${cliente.telefono ? `<p class="text-xs text-gray-500">Tel: ${cliente.telefono}</p>` : ''}
                    </div>
                    <svg class="w-6 h-6 text-[#218786] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>`;
            div.addEventListener('click', () => selectCliente(cliente));
            listaClientesGrid.appendChild(div);
        });
    }
    
    // Seleccionar cliente
    function selectCliente(cliente) {
        selectedCliente = cliente;
        document.getElementById('clienteId').value = cliente.id;
        document.getElementById('clienteInicial').textContent = cliente.nombre.charAt(0).toUpperCase();
        document.getElementById('clienteNombre').textContent = cliente.nombre;
        document.getElementById('clienteDni').textContent = cliente.dni;
        clienteSeleccionado.classList.remove('hidden');
        sinCliente.classList.add('hidden');
        modalBuscarCliente.classList.add('hidden');
        updateBtnConfirmar();
        
        Swal.fire({
            icon: 'success',
            title: 'Cliente seleccionado',
            text: cliente.nombre,
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false
        });
    }
    
    // Cambiar cliente
    btnCambiarCliente.addEventListener('click', () => {
        selectedCliente = null;
        document.getElementById('clienteId').value = '';
        clienteSeleccionado.classList.add('hidden');
        sinCliente.classList.remove('hidden');
        updateBtnConfirmar();
        modalBuscarCliente.classList.remove('hidden');
        cargarTodosClientes();
    });
    
    // Buscar y filtrar repuestos
    let timeoutRepuesto;
    buscarRepuestoInput.addEventListener('input', filtrarRepuestos);
    filtroCategoria.addEventListener('change', filtrarRepuestos);
    
    function filtrarRepuestos() {
        clearTimeout(timeoutRepuesto);
        timeoutRepuesto = setTimeout(() => {
            const query = buscarRepuestoInput.value.toLowerCase().trim();
            const categoriaId = parseInt(filtroCategoria.value) || 0;
            
            let filtrados = repuestos.filter(r => r.stock > 0);
            
            if (query !== '') {
                filtrados = filtrados.filter(r => 
                    r.nombre.toLowerCase().includes(query) || 
                    r.categoria.toLowerCase().includes(query)
                );
            }
            
            if (categoriaId > 0) {
                filtrados = filtrados.filter(r => r.categoria_id === categoriaId);
            }
            
            renderRepuestos(filtrados);
        }, 300);
    }
    
    // Render repuestos
    function renderRepuestos(repuestosFiltrados) {
        listaRepuestosGrid.innerHTML = '';
        const sinResultados = document.getElementById('sinResultadosRepuestos');
        
        if (repuestosFiltrados.length === 0) {
            sinResultados.classList.remove('hidden');
            return;
        }
        
        sinResultados.classList.add('hidden');
        
        repuestosFiltrados.forEach(repuesto => {
            const yaAgregado = items.find(i => i.id === repuesto.id);
            const div = document.createElement('div');
            div.className = `p-4 border-2 rounded-xl cursor-pointer transition-all duration-200 ${yaAgregado ? 'border-gray-300 bg-gray-50 opacity-60' : 'border-gray-200 hover:border-[#218786] hover:shadow-md'}`;
            div.innerHTML = `
                <div class="flex flex-col h-full">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-900 text-sm mb-1 line-clamp-2">${repuesto.nombre}</h4>
                            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-medium">${repuesto.categoria}</span>
                        </div>
                    </div>
                    <div class="mt-auto pt-3 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Precio</p>
                                <p class="text-lg font-bold text-[#218786]">S/ ${repuesto.precio_unitario.toFixed(2)}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 font-medium">Stock</p>
                                <p class="text-sm font-bold ${repuesto.stock <= 5 ? 'text-red-600' : 'text-green-600'}">${repuesto.stock}</p>
                            </div>
                        </div>
                    </div>
                    ${yaAgregado ? '<p class="text-xs text-center text-green-600 mt-2 font-medium">✓ Ya agregado</p>' : ''}
                </div>`;
            
            if (!yaAgregado) {
                div.addEventListener('click', () => addRepuesto(repuesto));
            }
            
            listaRepuestosGrid.appendChild(div);
        });
    }
    
    // Agregar repuesto
    function addRepuesto(repuesto) {
        const existe = items.find(item => item.id === repuesto.id);
        if (existe) {
            Swal.fire({
                icon: 'warning',
                title: 'Ya está en la lista',
                toast: true,
                position: 'top-end',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }
        
        items.push({
            id: repuesto.id,
            nombre: repuesto.nombre,
            categoria: repuesto.categoria,
            precio: repuesto.precio_unitario,
            stock: repuesto.stock,
            cantidad: 1
        });
        
        renderCarrito();
        renderRepuestos(repuestos.filter(r => r.stock > 0));
        
        Swal.fire({
            icon: 'success',
            title: 'Producto agregado',
            toast: true,
            position: 'top-end',
            timer: 1500,
            showConfirmButton: false
        });
    }
    
    // Render carrito
    function renderCarrito() {
        carritoBody.innerHTML = '';
        
        if (items.length === 0) {
            carritoTable.classList.add('hidden');
            carritoVacio.classList.remove('hidden');
            btnLimpiarCarrito.classList.add('hidden');
        } else {
            carritoTable.classList.remove('hidden');
            carritoVacio.classList.add('hidden');
            btnLimpiarCarrito.classList.remove('hidden');
            
            items.forEach((item, index) => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 transition-colors';
                tr.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-[#e6f7f6] rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                <svg class="w-5 h-5 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">${item.nombre}</p>
                                <p class="text-xs text-gray-500">${item.categoria}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">S/ ${item.precio.toFixed(2)}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center space-x-2">
                            <button type="button" onclick="updateCantidad(${item.id}, ${item.cantidad - 1})" ${item.cantidad <= 1 ? 'disabled' : ''}
                                    class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center hover:bg-[#218786] hover:text-white disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            </button>
                            <span class="w-12 text-center font-bold text-gray-900">${item.cantidad}</span>
                            <button type="button" onclick="updateCantidad(${item.id}, ${item.cantidad + 1})" ${item.cantidad >= item.stock ? 'disabled' : ''}
                                    class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center hover:bg-[#218786] hover:text-white disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            </button>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm ${item.stock <= 5 ? 'text-red-600 font-semibold' : 'text-gray-500'}">${item.stock}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">S/ ${(item.precio * item.cantidad).toFixed(2)}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <button type="button" onclick="removeItem(${item.id})" class="text-red-600 hover:text-white hover:bg-red-600 p-2 rounded-lg transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </td>
                    <input type="hidden" name="repuestos[${index}][id]" value="${item.id}">
                    <input type="hidden" name="repuestos[${index}][cantidad]" value="${item.cantidad}">
                    <input type="hidden" name="repuestos[${index}][precio]" value="${item.precio}">`;
                carritoBody.appendChild(tr);
            });
        }
        
        updateTotales();
    }
    
    // Funciones globales
    window.updateCantidad = function(itemId, nuevaCantidad) {
        const item = items.find(i => i.id === itemId);
        if (!item) return;
        
        nuevaCantidad = parseInt(nuevaCantidad);
        if (nuevaCantidad < 1) nuevaCantidad = 1;
        if (nuevaCantidad > item.stock) {
            nuevaCantidad = item.stock;
            Swal.fire({
                icon: 'warning',
                title: 'Stock máximo alcanzado',
                toast: true,
                position: 'top-end',
                timer: 2000,
                showConfirmButton: false
            });
        }
        
        item.cantidad = nuevaCantidad;
        renderCarrito();
    };
    
    window.removeItem = function(itemId) {
        Swal.fire({
            title: '¿Eliminar producto?',
            text: 'Se quitará del carrito',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                items = items.filter(i => i.id !== itemId);
                renderCarrito();
                if (!modalBuscarRepuesto.classList.contains('hidden')) {
                    filtrarRepuestos();
                }
            }
        });
    };
    
    // Limpiar carrito
    btnLimpiarCarrito.addEventListener('click', function() {
        Swal.fire({
            title: '¿Limpiar carrito?',
            text: 'Se eliminarán todos los productos',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#218786',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, limpiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                items = [];
                renderCarrito();
            }
        });
    });
    
    // Update totales
    function updateTotales() {
        const total = items.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
        const totalCantidad = items.reduce((sum, item) => sum + item.cantidad, 0);
        
        document.getElementById('totalVenta').textContent = `S/ ${total.toFixed(2)}`;
        document.getElementById('totalDisplay').textContent = `S/ ${total.toFixed(2)}`;
        document.getElementById('totalInput').value = total.toFixed(2);
        document.getElementById('itemsCount').textContent = items.length;
        document.getElementById('productosCount').textContent = items.length;
        document.getElementById('articulosCount').textContent = totalCantidad;
        
        updateBtnConfirmar();
    }
    
    // Nuevo cliente - validaciones
    document.getElementById('newClientDni').addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').substring(0, 8);
    });
    
    document.getElementById('newClientTelefono').addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').substring(0, 9);
    });
    
    function clearModalInputs() {
        document.getElementById('newClientDni').value = '';
        document.getElementById('newClientNombre').value = '';
        document.getElementById('newClientTelefono').value = '';
        document.getElementById('newClientDireccion').value = '';
    }
    
    // Guardar nuevo cliente
    btnGuardarCliente.addEventListener('click', async function() {
        const dni = document.getElementById('newClientDni').value.trim();
        const nombre = document.getElementById('newClientNombre').value.trim();
        const telefono = document.getElementById('newClientTelefono').value.trim();
        const direccion = document.getElementById('newClientDireccion').value.trim();
        
        if (!dni || dni.length < 8) {
            Swal.fire({icon: 'error', title: 'Error', text: 'El DNI debe tener 8 dígitos'});
            return;
        }
        if (!nombre) {
            Swal.fire({icon: 'error', title: 'Error', text: 'El nombre es obligatorio'});
            return;
        }
        if (!telefono || telefono.length < 7) {
            Swal.fire({icon: 'error', title: 'Error', text: 'El teléfono debe tener al menos 7 dígitos'});
            return;
        }
        
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const response = await fetch("/clientes/storeQuick", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({ dni, nombre, telefono, direccion })
            });
            
            const data = await response.json();
            
            if (data.success) {
                selectCliente(data.cliente);
                modalNuevoCliente.classList.add('hidden');
                clearModalInputs();
                await cargarTodosClientes();
            } else {
                let errorMsg = 'Error al guardar';
                if (data.errors) errorMsg = Object.values(data.errors).flat().join('\n');
                Swal.fire({icon: 'error', title: 'Error', text: errorMsg});
            }
        } catch (error) {
            console.error(error);
            Swal.fire({icon: 'error', title: 'Error', text: 'Ocurrió un error en el servidor'});
        }
    });
    
    // Método de pago
    const metodoPagoInputs = document.querySelectorAll('.metodo-pago');
    metodoPagoInputs.forEach(input => {
        input.addEventListener('change', function() {
            document.querySelectorAll('.metodo-pago-card').forEach(card => {
                card.classList.remove('border-[#218786]', 'bg-[#e6f7f6]');
                card.classList.add('border-gray-300');
            });
            
            if (this.checked) {
                const card = this.parentElement.querySelector('.metodo-pago-card');
                card.classList.remove('border-gray-300');
                card.classList.add('border-[#218786]', 'bg-[#e6f7f6]');
            }
            
            updateBtnConfirmar();
        });
    });
    
    // Update botón confirmar
    function updateBtnConfirmar() {
        const metodoPagoSeleccionado = document.querySelector('input[name="metodo_pago"]:checked');
        const hayItems = items.length > 0;
        const hayCliente = selectedCliente !== null;
        
        btnConfirmarVenta.disabled = !(hayItems && hayCliente && metodoPagoSeleccionado);
    }
    
    // Submit venta
    ventaForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!selectedCliente) {
            Swal.fire({icon: 'error', title: 'Error', text: 'Selecciona un cliente'});
            return;
        }
        
        if (items.length === 0) {
            Swal.fire({icon: 'error', title: 'Error', text: 'Agrega productos a la venta'});
            return;
        }
        
        const metodoPago = document.querySelector('input[name="metodo_pago"]:checked');
        if (!metodoPago) {
            Swal.fire({icon: 'error', title: 'Error', text: 'Selecciona un método de pago'});
            return;
        }
        
        const total = items.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
        
        const result = await Swal.fire({
            title: '¿Confirmar venta?',
            html: `
                <div class="text-left space-y-2 bg-gray-50 p-4 rounded-lg">
                    <p class="flex justify-between"><strong>Cliente:</strong> <span>${selectedCliente.nombre}</span></p>
                    <p class="flex justify-between"><strong>DNI:</strong> <span>${selectedCliente.dni}</span></p>
                    <p class="flex justify-between"><strong>Productos:</strong> <span>${items.length}</span></p>
                    <p class="flex justify-between"><strong>Método de pago:</strong> <span class="capitalize">${metodoPago.value}</span></p>
                    <hr class="my-2">
                    <p class="flex justify-between text-lg"><strong>Total:</strong> <span class="text-[#218786] font-bold">S/ ${total.toFixed(2)}</span></p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#218786',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, confirmar venta',
            cancelButtonText: 'Cancelar'
        });
        
        if (result.isConfirmed) {
            btnConfirmarVenta.disabled = true;
            btnConfirmarVenta.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Procesando...</span>';
            
            Swal.fire({
                title: 'Procesando venta...',
                html: 'Por favor espera un momento',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            this.submit();
        }
    });
    
    // Advertencia al salir
    window.addEventListener('beforeunload', function(e) {
        if (items.length > 0) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
}