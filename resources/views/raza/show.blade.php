@extends('layouts.app')

@section('template_title')
    {{ $raza->name ?? __('Show') . " " . __('Raza') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Raza</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('razas.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Nombre:</strong>
                                    {{ $raza->nombre }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Cantidad Mascotas:</strong>
                                    {{ $raza->mascotas_count ?? 0 }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Estado:</strong>
                                    {{ $raza->estado ? 'Activo' : 'Inactivo' }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
