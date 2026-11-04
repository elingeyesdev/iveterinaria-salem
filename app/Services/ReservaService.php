<?php

namespace App\Services;

use App\Mail\ReservaConfirmadaMail;
use App\Models\Cliente;
use App\Models\Mascotum;
use App\Models\Reserva;
use App\Support\ReservaEstado;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReservaService
{
    public function cambiarEstado(Reserva $reserva, string $estado): void
    {
        DB::transaction(function () use ($reserva, $estado): void {
            $actual = Reserva::query()->lockForUpdate()->findOrFail($reserva->id);

            if (! in_array($estado, ReservaEstado::all(), true)) {
                throw new \DomainException('Estado de reserva no valido.');
            }

            if ($actual->consulta()->exists()) {
                if ($estado !== ReservaEstado::ATENDIDA) {
                    throw new \DomainException('La reserva tiene una consulta registrada y debe permanecer Atendida.');
                }
            } elseif ($estado !== $actual->estado) {
                if ($actual->estado !== ReservaEstado::PENDIENTE ||
                    ! in_array($estado, [ReservaEstado::CANCELADA, ReservaEstado::NO_ASISTIO], true)) {
                    throw new \DomainException('Esta transicion no esta permitida. Registre una nueva reserva si necesita otra cita.');
                }
            }

            $actual->update(['estado' => $estado]);
        });
    }

    /** Duración de cada turno de atención, en minutos. */
    public const DURACION_MINUTOS = 20;

    /**
     * Valida la mascota y crea la reserva de manera segura, enviando el correo de confirmación.
     * Lanza una excepción si hay duplicado de horario.
     */
    public function crearReserva(array $datos)
    {
        $clienteActivo = Cliente::whereKey($datos['cliente_id'] ?? null)
            ->where('estado', 1)
            ->exists();

        $mascota = Mascotum::whereKey($datos['mascota_id'] ?? null)
            ->where('cliente_id', $datos['cliente_id'] ?? null)
            ->where('estado', 1)
            ->first();

        if (! $clienteActivo || ! $mascota) {
            return [
                'success' => false,
                'message' => 'La mascota seleccionada no pertenece al cliente registrado.',
            ];
        }

        try {
            $reserva = DB::transaction(function () use ($datos, $mascota): Reserva {
                $this->liberarHorarioCancelado($datos['fecha'], $datos['hora_inicio']);

                return Reserva::create([
                    'fecha' => $datos['fecha'],
                    'hora_inicio' => $datos['hora_inicio'],
                    'hora_fin' => $this->calcularHoraFin($datos['hora_inicio']),
                    'mascota_id' => $mascota->id,
                    'estado' => ReservaEstado::PENDIENTE,
                ]);
            });

            // ReservaConfirmadaMail implementa ShouldQueue: se envía por la cola.
            if ($reserva->mascota && $reserva->mascota->cliente && $reserva->mascota->cliente->correo) {
                Mail::to($reserva->mascota->cliente->correo)
                    ->send(new ReservaConfirmadaMail($reserva));
            }

            return ['success' => true, 'reserva' => $reserva];

        } catch (QueryException $e) {
            Log::warning('Intento de reserva duplicada', [
                'fecha' => $datos['fecha'],
                'hora_inicio' => $datos['hora_inicio'],
            ]);

            return [
                'success' => false,
                'message' => 'Ese horario ya fue reservado. Seleccione otro horario disponible.',
            ];
        }
    }

    /**
     * Libera el turno ocupado por una reserva cancelada.
     *
     * La tabla reserva tiene unique(fecha, hora_inicio). Una reserva cancelada
     * se conserva (la usa el reporte de reservas y asistencia), pero no debe
     * impedir que otra persona tome ese turno. Para no modificar la tabla, a la
     * fila cancelada se le asigna un segundo libre (09:00:00 -> 09:00:01).
     * El modelo Reserva muestra las horas como H:i, así que el cambio no se ve
     * en pantallas, correos ni reportes.
     *
     * Debe llamarse dentro de una transacción.
     */
    public function liberarHorarioCancelado(string $fecha, string $horaInicio, ?int $exceptoReservaId = null): void
    {
        $hora = substr($horaInicio, 0, 5);

        $filas = Reserva::query()
            ->whereDate('fecha', $fecha)
            ->where('hora_inicio', 'like', $hora.'%')
            ->lockForUpdate()
            ->get();

        $segundosUsados = $filas
            ->map(fn (Reserva $fila): int => $this->segundoDe($fila->getRawOriginal('hora_inicio')))
            ->all();

        $canceladasEnElTurno = $filas->filter(fn (Reserva $fila): bool => $fila->estado === ReservaEstado::CANCELADA
            && $fila->id !== $exceptoReservaId
            && $this->segundoDe($fila->getRawOriginal('hora_inicio')) === 0);

        foreach ($canceladasEnElTurno as $cancelada) {
            $segundoLibre = collect(range(1, 59))->first(fn (int $segundo): bool => ! in_array($segundo, $segundosUsados, true));

            if ($segundoLibre === null) {
                return;
            }

            $segundosUsados[] = $segundoLibre;

            // toBase(): no altera updated_at, para no falsear los reportes.
            Reserva::query()->whereKey($cancelada->id)->toBase()
                ->update(['hora_inicio' => $hora.':'.str_pad((string) $segundoLibre, 2, '0', STR_PAD_LEFT)]);
        }
    }

    /**
     * Segundo de una hora guardada ("09:00" o "09:00:00" -> 0, "09:00:07" -> 7).
     */
    private function segundoDe(?string $hora): int
    {
        return $hora !== null && strlen($hora) >= 8 ? (int) substr($hora, 6, 2) : 0;
    }

    /**
     * Obtiene y formatea los eventos para FullCalendar.
     */
    public function obtenerEventosFormateados($request): array
    {
        $query = Reserva::with('mascota.cliente')
            ->where('estado', '!=', ReservaEstado::CANCELADA);

        if ($request->filled('start')) {
            $query->whereDate('fecha', '>=', Carbon::parse($request->start)->toDateString());
        }

        if ($request->filled('end')) {
            $query->whereDate('fecha', '<=', Carbon::parse($request->end)->toDateString());
        }

        return $query->get()->map(function (Reserva $reserva) {
            $cliente = $reserva->mascota?->cliente;
            $nombreCliente = trim(($cliente?->nombre ?? '').' '.($cliente?->apellido ?? '')) ?: 'Sin cliente';
            $mascota = $reserva->mascota?->nombre ?? 'Sin mascota';

            $start = Carbon::parse($reserva->fecha.' '.$reserva->hora_inicio);
            $end = Carbon::parse($reserva->fecha.' '.$reserva->hora_fin);

            return [
                'id' => $reserva->id,
                'title' => $mascota.' - '.$nombreCliente,
                'start' => $start->toIso8601String(),
                'end' => $end->toIso8601String(),
                'extendedProps' => [
                    'cliente' => $nombreCliente,
                    'mascota' => $mascota,
                    'fecha' => $reserva->fecha,
                    'hora' => $reserva->hora_inicio.' - '.$reserva->hora_fin,
                    'estado' => $reserva->estado,
                ],
            ];
        })->toArray();
    }

    /**
     * La hora de fin siempre la calcula el servidor.
     */
    public function calcularHoraFin(string $horaInicio): string
    {
        return Carbon::createFromFormat('H:i', substr($horaInicio, 0, 5))
            ->addMinutes(self::DURACION_MINUTOS)
            ->format('H:i');
    }

    /**
     * Indica si la hora es un turno real de la clínica para esa fecha.
     */
    public function esHorarioValido(string $fecha, string $horaInicio): bool
    {
        foreach ($this->obtenerTurnos($fecha) as $turno) {
            $slots = $this->generarSlots($turno['inicio'], $turno['fin'], [], $fecha);

            if (in_array($horaInicio, array_column($slots, 'hora'), true)) {
                return true;
            }
        }

        return false;
    }

    public function obtenerTurnos(string $fecha): array
    {
        $dia = Carbon::parse($fecha)->dayOfWeek;

        if ($dia === 0) {
            return [];
        }

        if ($dia === 6) {
            return [[
                'nombre' => 'Mañana',
                'inicio' => '09:00',
                'fin' => '13:00',
            ]];
        }

        return [
            ['nombre' => 'Mañana', 'inicio' => '09:00', 'fin' => '12:00'],
            ['nombre' => 'Tarde', 'inicio' => '14:00', 'fin' => '18:00'],
        ];
    }

    public function obtenerHorasOcupadas(string $fecha, $reservaId = null): array
    {
        $query = Reserva::whereDate('fecha', $fecha)
            ->where('estado', '!=', ReservaEstado::CANCELADA);

        if ($reservaId) {
            $query->where('id', '!=', $reservaId);
        }

        return $query->pluck('hora_inicio')
            ->map(fn ($hora) => substr($hora, 0, 5))
            ->toArray();
    }

    public function generarSlots(string $inicio, string $fin, array $ocupados, string $fecha): array
    {
        $slots = [];
        $hora = Carbon::createFromFormat('H:i', $inicio);
        $finHora = Carbon::createFromFormat('H:i', $fin);
        $esHoy = Carbon::parse($fecha)->isToday();
        $horaActual = Carbon::now()->format('H:i');

        while ($hora < $finHora) {
            $actual = $hora->format('H:i');
            $pasado = $esHoy && $actual < $horaActual;
            $slots[] = [
                'hora' => $actual,
                'ocupado' => in_array($actual, $ocupados, true),
                'pasado' => $pasado,
            ];
            $hora->addMinutes(self::DURACION_MINUTOS);
        }

        return $slots;
    }
}
