@extends('adminlte::page')
@section('title', 'Panel de control')
@section('content_header')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div>
            <h1 class="mb-1 font-weight-bold">Panel de control</h1>
            <p class="text-muted mb-0">Resumen operativo de la clínica veterinaria.</p>
        </div>
        <span class="badge badge-success px-3 py-2 mt-2 mt-md-0"><i class="fas fa-check-circle mr-1"></i> Sistema operativo</span>
    </div>
@endsection
@section('content')
    <div class="row">
        @forelse(collect($estadisticas)->except(['Consultas en atención', 'Cuentas pendientes']) as $titulo => $valor)
            @php
                [$color, $icono] = match ($titulo) {
                    'Reservas de hoy' => ['info', 'calendar-check'],
                    'Consultas en atención' => ['primary', 'stethoscope'],
                    'Cuentas pendientes' => ['warning', 'file-invoice-dollar'],
                    'Ingresos de hoy (Bs)' => ['success', 'cash-register'],
                    'Clientes registrados' => ['info', 'users'],
                    default => ['success', 'paw'],
                };
            @endphp
            <div class="col-sm-6 col-xl-3">
                <div class="small-box bg-{{ $color }} shadow-sm">
                    <div class="inner"><h3>{{ $valor }}</h3><p>{{ $titulo }}</p></div>
                    <div class="icon"><i class="fas fa-{{ $icono }}" aria-hidden="true"></i></div>
                    <span class="small-box-footer">Información actualizada</span>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="alert alert-info">No tienes indicadores operativos asignados. Utiliza los módulos habilitados en el menú.</div></div>
        @endforelse
    </div>
<div class="row">
@can('permiso.reservas')<div class="col-lg-8"><div class="card card-outline card-primary shadow-sm"><div class="card-header">Reservas de esta semana</div><div class="card-body"><div id="reservasChart" style="min-height: 300px;"></div></div></div></div>@endcan
@can('permiso.mascotas')<div class="col-lg-4"><div class="card card-outline card-success shadow-sm"><div class="card-header">Pacientes por especie</div><div class="card-body">@if($especies->isNotEmpty())<div id="especiesChart" style="min-height: 300px;"></div>@else<p class="text-muted">Sin pacientes registrados.</p>@endif</div></div></div>@endcan
</div>
    <div class="row">
        @can('permiso.reservas')
        <div class="col-lg-7">
    @can('permiso.reservas')
<div class="card card-outline card-info shadow-sm"><div class="card-header"><h2 class="card-title">Agenda: próximas reservas</h2><a class="float-right" href="{{ route('reserva.index') }}">Ver todos</a></div><div class="card-body">
@if($upcomingReservations->isEmpty())<p class="text-muted">No hay registros pendientes para mostrar.</p>@else
<div class="table-responsive"><table class="table table-hover"><thead><tr><th>Fecha y hora</th><th>Mascota / Responsable</th><th>Estado</th><th>Acción</th></tr></thead><tbody>
@foreach($upcomingReservations as $reserva)
<tr><td>{{ \Illuminate\Support\Carbon::parse($reserva->fecha)->format('d/m/Y') }} {{ $reserva->hora_inicio }}</td>
<td>{{ $reserva->mascota?->nombre ?? 'Sin mascota' }}<br><small>{{ $reserva->mascota?->cliente?->nombre }} {{ $reserva->mascota?->cliente?->apellido }}</small></td><td>{{ $reserva->estado }}</td>
<td><a href="{{ route('reserva.show', $reserva) }}" class="btn btn-sm btn-outline-secondary">Ver reserva</a>
@can('permiso.consultas')<a href="{{ $reserva->consulta ? route('consulta.show', $reserva->consulta) : route('consulta.create', ['reserva_id' => $reserva->id]) }}" class="btn btn-sm btn-primary">{{ $reserva->consulta ? 'Ver atención' : 'Atender' }}</a>@endcan</td></tr>
@endforeach
</tbody></table></div>@endif
</div></div>
@endcan
        </div>
        @endcan
        <div class="{{ auth()->user()->can('permiso.reservas') ? 'col-lg-5' : 'col-12' }}">
            <div class="card card-outline card-warning shadow-sm">
                <div class="card-header"><h2 class="card-title font-weight-bold"><i class="fas fa-bolt mr-2"></i>Estado operativo</h2></div>
                <div class="card-body">
                    @forelse ($estadisticas as $titulo => $valor)
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                            <span>{{ $titulo }}</span><span class="badge badge-primary badge-pill">{{ $valor }}</span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No tienes indicadores operativos asignados.</p>
                    @endforelse
                </div>
                <div class="card-footer bg-white">
    <div class="mb-3 d-flex flex-wrap">
        @can('permiso.reservas')<a class="btn btn-primary mr-2 mb-2" href="{{ route('reserva.create') }}">Nueva reserva</a>@endcan
        @can('permiso.clientes')<a class="btn btn-outline-secondary mr-2 mb-2" href="{{ route('clientes.index') }}">Clientes</a>@endcan
        @can('permiso.mascotas')<a class="btn btn-outline-secondary mr-2 mb-2" href="{{ route('mascota.index') }}">Fichas de mascotas</a>@endcan
        @can('permiso.pagos')<a class="btn btn-outline-success mr-2 mb-2" href="{{ route('pagos.index', ['estado' => 'Pendiente']) }}">Caja: pendientes</a>@endcan
        @can('permiso.reportes.ingresos')<a class="btn btn-outline-success mr-2 mb-2" href="{{ route('reportes.show', 'ingresos') }}">Reporte de ingresos</a>@endcan
    </div>
                </div>
            </div>
        </div>
    </div>
    @can('permiso.consultas')
