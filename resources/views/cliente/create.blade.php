@extends('adminlte::page')

<!--@section('title', 'CREAR Clientes')-->

@section('content_header')
   
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header bg-dark text-white">
                        <span class="card-title">{{ __('CREAR') }} CLIENTE</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('clientes.store', ['from' => request('from')]) }}" role="form" enctype="multipart/form-data" novalidate>
                            @csrf
                            @include('cliente.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
