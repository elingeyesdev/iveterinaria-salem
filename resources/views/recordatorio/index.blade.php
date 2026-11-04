@extends('adminlte::page')

@section('title', 'Recordatorios')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">Recordatorios</h1>
        <a href="{{ route('recordatorio.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle mr-1"></i> Nuevo recordatorio
        </a>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($proximos->count())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-warning">
                <h3 class="card-title mb-0">
                    <i class="fas fa-bell mr-2"></i> Próximos 7 días
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Mascota</th>
                                <th>Recordatorio</th>
                                <th>Fecha</th>
                                <th>Faltan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($proximos as $proximo)
                                @php
                                    $dias = now()->startOfDay()->diffInDays($proximo->fecha->startOfDay(), false);
                                @endphp
                                <tr>
                                    <td><strong>{{ $proximo->mascota?->nombre ?? '—' }}</strong></td>
                                    <td>{{ $proximo->titulo }}</td>
                                    <td>{{ $proximo->fecha->format('d/m/Y') }}</td>
                                    <td>
                                        @if ($dias === 0)
                                            <span class="badge badge-danger">Hoy</span>
                                        @elseif ($dias === 1)
                                            <span class="badge badge-warning">Mañana</span>
                                        @else
                                            <span class="badge badge-info">{{ $dias }} días</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <form action="{{ route('recordatorio.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label for="search">Buscar</label>
                        <input type="text" id="search" name="search" class="form-control"
                               value="{{ request('search') }}" placeholder="Mascota, propietario o título...">
                    </div>
                    <div class="col-md-3">
                        <label for="tipo">Tipo</label>
                        <select name="tipo" id="tipo" class="form-control">
                            <option value="">Todos</option>
                            @foreach (['Vacunación', 'Desparasitación', 'Control veterinario', 'Baño/Peluquería', 'Limpieza dental', 'Otro'] as $tipo)
                                <option value="{{ $tipo }}" @selected(request('tipo') === $tipo)>{{ $tipo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="estado">Estado</label>
                        <select name="estado" id="estado" class="form-control">
                            <option value="">Todos</option>
                            @foreach (['Pendiente', 'Completado', 'Cancelado'] as $estado)
                                <option value="{{ $estado }}" @selected(request('estado') === $estado)>{{ $estado }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search mr-1"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Mascota</th>
                            <th>Propietario</th>
                            <th>Tipo</th>
                            <th>Título</th>
                            <th>Fecha</th>
                            <th>Correo</th>
                            <th>Estado</th>
                            <th width="145">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recordatorios as $recordatorio)
                            <tr>
                                <td><strong>{{ $recordatorio->mascota?->nombre ?? '—' }}</strong></td>
                                <td>
                                    {{ $recordatorio->mascota?->cliente?->nombre ?? '' }}
                                    {{ $recordatorio->mascota?->cliente?->apellido ?? '' }}
                                </td>
                                <td>{{ $recordatorio->tipo }}</td>
                                <td>{{ $recordatorio->titulo }}</td>
                                <td>{{ $recordatorio->fecha->format('d/m/Y') }}</td>
                                <td>
                                    @if ($recordatorio->reserva_id)
                                        @if ($recordatorio->enviado_at)
                                            <span class="badge badge-success">Enviado</span>
                                        @elseif ($recordatorio->error_envio)
                                            <span class="badge badge-danger" title="{{ $recordatorio->error_envio }}">Error</span>
                                        @else
                                            <span class="badge badge-warning">Programado</span>
                                        @endif
                                    @else
                                        <span class="text-muted">Manual</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($recordatorio->estado === 'Pendiente')
                                        <span class="badge badge-warning">Pendiente</span>
                                    @elseif ($recordatorio->estado === 'Completado')
                                        <span class="badge badge-success">Completado</span>
                                    @else
                                        <span class="badge badge-danger">Cancelado</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('recordatorio.show', $recordatorio) }}" class="btn btn-sm btn-info" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('recordatorio.edit', $recordatorio) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('recordatorio.destroy', $recordatorio) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Eliminar este recordatorio?');">
                                        @csrf
                                        @method('DELETE')
                                        @can('eliminar-registros')
<button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
@endcan
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-bell-slash fa-2x mb-2 d-block"></i>
                                    No existen recordatorios registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $recordatorios->links() }}
        </div>
    </div>
@stop
