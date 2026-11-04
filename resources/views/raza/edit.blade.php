@extends('adminlte::page')

@section('title', 'Editar Raza')

@section('content_header')
   
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                   <div class="card-header bg-dark text-white">
                        <span class="card-title">{{ __('EDITAR') }} RAZA</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('razas.update', $raza->id) }}" role="form" novalidate>
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('raza.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