<div class="card card-outline card-info shadow-sm"><div class="card-header"><h2 class="card-title">Consultas en atención</h2><a class="float-right" href="{{ route('consulta.index') }}">Ver todos</a></div><div class="card-body">
@if($recentConsultas->isEmpty())<p class="text-muted">No hay registros pendientes para mostrar.</p>@else
<div class="table-responsive"><table class="table table-hover"><thead><tr><th>Fecha</th><th>Mascota</th><th>Veterinario</th><th>Acción</th></tr></thead><tbody>
@foreach($recentConsultas as $consulta)
<tr><td>{{ $consulta->fecha?->format('d/m/Y') }}</td><td>{{ $consulta->reserva?->mascota?->nombre ?? 'Sin mascota' }}</td><td>{{ $consulta->veterinario?->nombre }} {{ $consulta->veterinario?->apellido }}</td><td><a class="btn btn-sm btn-primary" href="{{ route(auth()->user()->can('update', $consulta) ? 'consulta.edit' : 'consulta.show', $consulta) }}">Continuar atención</a></td></tr>
@endforeach
</tbody></table></div>@endif
</div></div>
@endcan
    @can('permiso.pagos')
<div class="card card-outline card-info shadow-sm"><div class="card-header"><h2 class="card-title">Caja: cuentas pendientes</h2><a class="float-right" href="{{ route('pagos.index') }}">Ver todos</a></div><div class="card-body">
@if($pagosPendientes->isEmpty())<p class="text-muted">No hay registros pendientes para mostrar.</p>@else
<div class="table-responsive"><table class="table table-hover"><thead><tr><th>Cuenta</th><th>Mascota / Responsable</th><th>Monto</th><th>Acción</th></tr></thead><tbody>
@foreach($pagosPendientes as $pago)
<tr><td>#{{ $pago->id }}</td><td>{{ $pago->consulta?->reserva?->mascota?->nombre ?? 'Sin mascota' }}<br><small>{{ $pago->consulta?->reserva?->mascota?->cliente?->nombre }} {{ $pago->consulta?->reserva?->mascota?->cliente?->apellido }}</small></td><td>Bs {{ number_format((float) $pago->monto, 2) }}</td><td><a class="btn btn-sm btn-success" href="{{ route('pagos.show', $pago) }}">Revisar y cobrar</a></td></tr>
@endforeach
</tbody></table></div>@endif
</div></div>
@endcan
@endsection
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const reservasElement = document.querySelector('#reservasChart');
            if (reservasElement) {
                new ApexCharts(reservasElement, {
                    chart: { type: 'area', height: 300, toolbar: { show: false }, zoom: { enabled: false } },
                    series: [{ name: 'Reservas', data: @json($weeklySeries) }],
                    xaxis: { categories: @json($weeklyLabels) },
                    stroke: { curve: 'smooth', width: 3 },
                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 90, 100] } },
                    tooltip: { y: { formatter: value => `${value} reserva(s)` } }
                }).render();
            }

            const especiesElement = document.querySelector('#especiesChart');
            if (especiesElement) {
                new ApexCharts(especiesElement, {
                    chart: { type: 'donut', height: 300, toolbar: { show: false } },
                    series: @json($especies->pluck('mascotas_count')->values()->all()),
                    labels: @json($especies->pluck('nombre')->values()->all()),
                    legend: { position: 'bottom' },
                    tooltip: { y: { formatter: value => `${value} mascota(s)` } }
                }).render();
            }
        });
    </script>
@endpush
