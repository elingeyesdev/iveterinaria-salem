<?php

namespace App\Http\Controllers;

use App\Services\KpiService;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

final class KpiController extends Controller
{
    public function __construct(private readonly KpiService $kpis) {}

    public function index(Request $request): View
    {
        Gate::authorize('permiso.reportes.kpi');

        $datos = $request->validate([
            'desde' => ['nullable', 'date_format:Y-m-d'],
            'hasta' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:desde'],
        ]);

        $hoy = CarbonImmutable::today();
        $desde = isset($datos['desde']) ? CarbonImmutable::createFromFormat('!Y-m-d', $datos['desde']) : $hoy->startOfMonth();
        $hasta = isset($datos['hasta']) ? CarbonImmutable::createFromFormat('!Y-m-d', $datos['hasta']) : $hoy;

        if ($desde->greaterThan($hasta) || $desde->diffInDays($hasta) > 731) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'hasta' => 'El rango debe ser válido y no superar 731 días.',
            ]);
        }

        return view('kpi.index', [
            'desde' => $desde->toDateString(),
            'hasta' => $hasta->toDateString(),
            'kpi' => $this->kpis->resumen($desde, $hasta),
        ]);
    }
}
