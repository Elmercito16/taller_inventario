{{-- Formulario de cliente. Variable opcional: $cliente (edición). --}}
@php $c = $cliente ?? null; @endphp
<div class="card" x-data="clienteForm(@js(['dni' => old('dni', $c->dni ?? ''), 'nombre' => old('nombre', $c->nombre ?? '')]))">
    <section class="form-section">
        <div>
            <h2 class="form-section-title">Identificación</h2>
            <p class="form-section-text">El DNI es obligatorio. Agrega el RUC si el cliente necesita factura.</p>
        </div>
        <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="form-label" for="dni">DNI <span class="text-red-500">*</span></label>
                <div class="flex gap-2">
                    <input id="dni" type="text" name="dni" x-model="dni" inputmode="numeric" maxlength="8" required
                           @input="dni = dni.replace(/\D/g, '').slice(0, 8)"
                           class="form-input font-mono @error('dni') form-input-error @enderror" placeholder="8 dígitos">
                    <button type="button" class="btn-secondary shrink-0" @click="consultarDni()" :disabled="dni.length !== 8 || consultando" title="Consultar en RENIEC">
                        <svg x-show="!consultando" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <svg x-show="consultando" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/><path fill="currentColor" class="opacity-75" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                        RENIEC
                    </button>
                </div>
                @error('dni')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label" for="ruc">RUC <span class="text-xs font-normal text-gray-400">(opcional)</span></label>
                <input id="ruc" type="text" name="ruc" value="{{ old('ruc', $c->ruc ?? '') }}" inputmode="numeric" maxlength="11"
                       oninput="this.value = this.value.replace(/\D/g, '').slice(0, 11)"
                       class="form-input font-mono @error('ruc') form-input-error @enderror" placeholder="10… o 20…">
                @error('ruc')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label class="form-label" for="nombre">Nombre o razón social <span class="text-red-500">*</span></label>
                <input id="nombre" type="text" name="nombre" x-model="nombre" maxlength="100" required
                       class="form-input @error('nombre') form-input-error @enderror" placeholder="Nombres y apellidos o razón social">
                @error('nombre')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>

    <section class="form-section">
        <div>
            <h2 class="form-section-title">Contacto</h2>
            <p class="form-section-text">Para coordinar entregas o enviar comprobantes.</p>
        </div>
        <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="form-label" for="telefono">Teléfono <span class="text-red-500">*</span></label>
                <input id="telefono" type="tel" name="telefono" value="{{ old('telefono', $c->telefono ?? '') }}" maxlength="15" required
                       class="form-input @error('telefono') form-input-error @enderror" placeholder="987 654 321">
                @error('telefono')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label" for="email">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email', $c->email ?? '') }}" maxlength="100"
                       class="form-input @error('email') form-input-error @enderror" placeholder="cliente@correo.com">
                @error('email')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label class="form-label" for="direccion">Dirección</label>
                <input id="direccion" type="text" name="direccion" value="{{ old('direccion', $c->direccion ?? '') }}" maxlength="150"
                       class="form-input @error('direccion') form-input-error @enderror" placeholder="Necesaria para facturas a empresas">
                @error('direccion')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    function clienteForm(inicial) {
        return {
            dni: inicial.dni || '',
            nombre: inicial.nombre || '',
            consultando: false,
            async consultarDni() {
                this.consultando = true;
                try {
                    const res = await fetch(@js(url('clientes/buscar-dni')) + '/' + this.dni, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    if (data.success) {
                        this.nombre = [data.nombres, data.apellidoPaterno, data.apellidoMaterno].filter(Boolean).join(' ').trim();
                        aviso('Datos obtenidos de RENIEC');
                    } else {
                        aviso(data.message || 'No se encontró el DNI', 'warning', 5000);
                    }
                } catch (e) {
                    aviso('No se pudo consultar RENIEC', 'error');
                } finally {
                    this.consultando = false;
                }
            },
        };
    }
</script>
@endpush
