{{-- resources/views/ventas/modals/nuevo-cliente.blade.php --}}
<div id="modalNuevoCliente" class="hidden fixed inset-0 z-[60]">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black opacity-50" id="overlayNuevoCliente"></div>
        
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md">
            <!-- Header -->
            <div class="bg-gradient-to-r from-[#218786] to-[#1d7874] px-4 py-3 rounded-t-lg">
                <h3 class="text-lg font-bold text-white">Nuevo Cliente</h3>
            </div>
            
            <div class="p-4 space-y-3">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">DNI *</label>
                    <input type="text" id="newClientDni" maxlength="8"
                           class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-[#218786]"
                           placeholder="8 dígitos">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nombre *</label>
                    <input type="text" id="newClientNombre"
                           class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-[#218786]"
                           placeholder="Nombre completo">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Teléfono *</label>
                    <input type="text" id="newClientTelefono" maxlength="9"
                           class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-[#218786]"
                           placeholder="9 dígitos">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Dirección</label>
                    <input type="text" id="newClientDireccion"
                           class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-[#218786]"
                           placeholder="Opcional">
                </div>
            </div>
            
            <div class="bg-gray-50 px-4 py-3 rounded-b-lg flex gap-2">
                <button type="button" id="btnCancelarNuevoCliente" 
                        class="flex-1 px-4 py-2 border-2 border-gray-300 rounded hover:bg-gray-100 font-bold">
                    Cancelar
                </button>
                <button type="button" id="btnGuardarCliente" 
                        class="flex-1 px-4 py-2 bg-[#218786] text-white rounded hover:bg-[#1d7874] font-bold">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    window.abrirModalNuevoCliente = function() {
        document.getElementById('modalNuevoCliente').classList.remove('hidden');
        document.getElementById('newClientDni').focus();
    };
    
    function cerrarModal() {
        document.getElementById('modalNuevoCliente').classList.add('hidden');
        document.getElementById('newClientDni').value = '';
        document.getElementById('newClientNombre').value = '';
        document.getElementById('newClientTelefono').value = '';
        document.getElementById('newClientDireccion').value = '';
    }
    
    document.getElementById('btnCancelarNuevoCliente').onclick = cerrarModal;
    document.getElementById('overlayNuevoCliente').onclick = cerrarModal;
    
    document.getElementById('newClientDni').addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').substring(0, 8);
    });
    
    document.getElementById('newClientTelefono').addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').substring(0, 9);
    });
    
    document.getElementById('btnGuardarCliente').onclick = async function() {
        const dni = document.getElementById('newClientDni').value.trim();
        const nombre = document.getElementById('newClientNombre').value.trim();
        const telefono = document.getElementById('newClientTelefono').value.trim();
        const direccion = document.getElementById('newClientDireccion').value.trim();
        
        // Validaciones
        if (!dni || dni.length < 8) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El DNI debe tener 8 dígitos',
                confirmButtonColor: '#218786'
            });
            return;
        }
        if (!nombre) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El nombre es obligatorio',
                confirmButtonColor: '#218786'
            });
            return;
        }
        if (!telefono || telefono.length < 7) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El teléfono debe tener al menos 7 dígitos',
                confirmButtonColor: '#218786'
            });
            return;
        }
        
        // Deshabilitar botón mientras guarda
        const btnGuardar = document.getElementById('btnGuardarCliente');
        const textoOriginal = btnGuardar.textContent;
        btnGuardar.disabled = true;
        btnGuardar.textContent = 'Guardando...';
        
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
                    text: `${nombre} se agregó correctamente`,
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
                    title: 'Error',
                    text: errorMsg,
                    confirmButtonColor: '#218786'
                });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor. Verifica tu conexión.',
                confirmButtonColor: '#218786'
            });
        } finally {
            // Rehabilitar botón
            btnGuardar.disabled = false;
            btnGuardar.textContent = textoOriginal;
        }
    };
});
</script>