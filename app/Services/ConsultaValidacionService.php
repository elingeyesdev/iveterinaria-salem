<?php

namespace App\Services;

use App\Models\Reserva;
use App\Models\Veterinario;
use App\Support\ReservaEstado;
use InvalidArgumentException;

/**
 * Centraliza las validaciones de negocio de las consultas.
 *
 * No reemplaza la validación del FormRequest.
 * Actúa como una segunda capa de protección dentro
 * de la lógica de negocio.
 */
class ConsultaValidacionService
{
    /**
     * Valida los datos necesarios para crear una consulta.
     */
    public function validarCrear(array $datos): void
    {
        $this->validarDatosConsulta($datos);

        $this->validarVeterinario(
            (int) $datos['veterinario_id']
        );

        $reservaId = $this->normalizarReservaId(
            $datos['reserva_id'] ?? null
        );

        if ($reservaId !== null) {
            $this->validarReserva($reservaId);
        }
    }

    /**
     * Valida los datos necesarios para actualizar una consulta.
     */
    public function validarActualizar(array $datos): void
    {
        $this->validarDatosConsulta($datos);

        $this->validarVeterinario(
            (int) $datos['veterinario_id']
        );

        if (array_key_exists('reserva_id', $datos)) {

            $reservaId = $this->normalizarReservaId(
                $datos['reserva_id']
            );

            if ($reservaId !== null) {
                $this->validarReserva($reservaId);
            }
        }
    }

    /**
     * Valida los campos básicos de una consulta.
     */
    private function validarDatosConsulta(
        array $datos
    ): void {
        if (
            ! isset($datos['fecha']) ||
            trim((string) $datos['fecha']) === ''
        ) {
            throw new InvalidArgumentException(
                'La fecha de la consulta es obligatoria.'
            );
        }

        if (
            ! isset($datos['veterinario_id']) ||
            ! is_numeric($datos['veterinario_id'])
        ) {
            throw new InvalidArgumentException(
                'Debe seleccionar un veterinario válido.'
            );
        }

        if (
            ! array_key_exists('motivo', $datos) ||
            trim((string) $datos['motivo']) === ''
        ) {
            throw new InvalidArgumentException(
                'El motivo de la consulta es obligatorio.'
            );
        }
    }

    /**
     * Valida que el veterinario exista y esté activo.
     */
    public function validarVeterinario(
        int $veterinarioId
    ): void {
        $existe = Veterinario::query()
            ->whereKey($veterinarioId)
            ->where('estado', 1)
            ->exists();

        if (! $existe) {
            throw new InvalidArgumentException(
                'El veterinario seleccionado no existe o está inactivo.'
            );
        }
    }

    /**
     * Valida que la reserva exista.
     */
    public function validarReserva(
        int $reservaId
    ): void {
        $reserva = Reserva::query()
            ->lockForUpdate()
            ->find($reservaId);

        if ($reserva && in_array($reserva->estado, [
            ReservaEstado::CANCELADA,
            ReservaEstado::NO_ASISTIO,
        ], true)) {
            throw new InvalidArgumentException('No se puede atender una reserva cancelada o marcada como no asistida.');
        }

        if (! $reserva) {
            throw new InvalidArgumentException(
                'La reserva seleccionada no existe.'
            );
        }
    }

    /**
     * Normaliza el ID de reserva.
     */
    public function normalizarReservaId(
        mixed $reservaId
    ): ?int {
        if (
            $reservaId === null ||
            $reservaId === ''
        ) {
            return null;
        }

        if (! is_numeric($reservaId)) {
            throw new InvalidArgumentException(
                'La reserva seleccionada no es válida.'
            );
        }

        $reservaId = (int) $reservaId;

        if ($reservaId <= 0) {
            return null;
        }

        return $reservaId;
    }

    /**
     * Normaliza y valida el peso.
     */
    public function normalizarPeso(
        mixed $peso
    ): float {
        if ($peso === null || $peso === '') {
            return 0.0;
        }

        if (! is_numeric($peso)) {
            throw new InvalidArgumentException(
                'El peso de la mascota debe ser numérico.'
            );
        }

        $peso = (float) $peso;

        if ($peso < 0) {
            throw new InvalidArgumentException(
                'El peso de la mascota no puede ser negativo.'
            );
        }

        return round($peso, 2);
    }
}
