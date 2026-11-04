<?php

namespace App\Services;

use App\Models\Consulta;
use App\Support\ConsultaTipo;
use Illuminate\Support\Facades\DB;

/**
 * Coordina el proceso de creación y actualización de consultas.
 *
 * Este Service no concentra toda la lógica.
 * Delega:
 *
 * - Validaciones → ConsultaValidacionService
 * - Servicios aplicados → ConsultaServicioService
 * - Peso de mascota → MascotaService
 *
 * Los pagos se gestionan de forma independiente.
 */
class ConsultaService
{
    public function __construct(
        private ConsultaValidacionService $validacionService,
        private ConsultaServicioService $consultaServicioService,
        private MascotaService $mascotaService,
        private PagoService $pagoService,
    ) {}

    /**
     * Crea una nueva consulta.
     */
    public function crear(array $datos): Consulta
    {
        return DB::transaction(function () use ($datos) {

            /*
             * Validamos los datos antes de crear la consulta.
             */
            $this->validacionService->validarCrear($datos);
            if (! empty($datos['reserva_id']) && Consulta::where('reserva_id', $datos['reserva_id'])->lockForUpdate()->first(['id']) !== null) {
                throw new \DomainException('Esta reserva ya tiene una consulta. Abra la atención existente.');
            }

            /*
             * Normalizamos el peso.
             */
            $peso = $this->validacionService->normalizarPeso(
                $datos['peso'] ?? null
            );

            /*
             * Normalizamos la reserva.
             */
            $reservaId = $this->validacionService->normalizarReservaId(
                $datos['reserva_id'] ?? null
            );

            /*
             * Creamos la consulta.
             */
            $consulta = Consulta::create([
                'fecha' => $datos['fecha'],
                'tipo' => $datos['tipo'] ?? ConsultaTipo::RESERVA,
                'veterinario_id' => (int) $datos['veterinario_id'],
                'reserva_id' => $reservaId,
                'peso' => $peso,
                'motivo' => trim((string) $datos['motivo']),
                'diagnostico' => $datos['diagnostico'] ?? null,
                'tratamiento' => $datos['tratamiento'] ?? null,
            ]);

            /*
             * Actualizamos el peso actual de la mascota.
             */
            $this->mascotaService->actualizarPesoDesdeConsulta(
                $consulta
            );

            /*
             * Registramos los servicios de la consulta.
             *
             * false = no reemplazar servicios existentes.
             */
            $this->consultaServicioService->sincronizar(
                $consulta,
                $datos['servicios'] ?? [],
                false
            );

            /*
             * Si la consulta proviene de una reserva,
             * la reserva pasa a estado atendido.
             */
            $this->marcarReservaAtendida($consulta);

            /*
             * Retornamos la consulta actualizada.
             */
            return $consulta->fresh([
                'reserva.mascota',
                'servicios',
                'pagos',
            ]);
        });
    }

    /**
     * Actualiza una consulta existente.
     */
    public function actualizar(Consulta $consulta, array $datos): Consulta
    {
        return DB::transaction(function () use ($consulta, $datos) {
            $consulta = Consulta::query()->lockForUpdate()->findOrFail($consulta->id);
            if ($consulta->estado_clinico === Consulta::FINALIZADA) {
                throw new \DomainException('No se puede editar una consulta finalizada.');
            }

            // No se puede alterar la composición de servicios de una consulta cobrada.
            // Se permite editar datos clínicos, pero la sincronización de servicios
            // verifica que no cambien los IDs cuando hay pagos activos.

            /*
             * Guardamos la reserva anterior.
             *
             * Esto permite detectar si durante la edición
             * se cambió la reserva asociada.
             */
            $reservaAnteriorId = $consulta->reserva_id;

            /*
             * Validamos los datos.
             */
            $this->validacionService->validarActualizar($datos);

            /*
             * Determinamos el peso.
             */
            $pesoEnviado =
                array_key_exists('peso', $datos) &&
                $datos['peso'] !== '' &&
                $datos['peso'] !== null;

            $peso = $pesoEnviado ? $this->validacionService->normalizarPeso($datos['peso']) : (float) ($consulta->peso ?? 0);

            /*
             * Determinamos la reserva.
             */
            $reservaId = array_key_exists('reserva_id', $datos)
                ? $this->validacionService->normalizarReservaId($datos['reserva_id'])
                : $consulta->reserva_id;

            if ((int) $reservaId !== (int) $consulta->reserva_id && $consulta->pagos()->exists()) {
                throw new \DomainException('No se puede cambiar la reserva de una consulta con movimientos de pago.');
            }

            /*
             * Actualizamos la consulta.
             */
            $consulta->update([
                'fecha' => $datos['fecha'],
                'tipo' => $datos['tipo'] ?? $consulta->tipo,
                'veterinario_id' => (int) $datos['veterinario_id'],
                'reserva_id' => $reservaId,
                'peso' => $peso,
                'motivo' => trim((string) $datos['motivo']),
                'diagnostico' => $datos['diagnostico'] ?? null,
                'tratamiento' => $datos['tratamiento'] ?? null,
            ]);

            /*
             * Sincronizamos el peso de la mascota.
             */
            $this->mascotaService->actualizarPesoDesdeConsulta($consulta);

            /*
             * En edición:
             *
             * true = reemplazar exactamente los servicios
             * enviados por el formulario.
             */
            $this->consultaServicioService->sincronizar($consulta, $datos['servicios'] ?? [], true);

            $consulta->load(['servicios', 'pagos']);
            if (round($consulta->total_servicios * 100) < round($consulta->total_pagado * 100)) {
                throw new \DomainException('El total de servicios no puede ser menor que los pagos activos registrados.');
            }

            /*
             * Si cambió la reserva, marcamos la nueva
             * como atendida.
             */
            if ($reservaId !== null && (int) $reservaId !== (int) $reservaAnteriorId) {
                $this->marcarReservaAtendida($consulta);
            }

            /*
             * Si no cambió la reserva pero existe una
             * reserva asociada, nos aseguramos de que
             * quede atendida.
             */
            if ($reservaId !== null && (int) $reservaId === (int) $reservaAnteriorId) {
                $this->marcarReservaAtendida($consulta);
            }

            return $consulta->fresh([
                'reserva.mascota',
                'servicios',
                'pagos',
            ]);
        });
    }

    /**
     * Finaliza la atención y prepara el pago pendiente sin registrar un cobro.
     */
    public function finalizar(Consulta $consulta): Consulta
    {
        return DB::transaction(function () use ($consulta): Consulta {
            $actual = Consulta::query()->lockForUpdate()->findOrFail($consulta->id);

            if ($actual->estado_clinico !== Consulta::FINALIZADA) {
                $this->pagoService->prepararPendiente($actual);

                $actual->forceFill([
                    'estado_clinico' => Consulta::FINALIZADA,
                    'finalizada_at' => now(),
                ])->save();
            }

            return $actual;
        });
    }

    private function marcarReservaAtendida(Consulta $consulta): void
    {
        $consulta->loadMissing('reserva');

        if (! $consulta->reserva) {
            return;
        }

        $consulta->reserva->marcarAtendida();
    }
}
