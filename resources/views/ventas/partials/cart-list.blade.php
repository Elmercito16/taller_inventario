<div class="flex-1 flex flex-col bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden h-full relative">
    
    <!-- Header: Título y Botón Agregar -->
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <div>
            <h2 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-teal-50 text-[#218786] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6m0 0L5.4 5M7 13h10M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"/></svg>
                </span>
                Carrito
            </h2>
            <p class="text-xs text-gray-500 ml-10"><span x-text="items.length">0</span> items agregados</p>
        </div>
        
        <!-- BOTÓN GRANDE PARA ABRIR CATÁLOGO -->
        <button type="button" 
                @click="showProductModal = true" 
                class="px-5 py-2.5 bg-[#218786] hover:bg-[#1a6d6c] text-white rounded-xl font-bold text-sm shadow-md hover:shadow-lg transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            AGREGAR PRODUCTOS
        </button>
    </div>

    <!-- Lista de Items -->
    <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar bg-white relative">
        
        <!-- Estado Vacío -->
        <div x-show="items.length === 0" class="absolute inset-0 flex flex-col items-center justify-center text-gray-400" x-cloak>
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                <svg class="w-12 h-12 opacity-30 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <p class="text-gray-600 font-bold text-lg">Tu carrito está vacío</p>
            <p class="text-sm text-gray-400 mb-6">Presiona el botón de arriba para agregar productos</p>
        </div>

        <!-- Items Agregados -->
        <template x-for="(item, index) in items" :key="item.id">
            <div class="flex items-center p-3 border border-gray-100 rounded-xl hover:shadow-md hover:border-[#218786]/30 transition-all bg-white group">
                <!-- Icono -->
                <div class="w-12 h-12 rounded-lg bg-gray-100 flex-shrink-0 flex items-center justify-center text-gray-500 font-bold text-xs mr-4 border border-gray-200">
                    <span x-text="item.nombre.substring(0,2).toUpperCase()"></span>
                </div>
                
                <!-- Info -->
                <div class="flex-1 min-w-0 mr-4">
                    <h4 class="text-sm font-bold text-gray-900 truncate" x-text="item.nombre"></h4>
                    <p class="text-xs text-gray-500" x-text="item.categoria || 'General'"></p>
                    <p class="text-xs text-[#218786] font-bold mt-0.5">P.U: S/ <span x-text="item.precio.toFixed(2)"></span></p>
                </div>

                <!-- Controles Cantidad -->
                <div class="flex items-center bg-gray-50 rounded-lg border border-gray-200 mr-6">
                    <button @click="updateQty(index, -1)" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-[#218786] hover:bg-white rounded-l-lg transition-colors font-bold">-</button>
                    <input type="text" readonly x-model="item.cantidad" class="w-10 text-center text-sm font-bold text-gray-800 bg-transparent border-none p-0 focus:ring-0">
                    <button @click="updateQty(index, 1)" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-[#218786] hover:bg-white rounded-r-lg transition-colors font-bold">+</button>
                </div>

                <!-- Precio Total Item -->
                <div class="text-right min-w-[90px] mr-4">
                    <p class="text-base font-bold text-gray-900">S/ <span x-text="(item.precio * item.cantidad).toFixed(2)"></span></p>
                </div>

                <!-- Eliminar -->
                <button @click="removeItem(index)" class="text-gray-300 hover:text-red-500 hover:bg-red-50 p-2 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </template>
    </div>
</div>