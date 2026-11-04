@extends('adminlte::page')

@section('title', 'CREAR USUARIO')

@section('content_header')

@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Crear') }} Usuario</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('role-users.store') }}" role="form" enctype="multipart/form-data" novalidate>
                            @csrf

                            @include('role-user.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
