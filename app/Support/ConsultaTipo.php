<?php

namespace App\Support;

/**
 * Tipos de atención clínica soportados por la estructura actual.
 */
final class ConsultaTipo
{
    public const RESERVA = 'Reserva';

    public const EMERGENCIA = 'Emergencia';

    /** @return array<int, string> */
    public static function all(): array
    {
        return [self::RESERVA, self::EMERGENCIA];
    }
}
