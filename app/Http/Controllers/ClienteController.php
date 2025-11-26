<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\Cliente;
use App\Models\Venta; 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // 1. IMPORTAR RULE PARA VALIDACIÓN SEGURA

class ClienteController extends Controller
{
    // Listado de clientes
    public function index()
    {
        // El trait BelongsToTenant filtra automáticamente por la empresa actual
        $clientes = Cliente::all();
        return view('clientes.index', compact('clientes'));
    }

    // Formulario para crear cliente
    public function create()
    {
        return view('clientes.create');
    }

    // Guardar cliente
    public function store(Request $request)
    {
        // 2. CAPTURAMOS LOS DATOS VALIDADOS
        $validated = $request->validate([
            'dni' => [
                'required',
                'string',
                'max:8',
                // Validación Multi-Tenant: Único solo para esta empresa
                Rule::unique('clientes')->where(function ($query) {
                    return $query->where('empresa_id', auth()->user()->empresa_id);
                })
            ],
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        // 3. USAMOS LOS DATOS VALIDADOS
        // (Asegúrate de que 'dni' esté en el $fillable de tu modelo Cliente)
        Cliente::create($validated);

        return redirect()->route('clientes.index')->with('success', 'Cliente creado correctamente.');
    }

    // Formulario para editar cliente
    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    // Actualizar cliente
    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'dni' => [
                'required',
                'string',
                'max:8',
                // Validación Multi-Tenant al actualizar (ignorando el ID actual)
                Rule::unique('clientes')->where(function ($query) {
                    return $query->where('empresa_id', auth()->user()->empresa_id);
                })->ignore($cliente->id)
            ],
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $cliente->update($validated);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    // Eliminar cliente
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado correctamente.');
    }

    // Historial de ventas de un cliente
    public function historialCliente($id)
    {
        $cliente = Cliente::findOrFail($id);

        // Trae todas las ventas de este cliente con sus detalles y repuestos
        $ventas = Venta::with('detalles.repuesto')
                        ->where('cliente_id', $id)
                        ->get();

        return view('ventas.historial', compact('cliente', 'ventas'));
    }
    



    // AGREGAR ESTE MÉTODO
    public function search(Request $request)
    {
        $term = $request->get('q');

        if (!$term) return response()->json([]);

        // El trait UsesTenantModel filtra automáticamente por la empresa del usuario
        $clientes = Cliente::where(function($query) use ($term) {
                $query->where('nombre', 'like', "%{$term}%")
                      ->orWhere('dni', 'like', "%{$term}%");
            })
            ->limit(20) // Limitamos resultados
            ->get(['id', 'nombre', 'dni', 'direccion']);

        return response()->json($clientes);
    }

    // ✅ Método para buscar DNI con API RENIEC (ejemplo usando apis.net.pe)
    public function buscarDni($dni)
    {
        try {
            $token = "TU_TOKEN_AQUI"; // ⚠️ Reemplaza con tu token real
            $url   = "https://api.apis.net.pe/v1/dni?numero=$dni";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get($url);

            if ($response->successful()) {
                $data = $response->json();

                // Ajustar respuesta para que el JS pueda llenarlo fácil
                return response()->json([
                    'success' => true,
                    'nombres' => $data['nombres'] ?? '',
                    'apellidoPaterno' => $data['apellidoPaterno'] ?? '',
                    'apellidoMaterno' => $data['apellidoMaterno'] ?? ''
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'No se pudo obtener los datos'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function storeQuick(Request $request)
    {
        try {
            // Validación manual porque es una petición AJAX
            $validated = $request->validate([
                'dni' => [
                    'required', 'string', 'max:15',
                    // Validación única por empresa
                    Rule::unique('clientes')->where(fn ($q) => $q->where('empresa_id', auth()->user()->empresa_id))
                ],
                'nombre' => 'required|string|max:255',
                'direccion' => 'nullable|string|max:255',
                'telefono' => 'nullable|string|max:20',
            ]);

            // Crear cliente (UsesTenantModel asigna la empresa)
            $cliente = Cliente::create($validated);

            return response()->json([
                'success' => true,
                'cliente' => $cliente, // Devolvemos el cliente para seleccionarlo automáticamente
                'message' => 'Cliente registrado correctamente'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Error de validación (ej. DNI duplicado)
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Error general
            return response()->json([
                'success' => false,
                'message' => 'Error del servidor: ' . $e->getMessage()
            ], 500);
        }
    }


    
}