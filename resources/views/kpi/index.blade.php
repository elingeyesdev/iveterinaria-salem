@extends('adminlte::page')

@section('title', 'Indicadores KPI | Salem')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap">
    <div><h1 class="font-weight-bold mb-1">Indicadores KPI</h1><p class="text-muted mb-0">Desempeño operativo y financiero · Salem Centro Veterinario</p></div>
    <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Reportes</a>
</div>
@stop

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('reportes.kpi.index') }}" class="form-row align-items-end">
            <div class="form-group col-md-4"><label for="desde">Desde</label><input id="desde" name="desde" type="date" class="form-control @error('desde') is-invalid @enderror" value="{{ old('desde', $desde) }}" required>@error('desde')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="form-group col-md-4"><label for="hasta">Hasta</label><input id="hasta" name="hasta" type="date" class="form-control @error('hasta') is-invalid @enderror" value="{{ old('hasta', $hasta) }}" required>@error('hasta')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="form-group col-md-4"><button class="btn btn-primary btn-block"><i class="fas fa-filter mr-1"></i> Aplicar filtros</button></div>
        </form>
    </div>
</div>
<div class="row">
@foreach ([
    ['Reservas registradas', number_format($kpi['reservas'], 0, ',', '.'), 'fa-calendar-alt', 'info', 'Reservas con fecha dentro del período'],
    ['Asistencia', $kpi['asistencia'] === null ? 'N/D' : number_format($kpi['asistencia'], 1, ',', '.').'%', 'fa-user-check', 'success', 'Atendidas / (Atendidas + No asistió)'],
    ['Consultas', number_format($kpi['consultas'], 0, ',', '.'), 'fa-stethoscope', 'primary', 'Consultas con fecha dentro del período'],
    ['Ingresos cobrados', 'Bs '.number_format($kpi['ingresos'], 2, ',', '.'), 'fa-wallet', 'warning', 'Pagos Pagado según fecha de registro'],
    ['Servicios prestados', number_format($kpi['servicios'], 0, ',', '.'), 'fa-briefcase-medical', 'secondary', 'Servicios asociados a consultas del período'],
    ['Consultas finalizadas', number_format($kpi['finalizadas'], 0, ',', '.'), 'fa-check-circle', 'success', 'Consultas con estado clínico Finalizada'],
] as [$titulo, $valor, $icono, $color, $descripcion])
<div class="col-md-6 col-xl-4"><div class="small-box bg-{{ $color }}"><div class="inner"><h3 style="font-size:1.8rem">{{ $valor }}</h3><p class="mb-0">{{ $titulo }}</p></div><div class="icon"><i class="fas {{ $icono }}"></i></div><div class="small-box-footer text-left px-3 py-2" style="font-size:.8rem">{{ $descripcion }}</div></div></div>
@endforeach
</div>
<div class="row">
    <div class="col-lg-5"><div class="card card-outline card-warning shadow-sm"><div class="card-header"><h3 class="card-title">Estado de las reservas</h3></div><div class="card-body"><table class="table table-sm table-striped"><thead><tr><th>Estado</th><th class="text-right">Cantidad</th></tr></thead><tbody>@forelse($kpi['estados'] as $estado => $cantidad)<tr><td>{{ $estado }}</td><td class="text-right">{{ number_format($cantidad, 0, ',', '.') }}</td></tr>@empty<tr><td colspan="2" class="text-muted">Sin registros para este período.</td></tr>@endforelse</tbody></table><p class="text-muted small mt-3 mb-0">Canceladas: {{ $kpi['canceladas'] }} · No asistió: {{ $kpi['no_asistio'] }}</p></div></div></div>
    <div class="col-lg-7"><div class="card card-outline card-warning shadow-sm"><div class="card-header"><h3 class="card-title">Evolución mensual de reservas</h3></div><div class="card-body"><canvas id="kpi-reservas-chart" height="170" aria-label="Gráfico de reservas por mes" role="img"></canvas><div id="kpi-chart-fallback" class="text-muted small d-none">El gráfico no está disponible. Los datos se muestran a continuación.</div><table class="table table-sm mt-3"><thead><tr><th>Mes</th><th class="text-right">Reservas</th></tr></thead><tbody>@forelse($kpi['meses'] as $mes)<tr><td>{{ $mes['etiqueta'] }}</td><td class="text-right">{{ $mes['total'] }}</td></tr>@empty<tr><td colspan="2" class="text-muted">Sin registros.</td></tr>@endforelse</tbody></table></div></div></div>
</div>
<div class="alert alert-light border small"><strong>Metodología:</strong> los KPI operativos utilizan fechas de reservas y consultas; los ingresos utilizan la fecha de registro de pagos con estado Pagado. Los pagos anulados y pendientes quedan excluidos. La tasa de asistencia omite reservas pendientes y canceladas. Cuando no existen reservas concluidas se muestra N/D. Las cifras reflejan el estado actual de los registros, no una reconstrucción histórica de cambios de estado.</div>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const meses = @json(array_column($kpi['meses'], 'etiqueta'));
    const valores = @json(array_column($kpi['meses'], 'total'));
    if (typeof Chart === 'undefined') { document.getElementById('kpi-chart-fallback').classList.remove('d-none'); return; }
    new Chart(document.getElementById('kpi-reservas-chart').getContext('2d'), {
        type: 'line',
        data: { labels: meses, datasets: [{ label: 'Reservas', data: valores, borderColor: '#ad8747', backgroundColor: 'rgba(173,135,71,.12)', fill: true, tension: .2 }] },
        options: { responsive: true, legend: { display: false }, scales: { yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }] } }
    });
});
</script>
@stop
