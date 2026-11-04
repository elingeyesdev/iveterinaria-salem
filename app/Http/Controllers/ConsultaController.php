<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsultumRequest;
use App\Models\Cliente;
use App\Models\Consulta;
use App\Models\Mascotum;
use App\Models\Reserva;
use App\Models\Servicio;
use App\Models\Veterinario;
use App\Services\ConsultaService;
use App\Services\ReservaService;
use App\Support\ConsultaTipo;
use App\Support\ReservaEstado;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ConsultaController extends Controller
{
    public function __construct(
        private readonly ConsultaService $consultaService,
        private readonly ReservaService $reservaService
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', Consulta::class);

        $consultas = Consulta::with([
            'reserva.mascota.cliente',
            'reserva.mascota.especie',
            'veterinario',
            'servicios',
            'pagos',
        ])
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(20);

        $reservasPendientes = Reserva::with('mascota.cliente')
            ->where('estado', ReservaEstado::PENDIENTE)
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->get();

        $veterinarios = Veterinario::where('estado', 1)->get();

        return view('consultum.index', compact('consultas', 'reservasPendientes', 'veterinarios'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        $this->authorize('create', Consulta::class);
        $reserva_id = $request->get('reserva_id');
        abort_if($reserva_id !== null && (! is_scalar($reserva_id) || ! ctype_digit((string) $reserva_id) || (int) $reserva_id < 1), 404);

        $reservaSeleccionada = $reserva_id
            ? Reserva::with(['mascota.cliente', 'consulta'])->findOrFail($reserva_id)
            : null;

        if ($reservaSeleccionada?->consulta) {
            return redirect()->route('consulta.show', $reservaSeleccionada->consulta);
        }

        $reservas = Reserva::with('mascota.cliente')
            ->whereIn('estado', [ReservaEstado::PENDIENTE, strtolower(ReservaEstado::PENDIENTE)])
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get();

        $veterinarios = Veterinario::where('estado', 1)->get();
        $servicios = Servicio::where('estado', 1)->get();
        $clientes = Cliente::where('estado', 1)->with(['mascotas' => function ($q) {
            $q->where('estado', 1)->orderBy('nombre');
        }])->orderBy('nombre')->get();
        $mascotaDirecta = $request->filled('mascota_id') ? Mascotum::with('cliente')->find($request->integer('mascota_id')) : null;
        if ($mascotaDirecta && (int) $request->query('cliente_id') !== (int) $mascotaDirecta->cliente_id) {
            abort(404);
        }

        return view('consultum.create', compact(
            'reservas',
            'veterinarios',
            'reserva_id',
            'reservaSeleccionada',
            'servicios',
            'clientes',
            'mascotaDirecta'
        ));
    }

    /**
     * Crea una reserva interna para una atención que llegó sin cita.
     * No modifica las migraciones: la consulta sigue vinculada a reserva.
     */
    public function continuarAtencionDirecta(Request $request): RedirectResponse
    {
        $this->authorize('create', Consulta::class);

        $datos = $request->validate([
            'cliente_id' => ['required', 'integer', 'exists:cliente,id'],
            'mascota_id' => ['required', 'integer', 'exists:mascota,id'],
        ]);

        $clienteActivo = Cliente::whereKey($datos['cliente_id'])
            ->where('estado', 1)
            ->exists();

        $mascota = Mascotum::whereKey($datos['mascota_id'])
            ->where('cliente_id', $datos['cliente_id'])
            ->where('estado', 1)
            ->first();

        if (! $clienteActivo || ! $mascota) {
            return back()->with('error', 'La mascota no pertenece al cliente seleccionado o está inactiva.');
        }

        // Busca el primer turno válido y libre desde hoy hasta los próximos 14 días.
        $turnoElegido = null;
        for ($offset = 0; $offset <= 14 && ! $turnoElegido; $offset++) {
            $fecha = now()->addDays($offset)->toDateString();
            foreach ($this->reservaService->obtenerTurnos($fecha) as $turno) {
                $ocupados = $this->reservaService->obtenerHorasOcupadas($fecha);
                foreach ($this->reservaService->generarSlots($turno['inicio'], $turno['fin'], $ocupados, $fecha) as $slot) {
                    if (! $slot['ocupado'] && ! $slot['pasado']) {
                        $turnoElegido = ['fecha' => $fecha, 'hora_inicio' => $slot['hora']];
                        break 3;
                    }
                }
            }
        }

        if (! $turnoElegido) {
            return back()->with('error', 'No se encontró un horario disponible en los próximos 14 días.');
        }

        try {
            $resultado = $this->reservaService->crearReserva([
                'cliente_id' => (int) $datos['cliente_id'],
                'mascota_id' => (int) $mascota->id,
                'fecha' => $turnoElegido['fecha'],
                'hora_inicio' => $turnoElegido['hora_inicio'],
            ], false);

            if (! ($resultado['success'] ?? false)) {
                return back()->withInput()->with('error', $resultado['message'] ?? 'No se pudo crear la reserva interna.');
            }

            $reserva = $resultado['reserva'];

            return redirect()->route('consulta.create', [
                'reserva_id' => $reserva->id,
                'tipo_interno' => 'directa',
            ])->with('success', 'Cliente y mascota registrados. Se creó una reserva interna para continuar con la consulta.');
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'No se pudo crear la reserva interna. Intente nuevamente.');
        }
    }

    public function store(ConsultumRequest $request): RedirectResponse
    {
        $this->authorize('create', Consulta::class);
        $validated = $request->validated();
        // Este formulario solo atiende el flujo "con reserva": reserva_id
        // es obligatorio (ver ConsultumRequest), así que el tipo siempre es 'Reserva'.
        $validated['tipo'] = ConsultaTipo::RESERVA;

        try {
            $consulta = $this->consultaService->crear($validated);

            // La atención clínica y los pagos son flujos separados.
            // Después de guardar la consulta mostramos su resumen, donde
            // recepción puede registrar uno o varios pagos sin modificar
            // el expediente médico.
            return Redirect::route('consulta.show', $consulta)
                ->with('success', 'Consulta médica registrada exitosamente.');
        } catch (\DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            report($e);

            return back()->withInput()
                ->with('error', 'No se pudo guardar la consulta. Intente nuevamente.');
        }
    }

    public function show(Consulta $consulta): View
    {
        $this->authorize('view', $consulta);
        $consulta->load(['reserva.mascota.cliente', 'reserva.mascota.especie', 'reserva.mascota.raza', 'veterinario', 'servicios', 'pagos']);

        return view('consultum.show', compact('consulta'));
    }

    public function edit(Consulta $consulta): View
    {
        $this->authorize('update', $consulta);
        $consulta->load(['reserva.mascota.cliente', 'veterinario', 'servicios', 'pagos']);

        $reservas = Reserva::with('mascota.cliente')
            ->where(function ($query) use ($consulta) {
                $query->whereIn('estado', [ReservaEstado::PENDIENTE, strtolower(ReservaEstado::PENDIENTE)]);
                if ($consulta->reserva_id) {
                    $query->orWhere('id', $consulta->reserva_id);
                }
            })
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->get();

        $veterinarios = Veterinario::where('estado', 1)->get();
        $servicios = Servicio::where('estado', 1)->get();

        $reservaSeleccionada = $consulta->reserva;
        $reserva_id = $consulta->reserva_id;

        return view('consultum.edit', compact('consulta', 'reservas', 'veterinarios', 'servicios', 'reservaSeleccionada', 'reserva_id'));
    }

    public function update(ConsultumRequest $request, Consulta $consulta): RedirectResponse
    {
        $this->authorize('update', $consulta);

        $validated = $request->validated();
        // Conservamos el tipo original. Una consulta creada como Emergencia
        // debe seguir siendo Emergencia al editarla.
        $validated['tipo'] = $consulta->tipo;

        // Se captura ANTES de guardar: después de $consultaService->actualizar()
        // $consulta->reserva_id ya es el nuevo valor.
        $reservaAnteriorId = $consulta->reserva_id;

        try {
            $consulta = $this->consultaService->actualizar($consulta, $validated);

            if ($reservaAnteriorId && $reservaAnteriorId != $consulta->reserva_id) {
                Reserva::find($reservaAnteriorId)?->liberarSiSinConsultasActivas($consulta->id);
            }

            return Redirect::route('consulta.show', $consulta)
                ->with('success', 'Consulta actualizada correctamente.');
        } catch (\DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            report($e);

            return back()->withInput()
                ->with('error', 'No se pudo actualizar la consulta. Intente nuevamente.');
        }
    }

    public function destroy(Consulta $consulta): RedirectResponse
    {
        $this->authorize('delete', $consulta);
        try {
            DB::transaction(function () use ($consulta): void {
                $consulta = Consulta::query()->lockForUpdate()->findOrFail($consulta->id);
                $this->authorize('delete', $consulta);
                if ($consulta->pagos()->exists()) {
                    throw new \DomainException('No se puede eliminar una consulta con movimientos de pago registrados, aunque estén anulados, porque forman parte del historial financiero.');
                }
                $reserva = $consulta->reserva;
                // consulta_servicio usa RESTRICT, por lo que primero se
                // desprenden las líneas de servicios y luego se elimina la
                // consulta, sin tocar la estructura de la base de datos.
                $consulta->servicios()->detach();
                $consulta->delete();
                $reserva?->liberarSiSinConsultasActivas();
            });

            return Redirect::route('consulta.index')
                ->with('success', 'Consulta eliminada correctamente.');
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            report($e);

            return back()->with('error', 'No se pudo eliminar la consulta. Intente nuevamente.');
        }
    }

    public function marcarCompletada(Consulta $consulta): RedirectResponse
    {
        $this->authorize('complete', $consulta);
        try {
            $this->consultaService->finalizar($consulta);

            return redirect()->route('consulta.show', $consulta)
                ->with('success', 'Atención finalizada. Los servicios cobrables quedan disponibles en Caja.');
        } catch (\Exception $e) {
            report($e);

            return back()->with('error', 'No se pudo finalizar la consulta. Intente nuevamente.');
        }
    }
}
