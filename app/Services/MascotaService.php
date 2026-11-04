<?php

namespace App\Services;

use App\Models\Consulta;

/**
 * Gestiona operaciones relacionadas con la mascota
 * que dependen de una consulta.
 */
class MascotaService
{
    /**
     * Actualiza el peso actual de la mascota
     * utilizando el peso registrado en la consulta.
     */
    public function actualizarPesoDesdeConsulta(
        Consulta $consulta
    ): void {

        /*
         * La relación mascotum debe estar definida
         * en el modelo Consulta.
         */
        $mascota = $consulta->mascotum;

        /*
         * Si la consulta no tiene mascota relacionada,
         * no hacemos nada.
         */
        if (! $mascota) {
            return;
        }

        /*
         * Un peso igual o menor a cero no representa
         * un peso clínico válido para actualizar.
         */
        if ((float) $consulta->peso <= 0) {
            return;
        }

        /*
         * Actualizamos el peso actual de la mascota.
         */
        $mascota->update([
            'peso' => round(
                (float) $consulta->peso,
                2
            ),
        ]);
    }
}
