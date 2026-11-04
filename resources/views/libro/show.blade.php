@extends('layouts.app')

@section('template_title')
    {{ $libro->name ?? __('Show') . " " . __('Libro') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Libro</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('libros.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Nombre:</strong>
                                    {{ $libro->nombre }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Autor:</strong>
                                    {{ $libro->autor }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Categoria Id:</strong>
                                    {{ $libro->categoria_id }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
