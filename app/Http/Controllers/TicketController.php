<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Tickets rápidos: ventas escritas a mano, sin control de inventario.
 * Generan boleta PDF y ticket con el logo y los datos de la empresa.
 */
class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::query()->orderByDesc('fecha')->orderByDesc('id');

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($w) use ($q) {
                $w->whereLike('cliente_nombre', "%{$q}%")->orWhereLike('cliente_documento', "%{$q}%");
                $numero = (int) preg_replace('/\D/', '', $q);
                if ($numero > 0) {
                    $w->orWhere('numero', $numero);
                }
            });
        }
        if ($request->filled('desde')) {
            $query->where('fecha', '>=', Carbon::parse($request->desde)->startOfDay());
        }
        if ($request->filled('hasta')) {
            $query->where('fecha', '<=', Carbon::parse($request->hasta)->endOfDay());
        }

        $resumen = [
            'hoy' => (float) Ticket::where('estado', 'emitido')->whereDate('fecha', today())->sum('total'),
            'cantidad_hoy' => Ticket::where('estado', 'emitido')->whereDate('fecha', today())->count(),
            'mes' => (float) Ticket::where('estado', 'emitido')->where('fecha', '>=', now()->startOfMonth())->sum('total'),
        ];

        $tickets = $query->paginate(20)->withQueryString();

        return view('tickets.index', compact('tickets', 'resumen'));
    }

    public function create()
    {
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_documento' => ['nullable', 'regex:/^(\d{8}|\d{11})$/'],
            'cliente_nombre' => 'nullable|string|max:150',
            'items' => 'required|array|min:1|max:100',
            'items.*.descripcion' => 'required|string|max:200',
            'items.*.cantidad' => 'required|numeric|gt:0|max:100000',
            'items.*.precio' => 'required|numeric|min:0|max:10000000',
        ], [
            'cliente_documento.regex' => 'El documento debe ser un DNI (8 dígitos) o RUC (11 dígitos).',
            'items.required' => 'Agrega al menos un ítem.',
            'items.*.descripcion.required' => 'Cada ítem necesita una descripción.',
            'items.*.cantidad.gt' => 'La cantidad debe ser mayor a 0.',
        ]);

        // El importe y el total SIEMPRE se calculan en el servidor
        $items = collect($validated['items'])->map(function ($item) {
            $cantidad = round((float) $item['cantidad'], 3);
            $precio = round((float) $item['precio'], 2);

            return [
                'descripcion' => trim($item['descripcion']),
                'cantidad' => $cantidad,
                'precio' => $precio,
                'importe' => round($cantidad * $precio, 2),
            ];
        })->values()->all();

        $empresaId = auth()->user()->empresa_id;

        $ticket = DB::transaction(fn () => Ticket::create([
            'numero' => Ticket::siguienteNumero($empresaId),
            'cliente_documento' => $validated['cliente_documento'] ?? null,
            'cliente_nombre' => isset($validated['cliente_nombre']) ? trim($validated['cliente_nombre']) : null,
            'items' => $items,
            'total' => round(array_sum(array_column($items, 'importe')), 2),
            'estado' => 'emitido',
            'usuario_id' => auth()->id(),
            'fecha' => now(),
        ]));

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Ticket {$ticket->numero_formateado} registrado.");
    }

    public function show(Ticket $ticket)
    {
        return view('tickets.show', ['ticket' => $ticket, 'empresa' => auth()->user()->empresa]);
    }

    /** Ticket de 80 mm para imprimir desde el navegador. */
    public function imprimir(Ticket $ticket)
    {
        return view('tickets.imprimir', ['ticket' => $ticket, 'empresa' => auth()->user()->empresa]);
    }

    /** Boleta en PDF (80 mm). */
    public function pdf(Request $request, Ticket $ticket)
    {
        $pdf = Pdf::loadView('tickets.boleta', ['ticket' => $ticket, 'empresa' => auth()->user()->empresa])
            ->setPaper([0, 0, 226.77, 841.89], 'portrait');

        $archivo = 'Boleta_' . $ticket->numero_formateado . '.pdf';

        // ?descargar=1 baja el archivo; sin él, se abre en el navegador.
        return $request->boolean('descargar') ? $pdf->download($archivo) : $pdf->stream($archivo);
    }

    public function anular(Ticket $ticket)
    {
        if ($ticket->estaAnulado()) {
            return back()->with('warning', 'El ticket ya estaba anulado.');
        }

        $ticket->update(['estado' => 'anulado']);

        return back()->with('success', "Ticket {$ticket->numero_formateado} anulado.");
    }

    /**
     * Busca el nombre por DNI/RUC:
     * 1) clientes registrados, 2) tickets anteriores (funciona sin internet),
     * 3) RENIEC / SUNAT vía apis.net.pe (si hay token configurado).
     */
    public function buscarCliente(Request $request)
    {
        $doc = preg_replace('/\D/', '', (string) $request->query('doc'));

        if (! in_array(strlen($doc), [8, 11], true)) {
            return response()->json(['encontrado' => false, 'mensaje' => 'Ingresa un DNI (8 dígitos) o RUC (11 dígitos).'], 422);
        }

        $cliente = Cliente::where('dni', $doc)->orWhere('ruc', $doc)->first();
        if ($cliente) {
            return response()->json(['encontrado' => true, 'nombre' => $cliente->nombre, 'origen' => 'clientes']);
        }

        $anterior = Ticket::where('cliente_documento', $doc)->whereNotNull('cliente_nombre')->latest('id')->first();
        if ($anterior) {
            return response()->json(['encontrado' => true, 'nombre' => $anterior->cliente_nombre, 'origen' => 'tickets']);
        }

        $token = config('services.apis_net_pe.token');
        if (! $token) {
            return response()->json(['encontrado' => false, 'mensaje' => 'No está registrado. Escribe el nombre.']);
        }

        try {
            $esRuc = strlen($doc) === 11;
            $respuesta = Http::withToken($token)->timeout(6)
                ->get('https://api.apis.net.pe/v1/' . ($esRuc ? 'ruc' : 'dni'), ['numero' => $doc]);

            if ($respuesta->successful()) {
                $d = $respuesta->json();
                $nombre = $esRuc
                    ? ($d['nombre'] ?? $d['razonSocial'] ?? null)
                    : trim(($d['nombres'] ?? '') . ' ' . ($d['apellidoPaterno'] ?? '') . ' ' . ($d['apellidoMaterno'] ?? ''));

                if ($nombre) {
                    return response()->json(['encontrado' => true, 'nombre' => $nombre, 'origen' => $esRuc ? 'sunat' : 'reniec']);
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json(['encontrado' => false, 'mensaje' => 'No se encontró el documento. Escribe el nombre.']);
    }
}
