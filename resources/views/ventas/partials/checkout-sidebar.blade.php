<div class="w-full lg:w-96 flex flex-col gap-6 h-full overflow-y-auto custom-scrollbar pr-1">
    
    <form id="ventaForm" action="{{ route('ventas.store') }}" method="POST" class="contents">
        @csrf
        <input type="hidden" name="cliente_id" :value="selectedClient?.id || ''">
        <input type="hidden" name="total" :value="cartTotal">
        
        <!-- Inputs ocultos para que Laravel reciba los items -->
        <template x-for="(item, index) in items" :key="index">
            <div>
                <input type="hidden" :name="'repuestos['+index+'][id]'" :value="item.id">
                <input type="hidden" :name="'repuestos['+index+'][cantidad]'" :value="item.cantidad">
                <input type="hidden" :name="'repuestos['+index+'][precio]'" :value="item.precio">
            </div>
        </template>

        <!-- 1. TARJETA CLIENTE -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Cliente
                </h3>
                <button type="button" @click="showClientModal = true" class="text-xs font-bold text-[#218786] hover:underline">
                    <span x-text="selectedClient ? 'Cambiar' : 'Buscar / Nuevo'"></span>
                </button>
            </div>

            <!-- CASO 1: Cliente Seleccionado -->
            <div x-show="selectedClient" class="bg-blue-50 border border-blue-100 rounded-xl p-3 flex items-center gap-3" x-cloak>
                <div class="w-10 h-10 rounded-full bg-[#218786] text-white flex items-center justify-center font-bold text-sm">
                    <span x-text="selectedClient?.nombre.substring(0,1)"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-gray-900 text-sm truncate" x-text="selectedClient?.nombre"></p>
                    <p class="text-xs text-gray-500" x-text="selectedClient?.dni || 'Sin DNI'"></p>
                </div>
            </div>

            <!-- CASO 2: Sin Cliente -->
            <div x-show="!selectedClient">
                <button type="button" 
                        @click="showClientModal = true"
                        class="w-full py-4 border-2 border-dashed border-gray-300 rounded-xl text-gray-500 hover:border-[#218786] hover:text-[#218786] hover:bg-teal-50 transition-all flex flex-col items-center justify-center gap-2 group">
                    <div class="p-2 bg-gray-100 rounded-full group-hover:bg-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <span class="text-sm font-bold">Seleccionar Cliente</span>
                </button>
            </div>
        </div>

        <!-- 2. RESUMEN Y PAGO -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex-1 flex flex-col">
            <h3 class="font-bold text-gray-800 text-sm uppercase mb-5 tracking-wide">Resumen de Pago</h3>

            <div class="space-y-3 mb-6 flex-1">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span class="font-medium">S/ <span x-text="(cartTotal / 1.18).toFixed(2)"></span></span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>IGV (18%)</span>
                    <span class="font-medium">S/ <span x-text="(cartTotal - (cartTotal / 1.18)).toFixed(2)"></span></span>
                </div>
                <div class="border-t border-dashed border-gray-200 my-2 pt-2">
                    <div class="flex justify-between items-end">
                        <span class="text-base font-bold text-gray-800">Total a Pagar</span>
                        <span class="text-3xl font-black text-[#218786]">S/ <span x-text="cartTotal.toFixed(2)"></span></span>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Método de Pago</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer relative">
                        <input type="radio" name="metodo_pago" value="efectivo" class="peer sr-only" checked>
                        <div class="py-3 px-2 text-center border-2 rounded-xl text-sm font-bold text-gray-500 bg-white peer-checked:bg-[#218786] peer-checked:text-white peer-checked:border-[#218786] transition-all shadow-sm hover:border-gray-300">
                            Efectivo
                        </div>
                    </label>
                    <label class="cursor-pointer relative">
                        <input type="radio" name="metodo_pago" value="tarjeta" class="peer sr-only">
                        <div class="py-3 px-2 text-center border-2 rounded-xl text-sm font-bold text-gray-500 bg-white peer-checked:bg-[#218786] peer-checked:text-white peer-checked:border-[#218786] transition-all shadow-sm hover:border-gray-300">
                            Tarjeta
                        </div>
                    </label>
                </div>
            </div>

            <button type="button" @click="submitSale" 
                    :disabled="items.length === 0 || isSubmitting"
                    class="w-full py-4 bg-[#218786] hover:bg-[#1a6d6c] text-white rounded-xl font-bold text-lg shadow-lg shadow-teal-500/30 transition-all transform active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none flex justify-center items-center">
                <span x-show="!isSubmitting">CONFIRMAR VENTA</span>
                <svg x-show="isSubmitting" class="animate-spin ml-2 h-5 w-5 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </button>
        </div>
    </form>
</div>