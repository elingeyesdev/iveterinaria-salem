<?php

namespace App\Services;

use App\Models\Consulta;
use App\Models\Pago;
use App\Models\Reserva;
use App\Support\ReservaEstado;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

final class KpiService
{
    /** Los KPI se agregan en SQL para evitar cargar expedientes clínicos. */
    public function resumen(CarbonImmutable $desde, CarbonImmutable $hasta): array
    {
        $inicio = $desde->toDateString();
        $fin = $hasta->toDateString();

        $reservas = Reserva::query()->whereBetween('fecha', [$inicio, $fin]);
        $totalReservas = (clone $reservas)->count();
        $atendidas = (clone $reservas)->where('estado', ReservaEstado::ATENDIDA)->count();
        $noAsistio = (clone $reservas)->where('estado', ReservaEstado::NO_ASISTIO)->count();
        $canceladas = (clone $reservas)->where('estado', ReservaEstado::CANCELADA)->count();
        $concluidas = $atendidas + $noAsistio;

        $consultas = Consulta::query()->whereBetween('fecha', [$inicio, $fin]);
        $totalConsultas = (clone $consultas)->count();
        $finalizadas = (clone $consultas)->where('estado_clinico', Consulta::FINALIZADA)->count();

        // El período financiero corresponde a la fecha del cobro (created_at),
        // no a la fecha clínica. No incluye pagos anulados ni pendientes.
        $ingresos = (float) Pago::query()
            ->where('estado', Pago::PAGADO)
            ->whereBetween('created_at', [$desde->startOfDay(), $hasta->endOfDay()])
            ->sum('monto');

        $servicios = (int) DB::table('consulta_servicio as cs')
            ->join('consulta as c', 'c.id', '=', 'cs.consulta_id')
            ->whereBetween('c.fecha', [$inicio, $fin])
            ->count();

        $porEstado = (clone $reservas)->select('estado')
            ->selectRaw('COUNT(*) as total')->groupBy('estado')
            ->pluck('total', 'estado')->map(fn ($n) => (int) $n)->all();

        $porMes = (clone $reservas)->selectRaw('YEAR(fecha) as anio, MONTH(fecha) as mes, COUNT(*) as total')
            ->groupByRaw('YEAR(fecha), MONTH(fecha)')
            ->orderBy('anio')->orderBy('mes')->get()
            ->map(fn ($r) => ['etiqueta' => sprintf('%04d-%02d', $r->anio, $r->mes), 'total' => (int) $r->total])->all();

        return [
            'reservas' => $totalReservas,
            'atendidas' => $atendidas,
            'no_asistio' => $noAsistio,
            'canceladas' => $canceladas,
            'asistencia' => $concluidas > 0 ? round($atendidas * 100 / $concluidas, 1) : null,
            'consultas' => $totalConsultas,
            'finalizadas' => $finalizadas,
            'ingresos' => round($ingresos, 2),
            'servicios' => $servicios,
            'estados' => $porEstado,
            'meses' => $porMes,
        ];
    }
}
