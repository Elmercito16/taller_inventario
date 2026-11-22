@extends('layouts.app')

@section('title', 'Usuarios')
@section('page-title', 'Usuarios')
@section('page-description', 'Personas con acceso al sistema de tu empresa')

@section('content')
<div x-data="usuariosPage()" class="space-y-6">

    @if($errors->any())
        <div class="alert alert-danger">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <ul class="text-sm list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <p class="text-sm text-gray-500">{{ $usuarios->count() }} usuario(s) registrados</p>
        <button type="button" @click="abrirNuevo()" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo usuario
        </button>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th class="hidden md:table-cell">Creado</th>
                        <th class="num">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $usuario)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-semibold text-sm">
                                        {{ mb_strtoupper(mb_substr($usuario->nombre, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 truncate">
                                            {{ $usuario->nombre }}
                                            @if($usuario->id === auth()->id())
                                                <span class="badge badge-gray ml-1">Tú</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500 truncate">{{ $usuario->correo }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $usuario->rol === 'admin' ? 'badge-primary' : ($usuario->rol === 'encargado' ? 'badge-info' : 'badge-gray') }}">
                                    {{ $roles[$usuario->rol] ?? ucfirst($usuario->rol) }}
                                </span>
                            </td>
                            <td class="hidden md:table-cell text-gray-500">{{ $usuario->created_at?->format('d/m/Y') }}</td>
                            <td class="text-right whitespace-nowrap">
                                <button type="button" class="btn-icon"
                                        @click="abrirEditar(@js(['id' => $usuario->id, 'nombre' => $usuario->nombre, 'correo' => $usuario->correo, 'rol' => $usuario->rol]))"
                                        title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                @if($usuario->id !== auth()->id())
                                    <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="inline"
                                          onsubmit="return confirm('¿Eliminar al usuario {{ addslashes($usuario->nombre) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon hover:!text-red-600 hover:!bg-red-50" title="Eliminar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal crear / editar --}}
    <div x-show="modal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="modal = false">
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="modal = false"></div>
        <div class="relative w-full max-w-md card" x-transition>
            <h3 class="text-lg font-semibold text-gray-900 mb-4" x-text="editando ? 'Editar usuario' : 'Nuevo usuario'"></h3>

            <form :action="editando ? '{{ url('usuarios') }}/' + form.id : '{{ route('usuarios.store') }}'" method="POST" class="space-y-4">
                @csrf
                <template x-if="editando"><input type="hidden" name="_method" value="PUT"></template>

                <div>
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" x-model="form.nombre" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Correo</label>
                    <input type="email" name="correo" x-model="form.correo" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="contraseña" class="form-input" :required="!editando" minlength="8" autocomplete="new-password"
                           :placeholder="editando ? 'Dejar vacío para no cambiar' : 'Mínimo 8 caracteres'">
                </div>
                <div>
                    <label class="form-label">Rol</label>
                    <select name="rol" x-model="form.rol" class="form-input">
                        @foreach($roles as $valor => $nombre)
                            <option value="{{ $valor }}">{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" class="btn-secondary" @click="modal = false">Cancelar</button>
                    <button type="submit" class="btn-primary" x-text="editando ? 'Guardar cambios' : 'Crear usuario'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function usuariosPage() {
        return {
            modal: false,
            editando: false,
            form: { id: null, nombre: '', correo: '', rol: 'usuario' },
            abrirNuevo() {
                this.editando = false;
                this.form = { id: null, nombre: '', correo: '', rol: 'usuario' };
                this.modal = true;
            },
            abrirEditar(usuario) {
                this.editando = true;
                this.form = { ...usuario };
                this.modal = true;
            },
        };
    }
</script>
@endpush
