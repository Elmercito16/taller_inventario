{{-- resources/views/ventas/modals/buscar-cliente.blade.php --}}
<div id="modalBuscarCliente" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
        <!-- Overlay con blur -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" id="overlayCliente"></div>
        
        <!-- Modal -->
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[85vh] flex flex-col transform transition-all">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-[#218786] to-[#1a6d6c] px-6 py-5 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Seleccionar Cliente</h3>
                            <p class="text-sm text-white/80">Busca y selecciona un cliente existente</p>
                        </div>
                    </div>
                    <button type="button" 
                            id="btnCerrarModalCliente" 
                            class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Búsqueda -->
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" 
                               id="buscarClienteInput" 
                               placeholder="Buscar por nombre o DNI..."
                               class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-all text-sm font-medium">
                    </div>
                    <button type="button" 
                            id="btnNuevoClienteModal"
                            class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-bold rounded-xl hover:shadow-lg hover:from-green-600 hover:to-green-700 transition-all whitespace-nowrap">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Nuevo Cliente
                    </button>
                </div>
            </div>

            <!-- Lista de Clientes -->
            <div class="flex-1 overflow-y-auto p-6">
                <!-- Grid de clientes -->
                <div id="listaClientesGrid" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>
                
                <!-- Sin resultados -->
                <div id="sinResultadosClientes" class="hidden text-center py-16">
                    <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">No se encontraron clientes</h3>
                    <p class="text-sm text-gray-500 mb-6">Intenta con otro término de búsqueda</p>
                    <button type="button" 
                            onclick="document.getElementById('btnNuevoClienteModal').click()"
                            class="inline-flex items-center px-6 py-3 bg-[#218786] text-white font-bold rounded-xl hover:bg-[#1a6d6c] transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Crear Nuevo Cliente
                    </button>
                </div>
                
                <!-- Loading -->
                <div id="loadingClientes" class="hidden text-center py-16">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-[#218786] mb-4"></div>
                    <p class="text-sm font-medium text-gray-600">Cargando clientes...</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-2xl">
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <span id="contadorClientes" class="font-medium">0 clientes encontrados</span>
                    <button type="button" 
                            id="btnCerrarModalClienteFooter"
                            class="px-6 py-2.5 border-2 border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-100 transition-all">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let todosClientes = [];
    
    window.abrirModalClientes = async function() {
        const modal = document.getElementById('modalBuscarCliente');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        await cargarClientes();
    };
    
    function cerrarModal() {
        const modal = document.getElementById('modalBuscarCliente');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        document.getElementById('buscarClienteInput').value = '';
    }
    
    document.getElementById('btnCerrarModalCliente').onclick = cerrarModal;
    document.getElementById('btnCerrarModalClienteFooter').onclick = cerrarModal;
    document.getElementById('overlayCliente').onclick = cerrarModal;
    
    // Cerrar con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModal();
        }
    });
    
    async function cargarClientes() {
        const loading = document.getElementById('loadingClientes');
        const grid = document.getElementById('listaClientesGrid');
        
        loading.classList.remove('hidden');
        grid.classList.add('hidden');
        
        try {
            const res = await fetch('/clientes/search?q=');
            todosClientes = await res.json();
            renderClientes(todosClientes);
        } catch (error) {
            console.error(error);
            document.getElementById('sinResultadosClientes').classList.remove('hidden');
        }
        loading.classList.add('hidden');
    }
    
    document.getElementById('buscarClienteInput').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const filtrados = query ? todosClientes.filter(c => 
            c.nombre.toLowerCase().includes(query) || c.dni.includes(query)
        ) : todosClientes;
        renderClientes(filtrados);
    });
    
    function renderClientes(clientes) {
        const grid = document.getElementById('listaClientesGrid');
        const sinResultados = document.getElementById('sinResultadosClientes');
        const contador = document.getElementById('contadorClientes');
        
        grid.innerHTML = '';
        contador.textContent = `${clientes.length} cliente${clientes.length !== 1 ? 's' : ''} encontrado${clientes.length !== 1 ? 's' : ''}`;
        
        if (clientes.length === 0) {
            grid.classList.add('hidden');
            sinResultados.classList.remove('hidden');
            return;
        }
        
        grid.classList.remove('hidden');
        sinResultados.classList.add('hidden');
        
        clientes.forEach(cliente => {
            const div = document.createElement('div');
            div.className = 'group p-4 border-2 border-gray-200 rounded-xl hover:border-[#218786] hover:shadow-lg cursor-pointer transition-all transform hover:-translate-y-1 bg-white';
            div.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-[#218786] to-[#1a6d6c] text-white flex items-center justify-center font-bold text-xl shadow-lg group-hover:scale-110 transition-transform">
                        ${cliente.nombre.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-900 text-base truncate group-hover:text-[#218786] transition-colors">${cliente.nombre}</p>
                        <p class="text-sm text-gray-500 flex items-center gap-1 mt-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                            DNI: <span class="font-semibold">${cliente.dni}</span>
                        </p>
                        ${cliente.telefono ? `
                        <p class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            ${cliente.telefono}
                        </p>
                        ` : ''}
                    </div>
                    <svg class="w-6 h-6 text-gray-400 group-hover:text-[#218786] group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>`;
            div.onclick = () => {
                if (window.selectCliente) {
                    window.selectCliente(cliente);
                    cerrarModal();
                }
            };
            grid.appendChild(div);
        });
    }
    
    document.getElementById('btnNuevoClienteModal').onclick = () => {
        cerrarModal();
        window.abrirModalNuevoCliente && window.abrirModalNuevoCliente();
    };
});
</script>
