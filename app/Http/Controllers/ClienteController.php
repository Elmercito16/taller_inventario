<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::withCount('ventas')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->reglas());

        Cliente::create($validated);

        return redirect()->route('clientes.index')->with('success', 'Cliente creado correctamente.');
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate($this->reglas($cliente->id));

        $cliente->update($validated);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        if ($cliente->ventas()->exists()) {
            return redirect()->route('clientes.index')
                ->with('error', 'No se puede eliminar: el cliente tiene ventas registradas.');
        }

        $cliente->delete();

        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado correctamente.');
    }

    /**
     * Buscar clientes por nombre, DNI o RUC (AJAX).
     */
    public function search(Request $request)
    {
        $query = trim((string) $request->input('q', ''));

        $clientes = Cliente::query()
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($q) use ($query) {
                    $q->where('nombre', 'LIKE', "%{$query}%")
                      ->orWhere('dni', 'LIKE', "%{$query}%")
                      ->orWhere('ruc', 'LIKE', "%{$query}%");
                });
            })
            ->orderBy('nombre', 'asc')
            ->limit(50)
            ->get(['id', 'nombre', 'dni', 'ruc', 'telefono', 'email']);

        return response()->json($clientes);
    }

    /**
     * Consulta de DNI (RENIEC) vía apis.net.pe.
     * Configura APIS_NET_PE_TOKEN en el .env.
     */
    public function buscarDni(string $dni)
    {
        $token = config('services.apis_net_pe.token');

        if (empty($token)) {
            return response()->json([
                'success' => false,
                'message' => 'La consulta de DNI no está configurada (falta APIS_NET_PE_TOKEN).',
            ], 503);
        }

        try {
            $response = Http::withToken($token)
                ->timeout(8)
                ->get('https://api.apis.net.pe/v1/dni', ['numero' => $dni]);

            if (! $response->successful()) {
                return response()->json(['success' => false, 'message' => 'No se pudo obtener los datos del DNI.'], 400);
            }

            $data = $response->json();

            return response()->json([
                'success' => true,
                'nombres' => $data['nombres'] ?? '',
                'apellidoPaterno' => $data['apellidoPaterno'] ?? '',
                'apellidoMaterno' => $data['apellidoMaterno'] ?? '',
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['success' => false, 'message' => 'Servicio de consulta no disponible.'], 500);
        }
    }

    /**
     * Crear cliente rápido desde el punto de venta (AJAX).
     */
    public function storeQuick(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;

        $validated = $request->validate([
            'dni' => [
                'required', 'digits:8',
                Rule::unique('clientes', 'dni')->where(fn ($q) => $q->where('empresa_id', $empresaId)),
            ],
            'nombre' => 'required|string|max:100',
            'telefono' => 'required|string|min:7|max:15',
            'direccion' => 'nullable|string|max:150',
        ], [
            'dni.required' => 'El DNI es obligatorio',
            'dni.digits' => 'El DNI debe tener 8 dígitos',
            'dni.unique' => 'Este DNI ya está registrado',
            'nombre.required' => 'El nombre es obligatorio',
            'telefono.required' => 'El teléfono es obligatorio',
            'telefono.min' => 'El teléfono debe tener al menos 7 dígitos',
        ]);

        try {
            $cliente = Cliente::create($validated);

            return response()->json([
                'success' => true,
                'cliente' => $cliente->only(['id', 'nombre', 'dni', 'telefono']),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el cliente',
                'errors' => ['general' => ['Ocurrió un error al guardar']],
            ], 500);
        }
    }

    /**
     * DNI y RUC únicos SOLO dentro de la empresa del usuario.
     */
    private function reglas(?int $ignorarId = null): array
    {
        $empresaId = auth()->user()->empresa_id;
        $porEmpresa = fn ($q) => $q->where('empresa_id', $empresaId);

        return [
            'dni' => [
                'required', 'digits:8',
                Rule::unique('clientes', 'dni')->where($porEmpresa)->ignore($ignorarId),
            ],
            'ruc' => [
                'nullable', 'digits:11', 'regex:/^(10|15|17|20)\d{9}$/',
                Rule::unique('clientes', 'ruc')->where($porEmpresa)->ignore($ignorarId),
            ],
            'nombre' => 'required|string|max:100',
            'telefono' => 'required|string|max:15',
            'direccion' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:100',
        ];
    }
}
