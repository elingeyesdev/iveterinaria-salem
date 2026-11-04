@extends('adminlte::page')
@section('title', 'Ficha de ' . $mascotum->nombre)
@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-center">
        <h1>Ficha de {{ $mascotum->nombre }}</h1>
        <a href="{{ route('mascota.index') }}" class="btn btn-outline-secondary">Volver a mascotas</a>
    </div>
@endsection
@section('content')
    <div class="card card-outline card-primary"><div class="card-body d-flex flex-wrap align-items-center">
        @if($mascotum->imagen)
            <img src="{{ Storage::url($mascotum->imagen) }}" alt="Foto de {{ $mascotum->nombre }}" width="96" class="rounded mr-3 mb-2">
        @else
            <i class="fas fa-paw fa-3x text-muted mr-3" aria-hidden="true"></i>
        @endif
        <div class="mr-auto">
            <h2 class="h4">{{ $mascotum->nombre }}</h2>
            <p class="text-muted">{{ $mascotum->especie?->nombre ?? 'Especie sin registrar' }} / {{ $mascotum->raza?->nombre ?? 'Raza sin registrar' }} / {{ $mascotum->sexo ?? 'Sexo sin registrar' }}</p>
            <p class="mb-0">Responsable: {{ trim(($mascotum->cliente?->nombre ?? '') . ' ' . ($mascotum->cliente?->apellido ?? '')) ?: 'Sin registrar' }}</p>
        </div>
        <span class="badge {{ $mascotum->estado ? 'badge-success' : 'badge-secondary' }} p-2">{{ $mascotum->estado ? 'Activo' : 'Inactivo' }}</span>
    </div></div>
    <nav aria-label="Secciones de la ficha" class="mb-3"><ul class="nav nav-pills flex-wrap">
        @foreach($secciones as $clave => $titulo)
            <li class="nav-item"><a href="{{ route('mascota.show', [$mascotum, 'seccion' => $clave]) }}" class="nav-link {{ $seccion === $clave ? 'active' : '' }}" @if($seccion === $clave) aria-current="page" @endif>{{ $titulo }}</a></li>
        @endforeach
    </ul></nav>
    <div class="card">
        <div class="card-header"><h2 class="card-title">{{ $secciones[$seccion] }}</h2></div>
        <div class="card-body">
        @if($seccion === 'datos')
            <dl class="row">
                <dt class="col-sm-4">Número de ficha</dt><dd class="col-sm-8">#{{ $mascotum->id }}</dd>
                <dt class="col-sm-4">Fecha de nacimiento</dt><dd class="col-sm-8">{{ $mascotum->fecha_nac ? \Illuminate\Support\Carbon::parse($mascotum->fecha_nac)->format('d/m/Y') : 'Sin registrar' }}</dd>
                <dt class="col-sm-4">Peso registrado</dt><dd class="col-sm-8">{{ $mascotum->peso !== null ? number_format((float) $mascotum->peso, 2) . ' kg' : 'Sin registrar' }}</dd>
            </dl>
            <a href="{{ route('mascota.edit', $mascotum) }}" class="btn btn-outline-primary">Editar datos</a>
            @can('permiso.clientes')
                @if($mascotum->cliente)
                    <a href="{{ route('clientes.show', $mascotum->cliente) }}" class="btn btn-outline-secondary">Ver responsable</a>
                @endif
            @endcan
        @elseif($registros->isEmpty())
            <p class="text-muted">No hay registros en esta sección para esta mascota.</p>
        @else
            <div class="table-responsive">
                @if($seccion === 'historial')
                    <table class="table table-hover"><thead><tr><th>Fecha</th><th>Atención</th><th>Veterinario</th><th>Estado</th><th>Detalle clínico</th></tr></thead><tbody>
                        @foreach($registros as $consulta)
                        <tr><td>{{ $consulta->fecha?->format('d/m/Y') }}</td><td><a href="{{ route('consulta.show', $consulta) }}">Consulta #{{ $consulta->id }}</a></td>
                        <td>{{ trim(($consulta->veterinario?->nombre ?? '') . ' ' . ($consulta->veterinario?->apellido ?? '')) ?: 'Sin registrar' }}</td><td>{{ $consulta->estado_clinico }}</td>
                        <td><details><summary>Ver motivo, diagnóstico y tratamiento</summary>
                        <p class="mt-2"><strong>Motivo:</strong> {{ $consulta->motivo ?: 'Sin registrar' }}</p>
                        <p><strong>Diagnóstico:</strong> {{ $consulta->diagnostico ?: 'Sin registrar' }}</p>
                        <p><strong>Tratamiento:</strong> {{ $consulta->tratamiento ?: 'Sin registrar' }}</p></details></td></tr>
                        @endforeach
                    </tbody></table>
                @elseif($seccion === 'servicios')
                    <table class="table table-hover"><thead><tr><th>Consulta</th><th>Servicio</th><th>Fecha de aplicación</th><th>Próxima fecha registrada</th></tr></thead><tbody>
                        @foreach($registros as $consulta)
                        @foreach($consulta->servicios as $servicio)
                        <tr><td><a href="{{ route('consulta.show', $consulta) }}">#{{ $consulta->id }}</a></td><td>{{ $servicio->nombre }}</td>
                        <td>{{ $servicio->pivot->fecha_actual ? \Illuminate\Support\Carbon::parse($servicio->pivot->fecha_actual)->format('d/m/Y') : ($consulta->fecha?->format('d/m/Y') ?? 'Sin registrar') }}</td>
                        <td>{{ $servicio->pivot->fecha_prox ? \Illuminate\Support\Carbon::parse($servicio->pivot->fecha_prox)->format('d/m/Y') : 'Sin registrar' }}</td></tr>
                        @endforeach
                        @endforeach
                    </tbody></table>
                    <p class="text-muted small">Los servicios se agrupan por consulta. Una próxima fecha registrada no confirma una reserva.</p>
                @elseif($seccion === 'peso')
                    <table class="table table-hover"><thead><tr><th>Fecha</th><th>Peso registrado en consulta</th><th>Consulta</th></tr></thead><tbody>
                        @foreach($registros as $consulta)
                        <tr><td>{{ $consulta->fecha?->format('d/m/Y') }}</td><td>{{ number_format((float) $consulta->peso, 2) }} kg</td><td><a href="{{ route('consulta.show', $consulta) }}">#{{ $consulta->id }}</a></td></tr>
                        @endforeach
                    </tbody></table>
                @elseif($seccion === 'reservas')
                    <table class="table table-hover"><thead><tr><th>Fecha</th><th>Horario</th><th>Estado</th><th>Acción</th></tr></thead><tbody>
                        @foreach($registros as $reserva)
                        <tr><td>{{ \Illuminate\Support\Carbon::parse($reserva->fecha)->format('d/m/Y') }}</td><td>{{ $reserva->hora_inicio }} - {{ $reserva->hora_fin }}</td><td>{{ $reserva->estado }}</td><td><a href="{{ route('reserva.show', $reserva) }}">Ver reserva #{{ $reserva->id }}</a></td></tr>
                        @endforeach
                    </tbody></table>
                @elseif($seccion === 'pagos')
                    <table class="table table-hover"><thead><tr><th>Fecha de registro</th><th>Consulta</th><th>Monto</th><th>Estado</th><th>Acción</th></tr></thead><tbody>
                        @foreach($registros as $pago)
                        <tr><td>{{ $pago->created_at?->format('d/m/Y H:i') ?? 'Sin registrar' }}</td><td>#{{ $pago->consulta_id }}</td><td>Bs {{ number_format((float) $pago->monto, 2) }}</td><td>{{ $pago->estado }}</td><td><a href="{{ route('pagos.show', $pago) }}">{{ $pago->estado === \App\Models\Pago::PENDIENTE ? 'Pagar' : 'Ver cuenta' }}</a></td></tr>
                        @endforeach
                    </tbody></table>
                @endif
            </div>
        @endif
        @if($registros)
            <div class="d-flex justify-content-end">{{ $registros->links('vendor.pagination.custom') }}</div>
        @endif
        </div>
    </div>
@endsection
