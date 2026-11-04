@extends('adminlte::page')

@section('title', 'Nueva Reserva')

@section('content_header')
    <h1>Nueva Reserva</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Registrar reserva</h3></div>
    <div class="card-body bg-white">
        @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button></div>@endif
        @if(session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button></div>@endif
        <form method="POST" action="{{ route('reserva.store') }}" novalidate>
            @csrf
            @include('reserva.form')
        </form>
    </div>
</div>
@stop
