@extends('adminlte::page')

@section('title', 'Administrar Pagos')

@section('content_header')
    <h1>{{ __('Administrar Pagos') }}</h1>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <form id="pagos-search-form" action="{{ route('pagos.index') }}" method="GET" role="search">
                            <div class="d-flex flex-wrap justify-content-between align-items-center">
                                <div class="d-flex flex-wrap align-items-center mb-2">
                                    
                    
                                    <div class="d-flex align-items-center">
                                        
                                        <select aria-label="Cantidad de pagos" name="perPage" id="perPageSelect" class="form-control w-auto">
                                            <option value="all" @selected(($filtros['perPage'] ?? 'all') === 'all')>Todos</option>
                                            @foreach ([5, 10, 15] as $cantidad)
                                                <option value="{{ $cantidad }}" @selected((int) ($filtros['perPage'] ?? 'all') === $cantidad)>{{ $cantidad }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="input-group mb-2 w-auto">
                                    <label for="estado" class="sr-only">Estado del pago</label>
                                    <select id="estado" name="estado" class="form-control">
                                        <option value="" @selected(empty($filtros['estado']))>Todos los estados</option>
                                        @foreach ([\App\Models\Pago::PENDIENTE, \App\Models\Pago::PAGADO, \App\Models\Pago::ANULADO] as $estado)
                                            <option value="{{ $estado }}" @selected(($filtros['estado'] ?? '') === $estado)>{{ $estado }}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit" aria-label="Filtrar pagos"><i class="fa fa-filter" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    @foreach (['success', 'info', 'warning', 'error'] as $mensaje)
                        @if(session($mensaje))
                            <div class="alert alert-{{ $mensaje === 'error' ? 'danger' : $mensaje }} m-4" role="status">{{ session($mensaje) }}</div>
                        @endif
                    @endforeach
                    @if($errors->any())
                        <div class="alert alert-danger m-4" role="alert">{{ $errors->first() }}</div>
                    @endif
                    <div id="pagos-search-error" class="alert alert-danger m-4" role="alert" hidden></div>
                    <div class="card-body bg-white" id="table-container" aria-live="polite">
                        @include('pagos._table')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    @vite('resources/js/app.js')
@endsection
