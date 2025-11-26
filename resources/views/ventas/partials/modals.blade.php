<!-- ========================================== -->
<!-- MODAL 1: CATÁLOGO DE PRODUCTOS (GRANDE) -->
<!-- ========================================== -->
<div x-show="showProductModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-70 backdrop-blur-sm" @click="showProductModal = false"></div>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl w-full h-[85vh] flex flex-col modal-enter">
            
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-white flex justify-between items-center shrink-0">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Agregar Productos</h3>
                    <p class="text-sm text-gray-500">Selecciona los repuestos para la venta</p>
                </div>
                <button @click="showProductModal = false" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-full text-gray-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Barra de Filtros -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex flex-col md:flex-row gap-4 shrink-0">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" x-model="productSearch" placeholder="Buscar por nombre, código..." class="block w-full pl-10 pr-3 py-3 border-gray-300 rounded-xl focus:ring-[#218786] focus:border-[#218786] shadow-sm text-sm">
                </div>
                <div class="md:w-64">
                    <select x-model="selectedCategory" class="block w-full pl-3 pr-10 py-3 border-gray-300 focus:ring-[#218786] focus:border-[#218786] text-sm rounded-xl shadow-sm cursor-pointer">
                        <option value="">Todas las Categorías</option>
                        <template x-for="cat in categories" :key="cat"><option :value="cat" x-text="cat"></option></template>
                    </select>
                </div>
            </div>

            <!-- Grid de Productos (Scroll) -->
            <div class="flex-1 overflow-y-auto p-6 bg-white custom-scrollbar">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    <template x-for="producto in filteredProducts" :key="producto.id">
                        <div @click="addToCart(producto)" 
                             class="group bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-lg hover:border-[#218786] hover:-translate-y-1 cursor-pointer transition-all duration-200 flex flex-col overflow-hidden h-full relative">
                            
                            <!-- Badge Stock -->
                            <div class="absolute top-2 right-2 z-10">
                                <span class="px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide shadow-sm"
                                      :class="producto.stock > 5 ? 'bg-green-100 text-green-800' : (producto.stock > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')">
                                    Stock: <span x-text="producto.stock"></span>
                                </span>
                            </div>

                            <!-- Imagen Placeholder -->
                            <div class="h-32 bg-gray-100 flex items-center justify-center group-hover:bg-teal-50 transition-colors border-b border-gray-50">
                                <svg class="h-12 w-12 text-gray-300 group-hover:text-[#218786] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>

                            <div class="p-4 flex flex-col flex-1">
                                <span class="text-[10px] font-bold text-gray-400 uppercase mb-1 tracking-wider" x-text="producto.categoria"></span>
                                <h4 class="text-sm font-bold text-gray-900 line-clamp-2 leading-snug group-hover:text-[#218786] transition-colors" x-text="producto.nombre"></h4>
                                
                                <div class="mt-auto pt-3 flex justify-between items-end border-t border-gray-50">
                                    <div>
                                        <span class="text-xs text-gray-400 block">Precio</span>
                                        <p class="text-lg font-black text-gray-900">S/ <span x-text="producto.precio_unitario.toFixed(2)"></span></p>
                                    </div>
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-[#218786] group-hover:text-white transition-all shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    <!-- Mensaje Sin Resultados -->
                    <div x-show="filteredProducts.length === 0" class="col-span-full py-20 text-center text-gray-500 flex flex-col items-center" x-cloak>
                        <svg class="w-12 h-12 opacity-30 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <p>No se encontraron productos</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 2: CLIENTES (BUSCAR O CREAR) -->
    <!-- ========================================== -->
    <div x-show="showClientModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-70 backdrop-blur-sm" @click="showClientModal = false"></div>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full modal-enter" x-data="{ tab: 'search' }">
                
                <!-- Tabs -->
                <div class="flex border-b border-gray-100 bg-gray-50">
                    <button @click="tab = 'search'" class="flex-1 py-4 text-sm font-bold text-center border-b-2 transition-colors" :class="tab === 'search' ? 'border-[#218786] text-[#218786] bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'">
                        Buscar Cliente
                    </button>
                    <button @click="tab = 'create'" class="flex-1 py-4 text-sm font-bold text-center border-b-2 transition-colors" :class="tab === 'create' ? 'border-[#218786] text-[#218786] bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'">
                        Nuevo Cliente
                    </button>
                    <button @click="showClientModal = false" class="px-5 text-gray-400 hover:text-red-500 hover:bg-gray-100 border-b border-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- BUSCAR -->
                <div x-show="tab === 'search'" class="p-6 h-96 flex flex-col">
                    <div class="relative mb-4">
                        <input type="text" x-model="clientSearchQuery" @input.debounce.300ms="searchClients"
                               placeholder="Escribe nombre o DNI..." 
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#218786] focus:border-[#218786] text-sm shadow-sm"
                               autofocus>
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></div>
                    </div>

                    <div class="flex-1 overflow-y-auto custom-scrollbar pr-1 space-y-2">
                        <template x-for="c in clientResults" :key="c.id">
                            <div @click="selectClient(c)" 
                                 class="p-3 rounded-xl border border-gray-100 hover:border-[#218786] hover:bg-teal-50 cursor-pointer flex justify-between items-center group transition-all">
                                <div>
                                    <p class="font-bold text-gray-800 text-sm group-hover:text-[#218786]" x-text="c.nombre"></p>
                                    <p class="text-xs text-gray-500">DNI: <span x-text="c.dni"></span></p>
                                </div>
                                <svg class="w-5 h-5 text-gray-300 group-hover:text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </template>
                        
                        <div x-show="clientResults.length === 0 && !isSearchingClient" class="text-center py-10">
                            <p class="text-gray-400 text-sm">Escribe para buscar...</p>
                        </div>
                        <div x-show="isSearchingClient" class="text-center py-10 text-[#218786]">
                            <svg class="animate-spin h-6 w-6 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- CREAR -->
                <div x-show="tab === 'create'" class="p-6">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">DNI *</label><input type="text" x-model="newClient.dni" class="w-full border-gray-300 rounded-lg text-sm py-2.5 focus:ring-[#218786] focus:border-[#218786]"></div>
                            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Teléfono</label><input type="text" x-model="newClient.telefono" class="w-full border-gray-300 rounded-lg text-sm py-2.5 focus:ring-[#218786] focus:border-[#218786]"></div>
                        </div>
                        <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nombre Completo *</label><input type="text" x-model="newClient.nombre" class="w-full border-gray-300 rounded-lg text-sm py-2.5 focus:ring-[#218786] focus:border-[#218786]"></div>
                        <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Dirección</label><input type="text" x-model="newClient.direccion" class="w-full border-gray-300 rounded-lg text-sm py-2.5 focus:ring-[#218786] focus:border-[#218786]"></div>
                    </div>
                    <div class="mt-8 pt-4 border-t border-gray-100 flex justify-end gap-3">
                        <button @click="showClientModal = false" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg text-sm font-medium">Cancelar</button>
                        <button @click="createClient" class="px-6 py-2 bg-[#218786] text-white rounded-lg text-sm font-bold hover:bg-[#1a6d6c] shadow-md transition-all">Guardar Cliente</button>
                    </div>
                </div>
            </div>
        </div>
    </div>