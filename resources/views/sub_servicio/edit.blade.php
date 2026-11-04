@extends('adminlte::page')

@section('title', 'EDITAR SUBSERVICIO')

@section('content_header')
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header bg-dark text-white">
                        <span class="card-title">{{ __('EDITAR') }} SUBSERVICIO</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('sub_servicios.update', $subServicio->id) }}" role="form" enctype="multipart/form-data" novalidate>
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('sub_servicio.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
