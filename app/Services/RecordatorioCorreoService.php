<?php

namespace App\Services;

use App\Mail\RecordatorioReservaMail;
use App\Models\Recordatorio;
use App\Models\Reserva;
use App\Support\ReservaEstado;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class RecordatorioCorreoService
{
    public function crearParaReserva(Reserva $reserva, int $horasAntes = 24): ?Recordatorio
    {
        $reserva->loadMissing('mascota.cliente');
        $mascota = $reserva->mascota;

        if (! $mascota) {
            return null;
        }

        $momentoCita = $reserva->fecha.' '.$reserva->hora_inicio;
        $fechaEnvio = now()->parse($momentoCita)->subHours($horasAntes);

        return Recordatorio::updateOrCreate(
            ['reserva_id' => $reserva->id],
            [
                'mascota_id' => $mascota->id,
                'tipo' => 'Control veterinario',
                'titulo' => 'Recordatorio de cita: '.$mascota->nombre,
                'descripcion' => 'Recordatorio automático de la reserva veterinaria.',
                'fecha' => $reserva->fecha,
                'estado' => $reserva->estado === ReservaEstado::PENDIENTE ? ReservaEstado::PENDIENTE : ReservaEstado::CANCELADA,
                'fecha_envio' => $fechaEnvio,
                'enviado_at' => null,
                'canal' => 'correo',
                'error_envio' => null,
            ]
        );
    }

    public function actualizarParaReserva(Reserva $reserva, int $horasAntes = 24): ?Recordatorio
    {
        return $this->crearParaReserva($reserva, $horasAntes);
    }

    public function cancelarParaReserva(Reserva $reserva): void
    {
        Recordatorio::where('reserva_id', $reserva->id)
            ->whereNull('enviado_at')
            ->update(['estado' => ReservaEstado::CANCELADA]);
    }

    public function enviar(Recordatorio $recordatorio, bool $forzar = false): bool
    {
        $recordatorio->loadMissing(['mascota.cliente', 'reserva', 'consulta', 'servicio']);

        // Los recordatorios de reservas solo se envían mientras la reserva
        // siga pendiente. Los recordatorios de fecha_prox pertenecen a una
        // consulta ya realizada y no dependen del estado de la reserva.
        if ($recordatorio->reserva && $recordatorio->reserva->estado !== ReservaEstado::PENDIENTE) {
            $recordatorio->update(['estado' => ReservaEstado::CANCELADA]);

            return false;
        }

        if (! $recordatorio->reserva && ! $recordatorio->consulta) {
            $recordatorio->update(['estado' => ReservaEstado::CANCELADA]);

            return false;
        }

        if (! $forzar && $recordatorio->fecha_envio?->isFuture()) {
            return false;
        }

        if ($recordatorio->enviado_at) {
            return false;
        }

        $correo = $recordatorio->mascota?->cliente?->correo;

        if (! $correo) {
            $recordatorio->update(['error_envio' => 'El cliente no tiene correo registrado.']);

            return false;
        }

        try {
            Mail::to($correo)->send(new RecordatorioReservaMail($recordatorio));

            $recordatorio->update([
                'enviado_at' => now(),
                'error_envio' => null,
            ]);

            return true;
        } catch (Throwable $e) {
            Log::error('No se pudo enviar recordatorio de reserva', [
                'recordatorio_id' => $recordatorio->id,
                'reserva_id' => $recordatorio->reserva_id,
                'error' => $e->getMessage(),
            ]);

            $recordatorio->update(['error_envio' => $e->getMessage()]);

            return false;
        }
    }
}
