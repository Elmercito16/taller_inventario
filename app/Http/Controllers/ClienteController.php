<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\Cliente;
use App\Models\Venta; // ✅ agregado porque lo usas en historialCliente
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::all();
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'dni' => 'required|string|max:8|unique:clientes,dni',
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        Cliente::create($request->all());

        return redirect()->route('clientes.index')->with('success', 'Cliente creado correctamente.');
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'dni' => 'required|string|max:8|unique:clientes,dni,' . $cliente->id,
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $cliente->update($request->all());

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado correctamente.');
    }

    public function historialCliente($id)
    {
        $cliente = Cliente::findOrFail($id);

        // Trae todas las ventas de este cliente con sus detalles y repuestos
        $ventas = Venta::with('detalles.repuesto')
                        ->where('cliente_id', $id)
                        ->get();

        return view('ventas.historial', compact('cliente', 'ventas'));
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
}
