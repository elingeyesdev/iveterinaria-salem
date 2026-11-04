@extends('layouts.app')

@section('template_title')
    {{ $especie->name ?? __('Show') . " " . __('Especie') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Especie</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('especies.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Nombre:</strong>
                                    {{ $especie->nombre }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Cantidad Mascotas:</strong>
                                    {{ $especie->mascotas_count ?? 0 }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Estado:</strong>
                                    {{ $especie->estado ? 'Activo' : 'Inactivo' }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
