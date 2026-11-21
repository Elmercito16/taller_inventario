<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Gestión de usuarios de la empresa (solo administradores).
 * El registro público fue eliminado: los usuarios siempre se crean
 * dentro de la empresa del administrador que los da de alta.
 */
class UsuarioController extends Controller
{
    public const ROLES = [
        'admin' => 'Administrador',
        'encargado' => 'Encargado',
        'usuario' => 'Vendedor',
    ];

    public function index()
    {
        $usuarios = Usuario::where('empresa_id', auth()->user()->empresa_id)
            ->orderBy('nombre')
            ->get();

        return view('usuarios.index', [
            'usuarios' => $usuarios,
            'roles' => self::ROLES,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|max:255|unique:usuarios,correo',
            'contraseña' => ['required', 'string', Password::min(8)],
            'rol' => ['required', Rule::in(array_keys(self::ROLES))],
        ]);

        Usuario::create([
            'nombre' => $validated['nombre'],
            'correo' => mb_strtolower($validated['correo']),
            'contraseña' => Hash::make($validated['contraseña']),
            'rol' => $validated['rol'],
            'empresa_id' => auth()->user()->empresa_id,
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, Usuario $usuario)
    {
        $this->autorizar($usuario);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => ['required', 'email', 'max:255', Rule::unique('usuarios', 'correo')->ignore($usuario->id)],
            'contraseña' => ['nullable', 'string', Password::min(8)],
            'rol' => ['required', Rule::in(array_keys(self::ROLES))],
        ]);

        // Evita que el último administrador se quite a sí mismo el rol
        if ($usuario->esAdmin() && $validated['rol'] !== 'admin' && $this->adminsDeLaEmpresa() <= 1) {
            return back()->with('error', 'Debe existir al menos un administrador en la empresa.');
        }

        $datos = [
            'nombre' => $validated['nombre'],
            'correo' => mb_strtolower($validated['correo']),
            'rol' => $validated['rol'],
        ];

        if (! empty($validated['contraseña'])) {
            $datos['contraseña'] = Hash::make($validated['contraseña']);
        }

        $usuario->update($datos);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Usuario $usuario)
    {
        $this->autorizar($usuario);

        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        if ($usuario->esAdmin() && $this->adminsDeLaEmpresa() <= 1) {
            return back()->with('error', 'Debe existir al menos un administrador en la empresa.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }

    private function autorizar(Usuario $usuario): void
    {
        abort_unless((int) $usuario->empresa_id === (int) auth()->user()->empresa_id, 404);
    }

    private function adminsDeLaEmpresa(): int
    {
        return Usuario::where('empresa_id', auth()->user()->empresa_id)->where('rol', 'admin')->count();
    }
}
