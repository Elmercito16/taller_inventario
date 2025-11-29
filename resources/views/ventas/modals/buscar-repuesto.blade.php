{{-- resources/views/ventas/modals/nuevo-cliente.blade.php --}}
<div id="modalNuevoCliente" class="hidden fixed inset-0 z-[60] overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 py-6">
        <div class="fixed inset-0 transition-opacity" style="background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(2px);" id="overlayNuevoCliente"></div>
        
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
            <!-- Header -->
            <div class="bg-gradient-to-r from-[#218786] to-[#1d7874] px-6 py-5 rounded-t-2xl">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Registrar Nuevo Cliente
                </h3>
                <p class="text-white text-opacity-90 text-sm mt-1">Completa los datos requeridos</p>
            </div>
            
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            DNI <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="newClientDni" 
                               maxlength="8"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-colors"
                               placeholder="8 dígitos">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Nombre Completo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="newClientNombre" 
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-colors"
                               placeholder="Nombres y apellidos">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Teléfono <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="newClientTelefono"
                               maxlength="9"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-colors"
                               placeholder="9 dígitos">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Dirección (Opcional)
                        </label>
                        <input type="text" 
                               id="newClientDireccion"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#218786] focus:border-[#218786] transition-colors"
                               placeholder="Dirección completa">
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 rounded-b-2xl flex flex-col-reverse sm:flex-row gap-3">
                <button type="button" 
                        id="btnCancelarNuevoCliente" 
                        class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-100 transition-colors">
                    Cancelar
                </button>
                <button type="button" 
                        id="btnGuardarCliente" 
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-[#218786] to-[#1d7874] text-white font-semibold rounded-lg hover:shadow-lg transition-all">
                    Guardar Cliente
                </button>
            </div>
        </div>
    </div>
</div>