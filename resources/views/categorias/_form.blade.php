{{-- Formulario de categoría. Variable opcional: $categoria (edición). --}}
<div class="card">
    <section class="form-section">
        <div>
            <h2 class="form-section-title">Datos de la categoría</h2>
            <p class="form-section-text">El nombre debe ser único dentro de tu negocio.</p>
        </div>
        <div class="lg:col-span-2 space-y-4">
            <div>
                <label class="form-label" for="nombre">Nombre <span class="text-red-500">*</span></label>
                <input id="nombre" type="text" name="nombre" value="{{ old('nombre', $categoria->nombre ?? '') }}" maxlength="255" required autofocus
                       class="form-input @error('nombre') form-input-error @enderror" placeholder="Ej.: Bebidas">
                @error('nombre')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div x-data="{ texto: @js(old('descripcion', $categoria->descripcion ?? '')) }">
                <label class="form-label" for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="3" maxlength="500" x-model="texto"
                          class="form-input @error('descripcion') form-input-error @enderror" placeholder="Opcional"></textarea>
                <p class="form-help text-right"><span x-text="texto.length"></span>/500</p>
                @error('descripcion')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>
</div>
