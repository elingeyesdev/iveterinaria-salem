<?php

namespace App\Services;

use App\Mail\ReservaConfirmadaMail;
use App\Models\Recordatorio;
use App\Models\Reserva;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Centraliza las notificaciones relacionadas con el dominio veterinario.
 *
 * No modifica la estructura de la base de datos. Reutiliza los Mailable y
 * Recordatorios que ya existen en el proyecto.
 */
class NotificacionService
{
    public function __construct(
        private readonly RecordatorioCorreoService $recordatorioCorreoService,
    ) {}

    /**
     * Envía el correo de confirmación de una reserva.
     *
     * Devuelve false cuando la reserva no tiene un correo válido o cuando
     * el envío falla; la reserva no se elimina por un fallo de correo.
     */
    public function enviarConfirmacionReserva(Reserva $reserva): bool
    {
        $reserva->loadMissing('mascota.cliente');
        $correo = $reserva->mascota?->cliente?->correo;

        if (! $correo) {
            Log::warning('Reserva sin correo para confirmación', [
                'reserva_id' => $reserva->id,
            ]);

            return false;
        }

        try {
            Mail::to($correo)->send(new ReservaConfirmadaMail($reserva));

            return true;
        } catch (Throwable $e) {
            Log::error('No se pudo enviar confirmación de reserva', [
                'reserva_id' => $reserva->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Crea o actualiza el recordatorio automático de una reserva.
     */
    public function programarRecordatorioReserva(Reserva $reserva, int $horasAntes = 24): ?Recordatorio
    {
        return $this->recordatorioCorreoService->crearParaReserva($reserva, $horasAntes);
    }

    /**
     * Cancela recordatorios pendientes asociados a una reserva.
     */
    public function cancelarRecordatoriosReserva(Reserva $reserva): void
    {
        $this->recordatorioCorreoService->cancelarParaReserva($reserva);
    }

    /**
     * Intenta enviar un recordatorio que ya esté listo para envío.
     */
    public function enviarRecordatorio(Recordatorio $recordatorio, bool $forzar = false): bool
    {
        return $this->recordatorioCorreoService->enviar($recordatorio, $forzar);
    }
}
