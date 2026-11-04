@extends('layouts.app')

@section('template_title')
    {{ $subServicio->name ?? __('Show') . " " . __('SubServicio') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} SubServicio</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('sub_servicios.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">

                        <div class="form-group mb-2 mb20">
                            <strong>Nombre:</strong>
                            {{ $subServicio->nombre }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Descripcion:</strong>
                            {{ $subServicio->descripcion }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Cantidad Servicios:</strong>
                            {{ $subServicio->servicios_count ?? 0 }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Estado:</strong>
                            {{ $subServicio->estado }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
