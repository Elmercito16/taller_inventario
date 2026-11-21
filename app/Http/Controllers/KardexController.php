<?php

namespace App\Http\Controllers;

use App\Models\MovimientoInventario;
use App\Models\Repuesto;
use App\Services\InventarioService;
use App\Support\Catalogos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KardexController extends Controller
{
    /**
     * Todos los movimientos de inventario, con filtros.
     */
    public function index(Request $request)
    {
        $query = $this->filtrar(MovimientoInventario::with(['repuesto', 'usuario']), $request)
            ->orderByDesc('fecha')
            ->orderByDesc('id');

        if ($request->get('exportar') === 'csv') {
            return $this->csv((clone $query)->get(), 'kardex-movimientos-' . now()->format('Ymd-His') . '.csv');
        }

        $resumen = (clone $query)->reorder()
            ->selectRaw("COALESCE(SUM(CASE WHEN tipo = 'entrada' THEN 1 ELSE 0 END), 0) as entradas")
            ->selectRaw("COALESCE(SUM(CASE WHEN tipo = 'salida' THEN 1 ELSE 0 END), 0) as salidas")
            ->setEagerLoads([])
            ->first();

        $movimientos = $query->paginate(25)->withQueryString();
        $productos = Repuesto::productos()->orderBy('nombre')->get(['id', 'nombre', 'codigo']);

        return view('kardex.index', [
            'movimientos' => $movimientos,
            'productos' => $productos,
            'motivos' => MovimientoInventario::MOTIVOS,
            'resumen' => $resumen,
        ]);
    }

    /**
     * Kardex de un producto: movimientos en orden cronológico con saldo.
     */
    public function show(Request $request, Repuesto $repuesto)
    {
        abort_if($repuesto->esServicio(), 404, 'Los servicios no tienen kardex.');

        $query = $this->filtrar($repuesto->movimientos()->with('usuario')->getQuery(), $request)
            ->orderBy('fecha')
            ->orderBy('id');

        if ($request->get('exportar') === 'csv') {
            return $this->csv((clone $query)->with('repuesto')->get(), 'kardex-' . $repuesto->codigo . '-' . now()->format('Ymd') . '.csv');
        }

        $totales = (clone $query)->reorder()
            ->selectRaw("COALESCE(SUM(CASE WHEN tipo = 'entrada' THEN cantidad ELSE 0 END), 0) as entradas")
            ->selectRaw("COALESCE(SUM(CASE WHEN tipo = 'salida' THEN cantidad ELSE 0 END), 0) as salidas")
            ->first();

        $movimientos = $query->paginate(30)->withQueryString();

        return view('kardex.show', [
            'repuesto' => $repuesto->load('categoria'),
            'movimientos' => $movimientos,
            'totales' => $totales,
            'motivos' => MovimientoInventario::MOTIVOS,
        ]);
    }

    /**
     * Ajuste de inventario: entrada, salida (merma, pérdida...) o conteo físico.
     */
    public function ajustar(Request $request, Repuesto $repuesto, InventarioService $inventario)
    {
        abort_if($repuesto->esServicio(), 404);

        $request->validate([
            'tipo_ajuste' => 'required|in:entrada,salida,conteo',
            'cantidad' => [
                'required', 'numeric', $request->tipo_ajuste === 'conteo' ? 'min:0' : 'gt:0', 'max:1000000',
                function ($atributo, $valor, $fallar) use ($repuesto) {
                    if (! $repuesto->permiteDecimales() && floor((float) $valor) != (float) $valor) {
                        $fallar('Este producto solo admite cantidades enteras.');
                    }
                },
            ],
            'observacion' => 'required|string|min:3|max:255',
        ], [
            'observacion.required' => 'Indica el motivo del ajuste (merma, vencimiento, conteo, etc.).',
        ]);

        $cantidad = (float) $request->cantidad;
        $opciones = ['descripcion' => $request->observacion];

        try {
            $movimiento = match ($request->tipo_ajuste) {
                'entrada' => $inventario->entrada($repuesto, $cantidad, 'ajuste_entrada', $opciones),
                'salida' => $inventario->salida($repuesto, $cantidad, 'ajuste_salida', $opciones),
                'conteo' => $inventario->fijarStock($repuesto, $cantidad, 'conteo_fisico', $opciones),
            };
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        if (! $movimiento) {
            return back()->with('warning', 'El conteo coincide con el stock actual; no se registró ningún ajuste.');
        }

        return back()->with('success', 'Ajuste registrado. Stock actual: ' . Catalogos::cantidad($movimiento->stock_nuevo) . ' ' . $repuesto->unidad_abreviatura . '.');
    }

    private function filtrar($query, Request $request)
    {
        if ($request->filled('repuesto_id')) {
            $query->where('repuesto_id', $request->repuesto_id);
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('motivo')) {
            $query->where('motivo', $request->motivo);
        }
        if ($request->filled('desde')) {
            $query->where('fecha', '>=', Carbon::parse($request->desde)->startOfDay());
        }
        if ($request->filled('hasta')) {
            $query->where('fecha', '<=', Carbon::parse($request->hasta)->endOfDay());
        }

        return $query;
    }

    /**
     * Exporta movimientos a CSV (se abre en Excel; separador ";" y BOM UTF-8).
     */
    private function csv($movimientos, string $nombre): StreamedResponse
    {
        return response()->streamDownload(function () use ($movimientos) {
            $salida = fopen('php://output', 'w');
            fwrite($salida, "\xEF\xBB\xBF");
            fputcsv($salida, ['Fecha', 'Código', 'Producto', 'Tipo', 'Motivo', 'Entrada', 'Salida', 'Saldo anterior', 'Saldo nuevo', 'Costo unit.', 'Referencia', 'Detalle', 'Usuario'], ';');

            foreach ($movimientos as $m) {
                fputcsv($salida, [
                    $m->fecha?->format('d/m/Y H:i'),
                    $m->repuesto->codigo ?? '',
                    $m->repuesto->nombre ?? 'Producto eliminado',
                    ucfirst($m->tipo),
                    $m->motivo_nombre,
                    $m->esEntrada() ? Catalogos::cantidad($m->cantidad) : '',
                    $m->esEntrada() ? '' : Catalogos::cantidad($m->cantidad),
                    Catalogos::cantidad($m->stock_anterior),
                    Catalogos::cantidad($m->stock_nuevo),
                    $m->costo_unitario !== null ? number_format($m->costo_unitario, 2, '.', '') : '',
                    $m->referencia_texto ?? '',
                    $m->descripcion ?? '',
                    $m->usuario->nombre ?? '',
                ], ';');
            }

            fclose($salida);
        }, $nombre, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
