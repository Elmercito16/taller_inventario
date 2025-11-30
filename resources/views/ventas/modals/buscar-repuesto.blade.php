<div id="modalBuscarRepuesto" class="hidden fixed inset-0 z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black opacity-50" id="overlayRepuesto"></div>
        
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[80vh] flex flex-col">
            <!-- Header -->
            <div class="bg-gradient-to-r from-[#218786] to-[#1d7874] px-4 py-3 rounded-t-lg flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">Agregar Productos</h3>
                <button type="button" id="btnCerrarModalRepuesto" class="text-white hover:bg-white hover:bg-opacity-20 rounded p-1">✕</button>
            </div>

            <!-- Filtros -->
            <div class="p-4 border-b grid grid-cols-1 md:grid-cols-2 gap-2">
                <input type="text" id="buscarRepuestoInput" placeholder="Buscar por nombre..."
                       class="px-3 py-2 border rounded focus:ring-2 focus:ring-[#218786]">
                <select id="filtroCategoria" class="px-3 py-2 border rounded focus:ring-2 focus:ring-[#218786]">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias ?? [] as $categoria)
                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Lista -->
            <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
                <!-- Se ajustó el grid para que sea más responsive (2 col en móvil, 3 en mediano, 4 en grande) -->
                <div id="listaRepuestosGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"></div>
                <div id="sinResultadosRepuestos" class="hidden text-center py-8 text-gray-500">No se encontraron repuestos</div>
            </div>
        </div>
    </div>
</div>

@php
    $repuestosJs = $repuestos->map(function($r){
        return [
            'id' => $r->id,
            'nombre' => $r->nombre,
            'categoria_id' => $r->categoria_id ?? 0,
            'precio_unitario' => (float) $r->precio_unitario,
            'stock' => (int) $r->cantidad,
            // 1. AÑADIDO: Mapeamos la imagen con la ruta correcta
            'imagen' => $r->imagen ? asset('storage/' . $r->imagen) : null,
        ];
    })->values();
@endphp

<script>
document.addEventListener('DOMContentLoaded', function() {
    const repuestos = @json($repuestosJs);
    
    window.abrirModalRepuestos = function() {
        document.getElementById('modalBuscarRepuesto').classList.remove('hidden');
        renderRepuestos(repuestos.filter(r => r.stock > 0));
    };
    
    document.getElementById('btnCerrarModalRepuesto').onclick = () => {
        document.getElementById('modalBuscarRepuesto').classList.add('hidden');
    };
    
    document.getElementById('overlayRepuesto').onclick = () => {
        document.getElementById('modalBuscarRepuesto').classList.add('hidden');
    };
    
    function filtrarRepuestos() {
        const query = document.getElementById('buscarRepuestoInput').value.toLowerCase();
        const categoriaId = parseInt(document.getElementById('filtroCategoria').value) || 0;
        
        let filtrados = repuestos.filter(r => r.stock > 0);
        
        if (query) filtrados = filtrados.filter(r => r.nombre.toLowerCase().includes(query));
        if (categoriaId > 0) filtrados = filtrados.filter(r => r.categoria_id === categoriaId);
        
        renderRepuestos(filtrados);
    }
    
    document.getElementById('buscarRepuestoInput').addEventListener('input', filtrarRepuestos);
    document.getElementById('filtroCategoria').addEventListener('change', filtrarRepuestos);
    
    function renderRepuestos(lista) {
        const grid = document.getElementById('listaRepuestosGrid');
        const sinResultados = document.getElementById('sinResultadosRepuestos');
        
        grid.innerHTML = '';
        
        if (lista.length === 0) {
            grid.classList.add('hidden');
            sinResultados.classList.remove('hidden');
            return;
        }
        
        grid.classList.remove('hidden');
        sinResultados.classList.add('hidden');
        
        lista.forEach(repuesto => {
            const yaAgregado = window.carritoItems && window.carritoItems.find(i => i.id === repuesto.id);
            const div = document.createElement('div');
            
            // Estilos de la tarjeta
            div.className = `group border rounded-lg overflow-hidden hover:shadow-md transition-all duration-200 flex flex-col bg-white ${yaAgregado ? 'border-gray-300 opacity-60' : 'border-gray-200 hover:border-[#218786] cursor-pointer'}`;
            
            // 2. AÑADIDO: Lógica para mostrar imagen o ícono por defecto
            let imagenHtml = '';
            if (repuesto.imagen) {
                imagenHtml = `<img src="${repuesto.imagen}" class="w-full h-32 object-cover group-hover:scale-105 transition-transform duration-500" alt="${repuesto.nombre}">`;
            } else {
                imagenHtml = `
                    <div class="w-full h-32 bg-gray-100 flex items-center justify-center text-gray-300 group-hover:text-[#218786] transition-colors">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                `;
            }

            div.innerHTML = `
                <div class="relative overflow-hidden border-b border-gray-100">
                    ${imagenHtml}
                    <!-- Badge de Stock Flotante -->
                    <div class="absolute top-2 right-2 bg-white/90 backdrop-blur px-2 py-0.5 rounded text-xs font-bold ${repuesto.stock <= 5 ? 'text-red-600' : 'text-green-600'} shadow-sm border border-gray-100">
                        Stock: ${repuesto.stock}
                    </div>
                </div>
                
                <div class="p-3 flex-1 flex flex-col">
                    <p class="font-bold text-sm text-gray-800 mb-1 line-clamp-2 h-10 group-hover:text-[#218786] transition-colors">${repuesto.nombre}</p>
                    
                    <div class="mt-auto flex justify-between items-end pt-2 border-t border-gray-50">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold">Precio</p>
                            <p class="text-lg font-bold text-[#218786]">S/ ${repuesto.precio_unitario.toFixed(2)}</p>
                        </div>
                        <!-- Botón visual '+' -->
                        <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-[#218786] group-hover:text-white transition-colors shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                    </div>
                    ${yaAgregado ? '<div class="mt-2 bg-green-50 text-green-700 text-xs font-bold text-center py-1 rounded border border-green-100">✓ Agregado</div>' : ''}
                </div>
            `;
            
            if (!yaAgregado) {
                div.onclick = () => {
                    window.addRepuesto && window.addRepuesto(repuesto);
                    renderRepuestos(lista);
                };
            }
            
            grid.appendChild(div);
        });
    }
});
</script>