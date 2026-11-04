@extends('adminlte::page')

@section('title', 'Editar Recordatorio')

@section('content_header')
    <h1>Editar Recordatorio</h1>
@stop

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <form method="POST" action="{{ route('recordatorio.update', $recordatorio) }}" novalidate>
                    @csrf
                    @method('PATCH')
                    @include('recordatorio.form')
                </form>
            </div>
        </div>
    </section>
@stop
