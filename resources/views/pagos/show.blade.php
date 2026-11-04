@extends('adminlte::page')

@section('title', 'Ver cuenta')

@section('content_header')
    <h1>Cuenta del pago #{{ $pago->id }}</h1>
@endsection

@section('content')
    <div class="container-fluid">
        <a href="{{ route('pagos.index') }}" class="btn btn-secondary mb-3">Volver a pagos</a>
        <div class="card">
            <div class="card-body">
                @include('pagos._resumen-cuenta')
                <dl class="row mb-0">
                    <dt class="col-sm-3">Consulta</dt><dd class="col-sm-9">#{{ $pago->consulta_id }}</dd>
                    <dt class="col-sm-3">Monto del pago</dt><dd class="col-sm-9">Bs {{ number_format((float) $pago->monto, 2) }}</dd>
                    <dt class="col-sm-3">Estado</dt><dd class="col-sm-9">{{ $pago->estado }}</dd>
                    <dt class="col-sm-3">Fecha de registro</dt><dd class="col-sm-9">{{ $pago->created_at?->format('d/m/Y H:i') }}</dd>
                </dl>
            </div>
        </div>
        @if($pago->estado === \App\Models\Pago::PAGADO)
            <a href="{{ route('pagos.comprobante', $pago) }}" class="btn btn-outline-primary mb-3">Imprimir</a>
        @endif
        @include('pagos._detalle', ['consulta' => $pago->consulta])
    </div>
@endsection
