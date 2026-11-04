@extends('adminlte::page')

@section('title', 'Mascota')

@section('content_header')

@endsection
@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                   <div class="card-header bg-dark text-white">
                        <span class="card-title">{{ __('CREAR') }} Mascota</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('mascota.store') }}" role="form" enctype="multipart/form-data" novalidate>
                            @csrf
                            <input type="hidden" name="reserva_id" value="{{ $reservaId ?? '' }}">
                            <input type="hidden" name="from" value="{{ request('from') }}">

                            @include('mascotum.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection