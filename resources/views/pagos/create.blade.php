@extends('adminlte::page')
@section('title', 'Registrar pago')
@section('content_header')
    <h1>Registrar pago</h1>
@endsection
@section('content')
    @if($consulta)
        @php($mascota = $consulta->reserva?->mascota)
        <div class="card">
            <div class="card-body">
                <h2 class="h5">Cuenta de la consulta #{{ $consulta->id }}</h2>
                <p>Estado clínico: {!! $consulta->estado_badge !!}</p>
                <p>{{ $mascota?->nombre ?? 'Sin mascota' }} — {{ $mascota?->cliente?->nombre }} {{ $mascota?->cliente?->apellido }}</p>
                <a href="{{ route('pagos.create') }}" class="btn btn-outline-primary">Seleccionar otra consulta</a>
                @can('permiso.consultas')
                    <a href="{{ route('consulta.show', $consulta) }}" class="btn btn-outline-secondary">Ver consulta</a>
                @endcan
            </div>
        </div>
        @include('pagos._detalle')
    @else
        <div class="card">
            <div class="card-body">
                <h2 class="h5">Selecciona la consulta que deseas cobrar</h2>
                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif
                <form method="GET" action="{{ route('pagos.create') }}" class="form-inline mb-3">
                    <label for="consulta_id" class="mr-2">Número de consulta</label>
                    <input name="consulta_id" id="consulta_id" type="number" min="1" required class="form-control mr-2">
                    <button class="btn btn-primary" type="submit">Buscar cuenta</button>
                </form>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead><tr><th>Consulta</th><th>Fecha</th><th>Mascota / Cliente</th><th>Total</th><th>Saldo</th><th>Acción</th></tr></thead>
                        <tbody>
                        @forelse($consultas as $item)
                            @php($mascota = $item->reserva?->mascota)
                            <tr>
                                <td>#{{ $item->id }}</td>
                                <td>{{ $item->fecha?->format('d/m/Y') }}</td>
                                <td>{{ $mascota?->nombre ?? 'Sin mascota' }}<br><small>{{ $mascota?->cliente?->nombre }} {{ $mascota?->cliente?->apellido }}</small></td>
                                <td>Bs {{ number_format($item->total_servicios, 2) }}</td>
                                <td>Bs {{ number_format($item->saldo_pendiente, 2) }}</td>
                                <td><a href="{{ route('pagos.create', ['consulta_id' => $item->id]) }}" class="btn btn-sm btn-primary">Ver cuenta / cobrar</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No hay consultas registradas.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $consultas->links() }}
            </div>
        </div>
    @endif
@endsection
