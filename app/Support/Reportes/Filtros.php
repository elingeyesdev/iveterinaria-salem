<?php

namespace App\Support\Reportes;

use Carbon\CarbonImmutable;

/**
 * Filtros ya validados y normalizados que reciben todos los reportes.
 */
final readonly class Filtros
{
    public function __construct(
        public CarbonImmutable $desde,
        public CarbonImmutable $hasta,
        public ?int $veterinarioId = null,
        public ?string $tipoPago = null,
    ) {}

    public function inicio(): CarbonImmutable
    {
        return $this->desde->startOfDay();
    }

    public function fin(): CarbonImmutable
    {
        return $this->hasta->endOfDay();
    }

    /** Cantidad de días del rango, ambos extremos incluidos. */
    public function dias(): int
    {
        return (int) floor($this->desde->startOfDay()->diffInDays($this->hasta->startOfDay(), true)) + 1;
    }

    /** @return array<string, string|int> Parámetros de query string equivalentes. */
    public function toQuery(): array
    {
        return array_filter([
            'desde' => $this->desde->toDateString(),
            'hasta' => $this->hasta->toDateString(),
            'veterinario_id' => $this->veterinarioId,
            'tipo_pago' => $this->tipoPago,
        ], fn ($v) => $v !== null && $v !== '');
    }
}
