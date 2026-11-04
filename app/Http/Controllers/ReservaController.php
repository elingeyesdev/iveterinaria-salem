<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservaRequest;
use App\Models\Cliente;
use App\Models\Reserva;
use App\Services\ReservaService;
use App\Support\ReservaEstado;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReservaController extends Controller
{
    protected ReservaService $reservaService;

    public function __construct(ReservaService $reservaService)
    {
        $this->reservaService = $reservaService;
    }

    public function index(Request $request): View|Response
    {
        $this->authorize('viewAny', Reserva::class);

        $search = $request->input('search');

        // Cantidad por página (por defecto 10, máximo 100)
        $perPage = min(max((int) $request->input('perPage', 10), 1), 100);

        $reservas = Reserva::with([
            'mascota.cliente',
            'mascota.especie',
            'mascota.raza',
            'consulta',
        ])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('mascota.cliente', function ($subQ) use ($search) {
                        $subQ->where('nombre', 'LIKE', "%{$search}%")
                            ->orWhere('apellido', 'LIKE', "%{$search}%");
                    })
                        ->orWhereHas('mascota', function ($subQ) use ($search) {
                            $subQ->where('nombre', 'LIKE', "%{$search}%");
                        })
                        ->orWhere('estado', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('fecha', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->view('reserva._table', compact('reservas'));
        }

        return view('reserva.index', compact('reservas'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Reserva::class);
        $reserva = new Reserva;

        // Se incluyen propietarios activos aunque todavía no tengan mascotas,
        // para que el personal pueda iniciar el registro desde este flujo.
        $clientes = Cliente::query()
            ->where('estado', 1)
            ->with(['mascotas' => function ($query) {
                $query->where('estado', 1)->orderBy('nombre');
            }])
            ->orderBy('nombre')
            ->get();

        // Estos identificadores solo preseleccionan opciones del formulario;
        // la pertenencia cliente-mascota vuelve a validarse en servidor al guardar.
        $clienteSeleccionado = $request->integer('cliente_id') ?: null;
        $mascotaSeleccionada = $request->integer('mascota_id') ?: null;

        return view('reserva.create', compact(
            'reserva',
            'clientes',
            'clienteSeleccionado',
            'mascotaSeleccionada'
        ));
    }

    public function store(ReservaRequest $request): RedirectResponse
    {
        $this->authorize('create', Reserva::class);

        $datos = $request->validated();

        $resultado = $this->reservaService->crearReserva($datos);

        if (! $resultado['success']) {
            return redirect()->route('reserva.create')->withInput()
                ->with('error', $resultado['message']);
        }

        return redirect()
            ->route('reserva.index')
            ->with('success', 'Reserva creada correctamente.');
    }

    public function show(Reserva $reserva): View
    {
        $this->authorize('view', $reserva);
        $reserva->load([
            'mascota.cliente',
            'mascota.especie',
            'mascota.raza',
            'consulta',
        ]);

        return view('reserva.show', compact('reserva'));
    }

    public function edit(Reserva $reserva): View
    {
        $this->authorize('update', $reserva);
        $reserva->load('mascota.cliente');

        $clientes = Cliente::where('estado', 1)
            ->with(['mascotas' => function ($query) {
                $query->where('estado', 1)->orderBy('nombre');
            }])
            ->orderBy('nombre')
            ->get();

        return view('reserva.edit', compact('reserva', 'clientes'));
    }

    public function update(ReservaRequest $request, Reserva $reserva): RedirectResponse
    {
        $this->authorize('update', $reserva);
        $datos = $request->validated();

        // Solo se actualiza la reserva existente. Antes se llamaba además a
        // crearReserva(), lo que generaba una reserva duplicada y un correo extra.
        try {
            $mascota = Mascotum::whereKey($datos['mascota_id'])
                ->where('cliente_id', $datos['cliente_id'])
                ->where('estado', 1)
                ->first();

            if (! $mascota) {
                return redirect()->route('reserva.index')
                    ->with('error', 'La mascota seleccionada no pertenece al cliente registrado.');
            }

            DB::transaction(function () use ($reserva, $datos, $mascota): void {
                $this->reservaService->liberarHorarioCancelado($datos['fecha'], $datos['hora_inicio'], $reserva->id);

                $reserva->update([
                    'fecha' => $datos['fecha'],
                    'hora_inicio' => $datos['hora_inicio'],
                    'hora_fin' => $this->reservaService->calcularHoraFin($datos['hora_inicio']),
                    'mascota_id' => $mascota->id,
                ]);
            });
        } catch (QueryException $e) {
            return redirect()->route('reserva.index')
                ->with('error', 'Ese horario ya está ocupado. Seleccione otro horario disponible.');
        }

        return redirect()
            ->route('reserva.index')
            ->with('success', 'Reserva actualizada correctamente.');
    }

    public function destroy(Reserva $reserva): RedirectResponse
    {
        $this->authorize('delete', $reserva);

        try {
            $reserva->delete();
        } catch (QueryException $e) {
            // consulta.reserva_id usa RESTRICT: una reserva ya atendida no se borra.
            return redirect()->route('reserva.index')
                ->with('error', 'No se puede eliminar una reserva que ya tiene una consulta registrada.');
        }

        return redirect()
            ->route('reserva.index')
            ->with('success', 'Reserva eliminada correctamente.');
    }

    public function cambiarEstado(Request $request, Reserva $reserva): RedirectResponse
    {
        $this->authorize('changeStatus', $reserva);
        $data = $request->validate([
            'estado' => ['required', Rule::in(ReservaEstado::all())],
        ]);

        try {
            $this->reservaService->cambiarEstado($reserva, $data['estado']);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Estado actualizado correctamente.');
    }

    public function disponibilidad(Request $request)
    {
        $request->validate([
            'fecha' => ['required', 'date_format:Y-m-d'],
            'reserva_id' => ['nullable', 'integer', 'exists:reserva,id'],
        ]);

        $fecha = $request->input('fecha');
        // reserva_id solo tiene sentido al editar, que es una pantalla interna.
        $reservaId = $request->user() ? $request->input('reserva_id') : null;
        $turnos = $this->reservaService->obtenerTurnos($fecha);

        if (empty($turnos)) {
            return response()->json(['cerrado' => true, 'turnos' => []]);
        }

        $ocupados = $this->reservaService->obtenerHorasOcupadas($fecha, $reservaId);

        foreach ($turnos as &$turno) {
            $turno['slots'] = $this->reservaService->generarSlots($turno['inicio'], $turno['fin'], $ocupados, $fecha);
        }

        return response()->json(['cerrado' => false, 'turnos' => $turnos]);
    }

    public function eventos(Request $request)
    {
        $this->authorize('viewAny', Reserva::class);
        $events = $this->reservaService->obtenerEventosFormateados($request);

        return response()->json($events);
    }
}
