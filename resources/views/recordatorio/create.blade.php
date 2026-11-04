@extends('adminlte::page')

@section('title', 'Nuevo Recordatorio')

@section('content_header')
    <h1>Nuevo Recordatorio</h1>
@stop

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <form method="POST" action="{{ route('recordatorio.store') }}" novalidate>
                    @csrf
                    @include('recordatorio.form')
                </form>
            </div>
        </div>
    </section>
@stop
