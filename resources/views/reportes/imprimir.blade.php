<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $def['titulo'] }} — Salem Centro Veterinario</title>
    <link rel="stylesheet" href="{{ asset('reportes-salem.css') }}">
</head>
<body class="salem-documento">
    <div class="toolbar">
        <button type="button" onclick="window.print()">Imprimir / Guardar como PDF</button>
        <a href="javascript:window.close()">Cerrar</a>
    </div>

    <main class="salem-hoja">
    <header>
        @include('partials.salem-marca')
        <div class="meta">
            Generado el {{ $generadoEn->format('d/m/Y H:i') }}<br>
            Por {{ $generadoPor }}
        </div>
    </header>
    <h1>{{ $def['titulo'] }}</h1>
    <p class="descripcion">{{ $def['descripcion'] }}</p>

    <div class="filtros">
        <strong>Período:</strong> {{ $filtros->desde->format('d/m/Y') }} al {{ $filtros->hasta->format('d/m/Y') }}
        @if ($veterinario) &nbsp;·&nbsp; <strong>Veterinario:</strong> {{ trim($veterinario->nombre . ' ' . $veterinario->apellido) }} @endif
        @if ($filtros->tipoPago) &nbsp;·&nbsp; <strong>Método de pago:</strong> {{ $filtros->tipoPago }} @endif
    </div>

    <div class="kpis">
        @foreach ($datos['kpis'] as $kpi)
            <div class="kpi">
                <div class="l">{{ $kpi['label'] }}</div>
                <div class="v">{{ \App\Support\FormatoReporte::valor($kpi['valor'], $kpi['formato']) }}</div>
                @if ($kpi['detalle'])<div class="d">{{ $kpi['detalle'] }}</div>@endif
            </div>
        @endforeach
    </div>

    <div class="detalle-titulo">
        <h2>Detalle del reporte</h2>
        <span>{{ count($datos['filas']) }} registros</span>
    </div>
    @if (count($datos['filas']) === 0)
        <p class="sin-datos">No hay datos para los filtros seleccionados.</p>
    @else
        <table>
            <thead>
                <tr>
                    @foreach ($datos['columnas'] as $col)
                        <th scope="col" class="{{ $col['tipo'] === 'texto' ? '' : 'r' }}">{{ $col['label'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($datos['filas'] as $fila)
                    <tr>
                        @foreach ($datos['columnas'] as $col)
                            <td class="{{ $col['tipo'] === 'texto' ? '' : 'r' }}">{{ \App\Support\FormatoReporte::valor($fila[$col['key']] ?? null, $col['tipo']) }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
            @if (! empty($datos['totales']))
                <tfoot>
                    <tr>
                        @foreach ($datos['columnas'] as $col)
                            <td class="{{ $col['tipo'] === 'texto' ? '' : 'r' }}">{{ ($datos['totales'][$col['key']] ?? null) === null ? '' : \App\Support\FormatoReporte::valor($datos['totales'][$col['key']], $col['tipo']) }}</td>
                        @endforeach
                    </tr>
                </tfoot>
            @endif
        </table>
    @endif

    <h2 class="metodologia-titulo">Metodología y notas de interpretación</h2>
    <div class="notas">
        @foreach ($datos['notas'] as $nota)<p>{{ $nota }}</p>@endforeach
    </div>

    <footer>
        <span>Salem Centro Veterinario — Documento generado automáticamente</span>
        <span>Confidencial: uso interno</span>
    </footer>
    </main>
</body>
</html>
