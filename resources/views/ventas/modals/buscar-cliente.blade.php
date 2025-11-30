{{-- resources/views/ventas/modals/buscar-cliente.blade.php --}}
<div id="modalBuscarCliente" class="hidden fixed inset-0 z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black opacity-50" id="overlayCliente"></div>
        
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-[80vh] flex flex-col">
            <!-- Header -->
            <div class="bg-gradient-to-r from-[#218786] to-[#1d7874] px-4 py-3 rounded-t-lg flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">Seleccionar Cliente</h3>
                <button type="button" id="btnCerrarModalCliente" class="text-white hover:bg-white hover:bg-opacity-20 rounded p-1">✕</button>
            </div>

            <!-- Búsqueda -->
            <div class="p-4 border-b flex gap-2">
                <input type="text" id="buscarClienteInput" placeholder="Buscar por nombre o DNI..."
                       class="flex-1 px-3 py-2 border rounded focus:ring-2 focus:ring-[#218786] focus:border-[#218786]">
                <button type="button" id="btnNuevoClienteModal"
                        class="px-4 py-2 bg-[#218786] text-white rounded hover:bg-[#1d7874] whitespace-nowrap">
                    + Nuevo
                </button>
            </div>

            <!-- Lista -->
            <div class="flex-1 overflow-y-auto p-4">
                <div id="listaClientesGrid" class="grid grid-cols-1 md:grid-cols-2 gap-3"></div>
                <div id="sinResultadosClientes" class="hidden text-center py-8 text-gray-500">No se encontraron clientes</div>
                <div id="loadingClientes" class="hidden text-center py-8 text-gray-500">Cargando...</div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let todosClientes = [];
    
    window.abrirModalClientes = async function() {
        document.getElementById('modalBuscarCliente').classList.remove('hidden');
        await cargarClientes();
    };
    
    document.getElementById('btnCerrarModalCliente').onclick = () => {
        document.getElementById('modalBuscarCliente').classList.add('hidden');
    };
    
    document.getElementById('overlayCliente').onclick = () => {
        document.getElementById('modalBuscarCliente').classList.add('hidden');
    };
    
    async function cargarClientes() {
        document.getElementById('loadingClientes').classList.remove('hidden');
        document.getElementById('listaClientesGrid').classList.add('hidden');
        
        try {
            const res = await fetch('/clientes/search?q=');
            todosClientes = await res.json();
            renderClientes(todosClientes);
        } catch (error) {
            console.error(error);
            document.getElementById('sinResultadosClientes').classList.remove('hidden');
        }
        document.getElementById('loadingClientes').classList.add('hidden');
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
        
        grid.innerHTML = '';
        
        if (clientes.length === 0) {
            grid.classList.add('hidden');
            sinResultados.classList.remove('hidden');
            return;
        }
        
        grid.classList.remove('hidden');
        sinResultados.classList.add('hidden');
        
        clientes.forEach(cliente => {
            const div = document.createElement('div');
            div.className = 'p-3 border-2 border-gray-200 rounded hover:border-[#218786] cursor-pointer';
            div.innerHTML = `
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-full bg-[#218786] text-white flex items-center justify-center font-bold">
                        ${cliente.nombre.charAt(0)}
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-sm">${cliente.nombre}</p>
                        <p class="text-xs text-gray-600">DNI: ${cliente.dni}</p>
                    </div>
                </div>`;
            div.onclick = () => window.selectCliente && window.selectCliente(cliente);
            grid.appendChild(div);
        });
    }
    
    document.getElementById('btnNuevoClienteModal').onclick = () => {
        window.abrirModalNuevoCliente && window.abrirModalNuevoCliente();
    };
});
</script>