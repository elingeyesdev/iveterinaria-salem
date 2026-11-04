<?php

namespace App\Support;

/**
 * Estados válidos del ciclo de vida de una reserva.
 *
 * Mantiene los valores compatibles con la estructura existente de la BD;
 * no crea ni modifica columnas.
 */
final class ReservaEstado
{
    public const PENDIENTE = 'Pendiente';

    public const ATENDIDA = 'Atendida';

    public const CANCELADA = 'Cancelado';

    public const NO_ASISTIO = 'No asistio';

    /** @return array<int, string> */
    public static function all(): array
    {
        return [
            self::PENDIENTE,
            self::ATENDIDA,
            self::CANCELADA,
            self::NO_ASISTIO,
        ];
    }

    public static function isActive(string $estado): bool
    {
        return ! in_array($estado, [self::CANCELADA, self::NO_ASISTIO], true);
    }
}
