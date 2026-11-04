@extends('adminlte::page')

@section('title', $def['titulo'])

@section('css')
    <link rel="stylesheet" href="{{ asset('reportes-salem.css') }}">
@stop

@section('content_header')
    <div class="salem-cabecera">
        @include('partials.salem-marca')
        <div class="salem-contexto">Informaci?n financiera y operativa<br>Centro de reportes</div>
    </div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div>
            <h1 class="mb-1 font-weight-bold"><i class="{{ $def['icono'] }} mr-2 text-primary"></i>{{ $def['titulo'] }}</h1>
            <p class="text-muted mb-0">
                {{ $filtros->desde->format('d/m/Y') }} — {{ $filtros->hasta->format('d/m/Y') }}
                <span class="mx-1">·</span>{{ $filtros->dias() }} {{ $filtros->dias() === 1 ? 'día' : 'días' }}
            </p>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="{{ route('reportes.csv', array_merge(['tipo' => $tipo], $filtros->toQuery())) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel mr-1"></i> Exportar CSV
            </a>
            <a href="{{ route('reportes.imprimir', array_merge(['tipo' => $tipo], $filtros->toQuery())) }}" target="_blank" rel="noopener" class="btn btn-outline-dark btn-sm">
                <i class="fas fa-print mr-1"></i> Imprimir / PDF
            </a>
            <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-th-large mr-1"></i> Todos los reportes
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="salem-reportes">
    @include('reportes._filtros')

    <div class="salem-metodologia mb-3"><i class="fas fa-shield-alt mr-2"></i><strong>Información verificable:</strong> cifras calculadas con los registros del período seleccionado. Las definiciones y limitaciones se documentan al final del reporte.</div>
    {{-- KPIs --}}
    <div class="row">
        @foreach ($datos['kpis'] as $kpi)
            <div class="col-sm-6 col-lg">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-{{ $kpi['color'] }}"><i class="{{ $kpi['icono'] }}"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ $kpi['label'] }}</span>
                        <span class="info-box-number" style="font-size:1.15rem;">
                            {{ \App\Support\FormatoReporte::valor($kpi['valor'], $kpi['formato']) }}
                        </span>
                        @if ($kpi['detalle'])
                            <span class="text-muted small text-truncate" title="{{ $kpi['detalle'] }}">{{ $kpi['detalle'] }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Gráficos --}}
    @php($hayDatos = count($datos['filas']) > 0)
    @if ($hayDatos)
        <div class="row">
            <div class="{{ $datos['grafico2'] ? 'col-lg-8' : 'col-12' }}">
                <div class="card shadow-sm">
                    <div class="card-header bg-white"><h3 class="card-title font-weight-bold">{{ $datos['grafico']['titulo'] }}</h3></div>
                    <div class="card-body"><div id="grafico1" style="min-height:300px;"></div></div>
                </div>
            </div>
            @if ($datos['grafico2'])
                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white"><h3 class="card-title font-weight-bold">{{ $datos['grafico2']['titulo'] }}</h3></div>
                        <div class="card-body"><div id="grafico2" style="min-height:300px;"></div></div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Detalle --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold mb-0">Detalle y trazabilidad</h3>
            <span class="text-muted small">{{ $filas->total() }} {{ $filas->total() === 1 ? 'fila' : 'filas' }}</span>
        </div>
        <div class="card-body p-0">
            @if (! $hayDatos)
                <div class="text-center text-muted py-5">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p class="mb-0">No hay datos para los filtros seleccionados.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm mb-0">
                        <thead class="thead-dark">
                            <tr>
                                @foreach ($datos['columnas'] as $col)
                                    <th scope="col" class="{{ $col['tipo'] === 'texto' ? '' : 'text-right' }}">{{ $col['label'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($filas as $fila)
                                <tr>
                                    @foreach ($datos['columnas'] as $col)
                                        <td class="{{ $col['tipo'] === 'texto' ? '' : 'text-right' }}">{{ \App\Support\FormatoReporte::valor($fila[$col['key']] ?? null, $col['tipo']) }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                        @if (! empty($datos['totales']))
                            <tfoot class="font-weight-bold bg-light">
                                <tr>
                                    @foreach ($datos['columnas'] as $col)
                                        <td class="{{ $col['tipo'] === 'texto' ? '' : 'text-right' }}">{{ ($datos['totales'][$col['key']] ?? null) === null ? '' : \App\Support\FormatoReporte::valor($datos['totales'][$col['key']], $col['tipo']) }}</td>
                                    @endforeach
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            @endif
        </div>
        @if ($filas->hasPages())
            <div class="card-footer bg-white">{{ $filas->links() }}</div>
        @endif
    </div>

    <h6 class="font-weight-bold mt-3">Criterios de interpretación y fuentes</h6>
    @foreach ($datos['notas'] as $nota)
        <p class="text-muted small mb-1"><i class="fas fa-info-circle mr-1"></i>{{ $nota }}</p>
    @endforeach
    </div>
@stop

@section('js')
    @if ($hayDatos)
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            (function () {
                const numero = (v, dec) => Number(v).toLocaleString('es-BO', { minimumFractionDigits: dec, maximumFractionDigits: dec });
                const formato = (tipo) => (v) => tipo === 'moneda' ? 'Bs ' + numero(v, 2)
                    : tipo === 'porcentaje' ? numero(v, 1) + '%' : numero(v, 0);

                function opciones(cfg) {
                    const fmt = formato(cfg.formato);
                    if (cfg.tipo === 'donut') {
                        return {
                            chart: { type: 'donut', height: 300 },
                            colors: ['#b68b2d', '#343637', '#728b7d', '#a97664', '#8e849d'],
                            series: cfg.series[0].data,
                            labels: cfg.labels,
                            legend: { position: 'bottom' },
                            tooltip: { y: { formatter: fmt } },
                            noData: { text: 'Sin datos' },
                        };
                    }
                    return {
                        chart: { type: cfg.tipo, height: 300, stacked: !!cfg.apilado, toolbar: { show: false }, zoom: { enabled: false } },
                        colors: ['#b68b2d', '#343637', '#728b7d', '#a97664'],
                        series: cfg.series,
                        xaxis: { categories: cfg.labels, labels: { rotate: -45, hideOverlappingLabels: true } },
                        yaxis: { labels: { formatter: fmt } },
                        dataLabels: { enabled: false },
                        stroke: cfg.tipo === 'area' ? { curve: 'smooth', width: 2 } : { width: 0 },
                        plotOptions: { bar: { horizontal: !!cfg.horizontal, borderRadius: 3, columnWidth: '55%' } },
                        tooltip: { y: { formatter: fmt } },
                        legend: { show: cfg.series.length > 1, position: 'top' },
                        noData: { text: 'Sin datos en el período' },
                    };
                }

                const g1 = @json($datos['grafico']);
                const g2 = @json($datos['grafico2']);
                const el1 = document.querySelector('#grafico1');
                const el2 = document.querySelector('#grafico2');
                if (el1 && g1) new ApexCharts(el1, opciones(g1)).render();
                if (el2 && g2) new ApexCharts(el2, opciones(g2)).render();
            })();
        </script>
    @endif
@stop
