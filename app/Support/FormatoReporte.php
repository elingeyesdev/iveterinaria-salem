<?php

namespace App\Support;

/**
 * Formato de valores compartido por la pantalla y la vista de impresión.
 * (El CSV formatea por su cuenta porque necesita números "puros" para Excel.)
 */
final class FormatoReporte
{
    public static function valor(mixed $valor, string $tipo): string
    {
        if ($tipo !== 'texto' && ($valor === null || $valor === '')) {
            return '—';
        }

        return match ($tipo) {
            'moneda' => 'Bs '.number_format((float) $valor, 2, '.', ','),
            'entero' => number_format((int) $valor, 0, '.', ','),
            'porcentaje' => number_format((float) $valor, 1, '.', ',').'%',
            default => (string) $valor,
        };
    }
}
