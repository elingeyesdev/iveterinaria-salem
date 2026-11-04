@extends('adminlte::page')

@section('title', 'Detalle del Recordatorio')

@section('content_header')
    <h1>Detalle del Recordatorio</h1>
@stop

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-dark text-white">
            <h3 class="card-title mb-0">
                <i class="fas fa-bell mr-2"></i>{{ $recordatorio->titulo }}
            </h3>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-5">Mascota</dt>
                        <dd class="col-sm-7">{{ $recordatorio->mascota?->nombre ?? '—' }}</dd>

                        <dt class="col-sm-5">Propietario</dt>
                        <dd class="col-sm-7">
                            {{ $recordatorio->mascota?->cliente?->nombre ?? '' }}
                            {{ $recordatorio->mascota?->cliente?->apellido ?? '' }}
                        </dd>

                        <dt class="col-sm-5">Tipo</dt>
                        <dd class="col-sm-7">{{ $recordatorio->tipo }}</dd>
                    </dl>
                </div>

                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-5">Fecha</dt>
                        <dd class="col-sm-7">{{ $recordatorio->fecha->format('d/m/Y') }}</dd>

                        <dt class="col-sm-5">Estado</dt>
                        <dd class="col-sm-7">
                            @if ($recordatorio->estado === 'Pendiente')
                                <span class="badge badge-warning">Pendiente</span>
                            @elseif ($recordatorio->estado === 'Completado')
                                <span class="badge badge-success">Completado</span>
                            @else
                                <span class="badge badge-danger">Cancelado</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            <hr>

            <h6><i class="fas fa-align-left mr-1"></i> Descripción</h6>
            <p class="mb-0">{{ $recordatorio->descripcion ?: 'Sin descripción.' }}</p>
        </div>

        <div class="card-footer bg-white">
            <a href="{{ route('recordatorio.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Regresar
            </a>
            <a href="{{ route('recordatorio.edit', $recordatorio) }}" class="btn btn-warning">
                <i class="fas fa-edit mr-1"></i> Editar
            </a>
        </div>
    </div>
@stop
