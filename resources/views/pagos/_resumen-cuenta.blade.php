@php
    $mascotaCuenta = $pago->consulta?->reserva?->mascota;
    $clienteCuenta = $mascotaCuenta?->cliente;
@endphp
<dl class="row">
    <dt class="col-sm-3">Mascota</dt><dd class="col-sm-9">{{ $mascotaCuenta?->nombre ?? 'Sin mascota asociada' }}</dd>
    <dt class="col-sm-3">Responsable</dt><dd class="col-sm-9">{{ trim(($clienteCuenta?->nombre ?? '') . ' ' . ($clienteCuenta?->apellido ?? '')) ?: 'Sin responsable registrado' }}</dd>
    <dt class="col-sm-3">Consulta</dt><dd class="col-sm-9">#{{ $pago->consulta_id }}</dd>
</dl>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead><tr><th>Servicios de la consulta</th><th class="text-right">Importe (Bs)</th></tr></thead>
        <tbody>
            @foreach($pago->consulta->servicios as $servicio)
                <tr><td>{{ $servicio->nombre }}</td><td class="text-right">{{ number_format((float) $servicio->pivot->precio, 2) }}</td></tr>
            @endforeach
        </tbody>
        <tfoot><tr><th>Total de servicios</th><th class="text-right">{{ number_format($pago->consulta->total_servicios, 2) }}</th></tr></tfoot>
    </table>
</div>
