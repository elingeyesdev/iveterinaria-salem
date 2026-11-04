@extends('adminlte::page')

@section('title', 'Veterinarios')

@section('content_header')

@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header bg-dark text-white">
                        <span class="card-title">{{ __('CREAR') }} VETERINARIO</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('veterinarios.store') }}" role="form" enctype="multipart/form-data" novalidate>
                            @csrf

                            @include('veterinario.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
