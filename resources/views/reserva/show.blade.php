@extends('adminlte::page')

@section('title', 'Detalle de Reserva')

@section('content_header')
    <h1>Detalle de Reserva</h1>
@stop

@section('content')
@php $cliente = $reserva->mascota?->cliente; @endphp
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Reserva #{{ $reserva->id }}</h3>
        <a class="btn btn-primary btn-sm" href="{{ route('reserva.index') }}">Regresar</a>
    </div>
    <div class="card-body bg-white">
        <div class="row">
            <div class="col-md-6">
                <h5>Cliente</h5>
                <p><strong>Nombre:</strong> {{ trim(($cliente?->nombre ?? '') . ' ' . ($cliente?->apellido ?? '')) ?: 'Sin cliente' }}</p>
                <p><strong>Correo:</strong> {{ $cliente?->correo ?? '—' }}</p>
                <p><strong>Teléfono:</strong> {{ $cliente?->telefono ?? '—' }}</p>
            </div>
            <div class="col-md-6">
                <h5>Mascota</h5>
                <p><strong>Nombre:</strong> {{ $reserva->mascota?->nombre ?? '—' }}</p>
                <p><strong>Especie:</strong> {{ $reserva->mascota?->especie?->nombre ?? '—' }}</p>
                <p><strong>Raza:</strong> {{ $reserva->mascota?->raza?->nombre ?? '—' }}</p>
            </div>
        </div>
        @can('permiso.consultas')
            @if($reserva->consulta)
                <a href="{{ route('consulta.show', $reserva->consulta) }}" class="btn btn-primary">Ver atención</a>
            @elseif($reserva->estado === \App\Support\ReservaEstado::PENDIENTE)
                <a href="{{ route('consulta.create', ['reserva_id' => $reserva->id]) }}" class="btn btn-primary">Atender</a>
            @endif
        @endcan
        @can('permiso.mascotas')
            @if($reserva->mascota)
                <a href="{{ route('mascota.show', $reserva->mascota) }}" class="btn btn-outline-secondary">Ver ficha de mascota</a>
            @endif
        @endcan
        <hr>
        <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}</p>
        <p><strong>Horario:</strong> {{ $reserva->hora_inicio }} - {{ $reserva->hora_fin }}</p>
        <p><strong>Estado:</strong> {{ $reserva->estado }}</p>
    </div>
</div>
@stop
