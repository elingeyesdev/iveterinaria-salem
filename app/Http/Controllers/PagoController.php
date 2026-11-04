<?php

namespace App\Http\Controllers;

use App\Http\Requests\PagoFiltroRequest;
use App\Http\Requests\PagoRequest;
use App\Models\Consulta;
use App\Models\Pago;
use App\Services\PagoService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Flujo financiero de la consulta, independiente de la edición clínica.
 *
 * Toda la lógica de negocio (cálculo de saldo, bloqueo por concurrencia,
 * validación de montos y anulación) vive en PagoService; este controlador
 * solo autoriza, delega y construye la respuesta para el usuario.
 */
class PagoController extends Controller
{
    public function __construct(private readonly PagoService $pagoService) {}

    public function index(PagoFiltroRequest $request): View
    {
        $filtros = $request->validated();
        $estado = $filtros['estado'] ?? null;

        $query = Pago::query()
            ->when($estado !== null, function (Builder $query) use ($estado): void {
                $query->where('estado', $estado);
            })
            ->orderByDesc('id');

        $mostrarTodos = ($filtros['perPage'] ?? 'all') === 'all';
        $perPage = $mostrarTodos ? max(1, (clone $query)->count()) : (int) $filtros['perPage'];
        $pagos = $query->paginate($perPage, ['*'], 'page', $mostrarTodos ? 1 : null)
            ->appends($request->safe()->only(['estado', 'perPage']));

        if ($request->ajax()) {
            return view('pagos._table', compact('pagos', 'filtros'));
        }

        return view('pagos.index', compact('pagos', 'filtros'));
    }

    public function edit() {}

    public function create(PagoFiltroRequest $request): View|RedirectResponse
    {
        $consultaId = $request->validated('consulta_id');
        $consulta = $consultaId
            ? Consulta::with(['servicios', 'pagos', 'reserva.mascota.cliente'])->findOrFail($consultaId)
            : null;

        if ($consulta) {
            $pago = $consulta->pagos->where('estado', Pago::PENDIENTE)->sortByDesc('id')->first()
                ?? $consulta->pagos->sortByDesc('id')->first();

            if ($pago) {
                return redirect()->route('pagos.show', $pago);
            }
        }

        $consultas = $consulta ? null : Consulta::with(['servicios', 'pagos', 'reserva.mascota.cliente'])
            ->orderByDesc('fecha')->orderByDesc('id')->paginate(20);

        return view('pagos.create', compact('consulta', 'consultas'));
    }

    /** Cobro completo exclusivamente en efectivo. */
    public function store(PagoRequest $request, Consulta $consulta): RedirectResponse
    {
        $this->authorize('create', [Pago::class, $consulta]);
        try {
            $this->pagoService->registrar($consulta, $request->validated());
        } catch (\DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'No se pudo completar el cobro.');
        }

        return back()->with('success', 'Pago en efectivo registrado correctamente. Cobro completo.');
    }

    public function show(Pago $pago): View
    {
        $this->authorize('view', $pago);

        $pago->load([
            'consulta.reserva.mascota.cliente',
            'consulta.servicios',
            'consulta.pagos',
        ]);

        return view('pagos.show', compact('pago'));
    }

    public function comprobante(Pago $pago): View
    {
        $this->authorize('view', $pago);
        abort_unless($pago->estado === Pago::PAGADO, 409, 'Solo se emiten comprobantes de pagos vigentes confirmados.');
        $pago->load(['consulta.reserva.mascota.cliente', 'consulta.servicios']);

        return view('pagos.comprobante', compact('pago'));
    }

    /**
     * Anula un pago sin eliminarlo físicamente.
     * El registro queda disponible para auditoría y deja de contar como pagado.
     */
    public function anular(Consulta $consulta, Pago $pago): RedirectResponse
    {
        $this->authorize('anular', $pago);

        if ((int) $pago->consulta_id !== (int) $consulta->id) {
            abort(404);
        }

        try {
            $this->pagoService->anular($consulta, $pago);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'No se pudo anular el pago. Intente nuevamente.');
        }

        $consulta->unsetRelation('pagos')->unsetRelation('servicios');

        return back()->with('warning', sprintf(
            'Anulación registrada. Los movimientos se conservan en el historial. Saldo pendiente: Bs %s.',
            $this->formatear($this->pagoService->calcularSaldo($consulta))
        ));
    }

    private function formatear(float $monto): string
    {
        return number_format($monto, 2, '.', ',');
    }
}
