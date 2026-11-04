@extends('adminlte::page')

@section('title', 'Editar Reserva')

@section('content_header')
    <h1>Editar Reserva</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Modificar reserva #{{ $reserva->id }}</h3></div>
    <div class="card-body bg-white">
        @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
        <form method="POST" action="{{ route('reserva.update', $reserva) }}" novalidate>
            @csrf
            @method('PATCH')
            @include('reserva.form')
        </form>
    </div>
</div>
@stop
