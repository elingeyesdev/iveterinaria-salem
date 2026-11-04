@extends('adminlte::page')

@section('title', 'EDITAR VETERINARIO')

@section('content_header')

@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header bg-dark text-white">
                        <span class="card-title">{{ __('EDITAR') }} VETERINARIO</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('veterinarios.update', $veterinario->id) }}" role="form" enctype="multipart/form-data" novalidate>
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('veterinario.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
