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
    /**
     * Buscar clientes por nombre o DNI (para AJAX)
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        
        $clientes = Cliente::query()
            ->when($query, function ($q) use ($query) {
                $q->where('nombre', 'LIKE', "%{$query}%")
                  ->orWhere('dni', 'LIKE', "%{$query}%");
            })
            ->orderBy('nombre', 'asc')
            ->limit(50)
            ->get(['id', 'nombre', 'dni', 'telefono', 'email']);
        
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
        $validated = $request->validate([
            'dni' => 'required|string|size:8|unique:clientes,dni',
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|min:7|max:9',
            'direccion' => 'nullable|string|max:500',
        ], [
            'dni.required' => 'El DNI es obligatorio',
            'dni.size' => 'El DNI debe tener 8 dígitos',
            'dni.unique' => 'Este DNI ya está registrado',
            'nombre.required' => 'El nombre es obligatorio',
            'telefono.required' => 'El teléfono es obligatorio',
            'telefono.min' => 'El teléfono debe tener al menos 7 dígitos',
        ]);

        try {
            $cliente = Cliente::create($validated);
            
            return response()->json([
                'success' => true,
                'cliente' => [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'dni' => $cliente->dni,
                    'telefono' => $cliente->telefono,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el cliente',
                'errors' => ['general' => ['Ocurrió un error al guardar']]
            ], 500);
        }
    }


    
}