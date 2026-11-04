<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Consulta;
use App\Models\Especie;
use App\Models\Mascotum;
use App\Models\Pago;
use App\Models\Reserva;
use App\Support\ReservaEstado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $estadisticas = [];
        $upcomingReservations = collect();
        $recentConsultas = collect();
        $pagosPendientes = collect();
        $especies = collect();
        $weeklyLabels = [];
        $weeklySeries = [];

        if (Gate::allows('permiso.reservas')) {
            $estadisticas['Reservas de hoy'] = Reserva::whereDate('fecha', today())->count();
            $upcomingReservations = Reserva::with(['mascota.cliente', 'consulta'])
                ->where('estado', ReservaEstado::PENDIENTE)->whereDate('fecha', '>=', today())
                ->orderBy('fecha')->orderBy('hora_inicio')->limit(8)->get();
            $inicio = now()->startOfWeek();
            $semanales = Reserva::whereBetween('fecha', [$inicio->toDateString(), $inicio->copy()->endOfWeek()->toDateString()])
                ->select('fecha', DB::raw('COUNT(*) as total'))->groupBy('fecha')->pluck('total', 'fecha');
            for ($dia = 0; $dia < 7; $dia++) {
                $fecha = $inicio->copy()->addDays($dia);
                $weeklyLabels[] = $fecha->translatedFormat('D');
                $weeklySeries[] = $semanales[$fecha->toDateString()] ?? 0;
            }
        }
        if (Gate::allows('permiso.consultas')) {
            $estadisticas['Consultas en atención'] = Consulta::where('estado_clinico', Consulta::EN_ATENCION)->count();
            $recentConsultas = Consulta::with(['reserva.mascota.cliente', 'veterinario'])
                ->where('estado_clinico', Consulta::EN_ATENCION)->orderBy('fecha')->orderBy('id')->limit(8)->get();
        }
        if (Gate::allows('permiso.pagos')) {
            $estadisticas['Cuentas pendientes'] = Pago::where('estado', Pago::PENDIENTE)->count();
            $pagosPendientes = Pago::with('consulta.reserva.mascota.cliente')->where('estado', Pago::PENDIENTE)
                ->orderBy('id')->limit(8)->get();
        }
        if (Gate::allows('permiso.reportes.ingresos')) {
            $estadisticas['Ingresos de hoy (Bs)'] = number_format((float) Pago::where('estado', Pago::PAGADO)->whereDate('updated_at', today())->sum('monto'), 2);
        }
        if (Gate::allows('permiso.clientes')) {
            $estadisticas['Clientes registrados'] = Cliente::count();
        }
        if (Gate::allows('permiso.mascotas')) {
            $estadisticas['Mascotas registradas'] = Mascotum::count();
            $especies = Especie::withCount('mascotas')->orderByDesc('mascotas_count')->get();
        }

        return view('home', compact('estadisticas', 'upcomingReservations', 'recentConsultas', 'pagosPendientes', 'especies', 'weeklyLabels', 'weeklySeries'));
    }
}
