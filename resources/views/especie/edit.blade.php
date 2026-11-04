@extends('adminlte::page')

@section('title', 'Editar Especie')

@section('content_header')
   
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                   <div class="card-header bg-dark text-white">
                        <span class="card-title">{{ __('EDITAR') }} ESPECIE</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('especies.update', $especie->id) }}" role="form" novalidate>
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('especie.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
