@extends('layouts.app')

@section('template_title')
    {{ $cliente->name ?? __('Show') . " " . __('Cliente') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Cliente</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('clientes.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Nombre:</strong>
                                    {{ $cliente->nombre }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Apellido:</strong>
                                    {{ $cliente->apellido }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Direccion:</strong>
                                    {{ $cliente->direccion }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Correo:</strong>
                                    {{ $cliente->correo }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Telefono:</strong>
                                    {{ $cliente->telefono }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Estado:</strong>
                                    {{ $cliente->estado }}
                                </div>

                                <div class="form-group mb-2 mb20">
                                    <strong>Mascotas:</strong>
                                    @if($cliente->mascotas->isNotEmpty())
                                        <ul class="mt-2 mb-0">
                                            @foreach($cliente->mascotas as $mascota)
                                                <li>{{ $mascota->nombre }} ({{ $mascota->especie }})</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="mb-0">No tiene mascotas registradas.</p>
                                    @endif
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
