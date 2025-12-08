{{-- resources/views/ventas/modals/nuevo-cliente.blade.php --}}
<div id="modalNuevoCliente" class="hidden fixed inset-0 z-[60] overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
        <!-- Overlay con blur -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" id="overlayNuevoCliente"></div>
        
        <!-- Modal -->
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-5 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Nuevo Cliente</h3>
                            <p class="text-sm text-white/80">Registra un cliente rápidamente</p>
                        </div>
                    </div>
                    <button type="button" 
                            id="btnCerrarNuevoClienteHeader"
                            class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Form -->
            <div class="p-6 space-y-5">
                <!-- DNI -->
                <div class="group">
                    <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                        </svg>
                        DNI
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="newClientDni" 
                               maxlength="8"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all font-medium"
                               placeholder="12345678">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-xs text-gray-400 font-medium" id="dniCounter">0/8</span>
                        </div>
                    </div>
                </div>

                <!-- Nombre -->
                <div class="group">
                    <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Nombre Completo
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="newClientNombre"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all font-medium"
                           placeholder="Juan Pérez García">
                </div>

                <!-- Teléfono -->
                <div class="group">
                    <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Teléfono
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="newClientTelefono" 
                               maxlength="9"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all font-medium"
                               placeholder="987654321">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-xs text-gray-400 font-medium" id="telCounter">0/9</span>
                        </div>
                    </div>
                </div>

                <!-- Dirección -->
                <div class="group">
                    <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#218786]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Dirección
                        <span class="text-gray-400 text-xs font-normal">(Opcional)</span>
                    </label>
                    <textarea id="newClientDireccion"
                              rows="2"
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all font-medium resize-none"
                              placeholder="Av. Principal 123, Distrito, Ciudad"></textarea>
                </div>

                
            </div>
            
            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 rounded-b-2xl flex gap-3 border-t border-gray-200">
                <button type="button" 
                        id="btnCancelarNuevoCliente" 
                        class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-100 transition-all flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cancelar
                </button>
                <button type="button" 
                        id="btnGuardarCliente" 
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-bold rounded-xl hover:shadow-lg hover:from-green-600 hover:to-green-700 transition-all flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span id="btnGuardarTexto">Guardar Cliente</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalNuevoCliente');
    const dniInput = document.getElementById('newClientDni');
    const telefonoInput = document.getElementById('newClientTelefono');
    const dniCounter = document.getElementById('dniCounter');
    const telCounter = document.getElementById('telCounter');
    
    window.abrirModalNuevoCliente = function() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        dniInput.focus();
    };
    
    function cerrarModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        document.getElementById('newClientDni').value = '';
        document.getElementById('newClientNombre').value = '';
        document.getElementById('newClientTelefono').value = '';
        document.getElementById('newClientDireccion').value = '';
        dniCounter.textContent = '0/8';
        telCounter.textContent = '0/9';
    }
    
    document.getElementById('btnCancelarNuevoCliente').onclick = cerrarModal;
    document.getElementById('btnCerrarNuevoClienteHeader').onclick = cerrarModal;
    document.getElementById('overlayNuevoCliente').onclick = cerrarModal;
    
    // Cerrar con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            cerrarModal();
        }
    });
    
    // Validación y contador DNI
    dniInput.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').substring(0, 8);
        dniCounter.textContent = `${this.value.length}/8`;
        if (this.value.length === 8) {
            dniCounter.classList.add('text-green-600', 'font-bold');
            dniCounter.classList.remove('text-gray-400');
        } else {
            dniCounter.classList.remove('text-green-600', 'font-bold');
            dniCounter.classList.add('text-gray-400');
        }
    });
    
    // Validación y contador teléfono
    telefonoInput.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').substring(0, 9);
        telCounter.textContent = `${this.value.length}/9`;
        if (this.value.length >= 7) {
            telCounter.classList.add('text-green-600', 'font-bold');
            telCounter.classList.remove('text-gray-400');
        } else {
            telCounter.classList.remove('text-green-600', 'font-bold');
            telCounter.classList.add('text-gray-400');
        }
    });
    
    document.getElementById('btnGuardarCliente').onclick = async function() {
        const dni = dniInput.value.trim();
        const nombre = document.getElementById('newClientNombre').value.trim();
        const telefono = telefonoInput.value.trim();
        const direccion = document.getElementById('newClientDireccion').value.trim();
        
        // Validaciones
        if (!dni || dni.length < 8) {
            Swal.fire({
                icon: 'error',
                title: 'DNI incompleto',
                text: 'El DNI debe tener exactamente 8 dígitos',
                confirmButtonColor: '#218786'
            });
            dniInput.focus();
            return;
        }
        if (!nombre) {
            Swal.fire({
                icon: 'error',
                title: 'Nombre requerido',
                text: 'El nombre completo es obligatorio',
                confirmButtonColor: '#218786'
            });
            document.getElementById('newClientNombre').focus();
            return;
        }
        if (!telefono || telefono.length < 7) {
            Swal.fire({
                icon: 'error',
                title: 'Teléfono incompleto',
                text: 'El teléfono debe tener al menos 7 dígitos',
                confirmButtonColor: '#218786'
            });
            telefonoInput.focus();
            return;
        }
        
        // Deshabilitar botón mientras guarda
        const btnGuardar = document.getElementById('btnGuardarCliente');
        const btnTexto = document.getElementById('btnGuardarTexto');
        const textoOriginal = btnTexto.textContent;
        
        btnGuardar.disabled = true;
        btnTexto.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                ircle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Guardando...
        `;
        
        try {
            const res = await fetch("{{ route('clientes.storeQuick') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ dni, nombre, telefono, direccion })
            });
            
            const data = await res.json();
            
            if (data.success) {
                // Seleccionar el cliente automáticamente
                if (window.selectCliente) {
                    window.selectCliente(data.cliente);
                }
                
                cerrarModal();
                
                // Mensaje de éxito
                Swal.fire({
                    icon: 'success',
                    title: '¡Cliente registrado!',
                    html: `<p class="text-gray-600"><strong>${nombre}</strong> se agregó correctamente y fue seleccionado.</p>`,
                    confirmButtonColor: '#218786',
                    timer: 3000,
                    showConfirmButton: true
                });
            } else {
                // Mostrar errores
                let errorMsg = 'Error al guardar el cliente';
                if (data.errors) {
                    errorMsg = Object.values(data.errors).flat().join('\n');
                } else if (data.message) {
                    errorMsg = data.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    text: errorMsg,
                    confirmButtonColor: '#218786'
                });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor. Por favor verifica tu conexión a internet.',
                confirmButtonColor: '#218786'
            });
        } finally {
            // Rehabilitar botón
            btnGuardar.disabled = false;
            btnTexto.textContent = textoOriginal;
        }
    };
});
</script>
