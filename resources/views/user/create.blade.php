@extends('adminlte::page')

@section('title', 'CREAR USUARIO')

@section('content_header')

@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header bg-dark text-white">
                        <span class="card-title">{{ __('CREAR') }} USUARIO</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('users.store') }}" role="form" enctype="multipart/form-data" novalidate>
                            @csrf

                            @include('user.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
