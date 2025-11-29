{{-- resources/views/ventas/partials/total-finalizar.blade.php --}}
<div class="section-card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="total-section px-6 py-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white text-opacity-90 text-sm font-medium">Total de la Venta</p>
                <p class="text-4xl font-bold text-white mt-1" id="totalDisplay">S/ 0.00</p>
            </div>
            <div class="text-right text-white text-opacity-90">
                <p class="text-sm"><span id="productosCount">0</span> productos</p>
                <p class="text-sm"><span id="articulosCount">0</span> artículos</p>
            </div>
        </div>
    </div>
    
    <div class="p-6 bg-gray-50">
        <!-- Método de pago -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-3">
                Método de Pago <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label class="relative cursor-pointer group">
                    <input type="radio" name="metodo_pago" value="efectivo" class="sr-only metodo-pago" required>
                    <div class="metodo-pago-card flex items-center justify-center p-4 border-2 rounded-xl transition-all duration-200 border-gray-300 group-hover:border-[#218786] bg-white">
                        <svg class="w-6 h-6 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="font-semibold text-gray-900">Efectivo</span>
                    </div>
                </label>
                <label class="relative cursor-pointer group">
                    <input type="radio" name="metodo_pago" value="tarjeta" class="sr-only metodo-pago" required>
                    <div class="metodo-pago-card flex items-center justify-center p-4 border-2 rounded-xl transition-all duration-200 border-gray-300 group-hover:border-[#218786] bg-white">
                        <svg class="w-6 h-6 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <span class="font-semibold text-gray-900">Tarjeta</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Acciones -->
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="{{ route('ventas.index') }}" 
               class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-100 transition-colors duration-200 text-center inline-flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Cancelar
            </a>
            
            <button type="submit" 
                    id="btnConfirmarVenta"
                    disabled
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-[#218786] to-[#1a6d6c] text-white font-semibold rounded-lg transition-all duration-200 inline-flex items-center justify-center shadow-md disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-lg hover:scale-105">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Confirmar Venta</span>
            </button>
        </div>
    </div>
    
    <input type="hidden" name="total" id="totalInput" value="0">
</div>