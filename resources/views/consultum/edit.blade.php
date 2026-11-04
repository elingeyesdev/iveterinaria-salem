@extends('adminlte::page')

@section('title', 'Editar Consulta')

@section('content_header')
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header bg-dark text-white">
                        <span class="card-title">{{ __('EDITAR') }} CONSULTA MÉDICA</span>
                    </div>
                    <div class="card-body bg-white">
                        @if ($message = Session::get('error'))
                            <div class="alert alert-danger mb-3">
                                <p class="mb-0">{{ $message }}</p>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('consulta.update', $consulta->id) }}" role="form" enctype="multipart/form-data" novalidate>
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('consultum.form', [
                                'consulta'           => $consulta,
                                'consulta_id'        => $consulta->id,
                                'reserva_id'         => $reserva_id ?? $consulta->reserva_id,
                                'reservaSeleccionada'=> $reservaSeleccionada ?? null,
                                'reservas'           => $reservas,
                                'veterinarios'       => $veterinarios,
                                'servicios'          => $servicios,
                                'isEdit'             => true,
                            ])

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection