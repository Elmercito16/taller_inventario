{{-- Formulario de proveedor. Variable opcional: $proveedor (edición). --}}
@php $p = $proveedor ?? null; @endphp
<div class="card">
    <section class="form-section">
        <div>
            <h2 class="form-section-title">Empresa</h2>
            <p class="form-section-text">Razón social o nombre con el que identificas al proveedor.</p>
        </div>
        <div class="lg:col-span-2">
            <label class="form-label" for="nombre">Nombre <span class="text-red-500">*</span></label>
            <input id="nombre" type="text" name="nombre" value="{{ old('nombre', $p->nombre ?? '') }}" maxlength="255" required autofocus
                   class="form-input @error('nombre') form-input-error @enderror" placeholder="Ej.: Distribuidora García SAC">
            @error('nombre')<p class="form-error">{{ $message }}</p>@enderror
        </div>
    </section>

    <section class="form-section">
        <div>
            <h2 class="form-section-title">Contacto</h2>
            <p class="form-section-text">Persona y medios para hacer pedidos.</p>
        </div>
        <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="form-label" for="contacto">Persona de contacto</label>
                <input id="contacto" type="text" name="contacto" value="{{ old('contacto', $p->contacto ?? '') }}" maxlength="255"
                       class="form-input @error('contacto') form-input-error @enderror" placeholder="Ej.: Carlos Ruiz">
                @error('contacto')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label" for="telefono">Teléfono</label>
                <input id="telefono" type="tel" name="telefono" value="{{ old('telefono', $p->telefono ?? '') }}" maxlength="20"
                       class="form-input @error('telefono') form-input-error @enderror" placeholder="Ej.: 987 654 321">
                @error('telefono')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label class="form-label" for="direccion">Dirección</label>
                <input id="direccion" type="text" name="direccion" value="{{ old('direccion', $p->direccion ?? '') }}" maxlength="255"
                       class="form-input @error('direccion') form-input-error @enderror" placeholder="Ej.: Jr. Comercio 123, Lima">
                @error('direccion')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>
</div>
