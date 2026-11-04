@extends('adminlte::page')

@section('title', 'Reportes')

@section('css')
    <link rel="stylesheet" href="{{ asset('reportes-salem.css') }}">
@stop

@section('content_header')
    <div class="salem-cabecera">
        @include('partials.salem-marca')
        <div class="salem-contexto">Información financiera y operativa<br>Análisis gerencial y operativo</div>
    </div>
    <div>
        <h1 class="mb-1 font-weight-bold">Reportes</h1>
        <p class="text-muted mb-0">Informes trazables con indicadores, gráficos, filtros por período y exportación CSV o impresión.</p>
    </div>
@stop

@section('content')
    <div class="salem-reportes">
    <div class="row">
        @foreach ($reportes as $slug => $def)
            <div class="col-md-6 col-xl-4 d-flex">
                <div class="card card-outline card-primary shadow-sm w-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-2">
                            <span class="d-inline-flex align-items-center justify-content-center rounded bg-primary text-white mr-3" style="width:42px;height:42px;">
                                <i class="{{ $def['icono'] }}"></i>
                            </span>
                            <h5 class="mb-0 font-weight-bold">{{ $def['titulo'] }}</h5>
                        </div>
                        <p class="text-muted flex-grow-1">{{ $def['descripcion'] }}</p>
                        <a href="{{ route('reportes.show', $slug) }}" class="btn btn-primary btn-sm align-self-start">
                            Abrir reporte <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    </div>
@stop
