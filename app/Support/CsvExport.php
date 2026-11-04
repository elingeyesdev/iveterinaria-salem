<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Exportación CSV en streaming, lista para abrir en Excel (BOM UTF-8,
 * separador ";" y coma decimal, que es lo que espera Excel en español).
 *
 * Neutraliza "CSV injection": una celda de texto que empieza con = + - @
 * podría ejecutarse como fórmula al abrirla en Excel.
 */
final class CsvExport
{
    /**
     * @param  array<int, array{key:string,label:string,tipo:string}>  $columnas
     * @param  iterable<int, array<string, mixed>>  $filas
     */
    public static function descargar(string $nombreArchivo, array $columnas, iterable $filas): StreamedResponse
    {
        return response()->streamDownload(function () use ($columnas, $filas): void {
            $salida = fopen('php://output', 'w');

            fwrite($salida, "\xEF\xBB\xBF");
            fputcsv($salida, array_map(fn ($c) => $c['label'], $columnas), ';', '"', '\\');

            foreach ($filas as $fila) {
                $linea = [];
                foreach ($columnas as $columna) {
                    $linea[] = self::celda($fila[$columna['key']] ?? null, $columna['tipo']);
                }
                fputcsv($salida, $linea, ';', '"', '\\');
            }

            fclose($salida);
        }, $nombreArchivo, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    private static function celda(mixed $valor, string $tipo): string
    {
        if ($valor === null || $valor === '') {
            return '';
        }

        return match ($tipo) {
            'moneda' => number_format((float) $valor, 2, ',', ''),
            'porcentaje' => number_format((float) $valor, 1, ',', ''),
            'entero' => (string) (int) $valor,
            default => self::neutralizar((string) $valor),
        };
    }

    private static function neutralizar(string $texto): string
    {
        return preg_match('/^[=+\-@\t\r]/', $texto) === 1 ? "'".$texto : $texto;
    }
}
