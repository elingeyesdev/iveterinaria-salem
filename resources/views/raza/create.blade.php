@extends('adminlte::page')

<!--@section('title', 'CREAR Raza')-->

@section('content_header')

@endsection
@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                   <div class="card-header bg-dark text-white">
                        <span class="card-title">{{ __('CREAR') }} Raza</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('razas.store') }}" role="form" novalidate>
                            @csrf

                            @include('raza.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
