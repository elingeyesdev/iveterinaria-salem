<!-- PAGOS -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card border-0 bg-white shadow-sm mb-0">
            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                <div>
                    <i class="fa fa-receipt me-1"></i> <strong>Pagos de la consulta</strong>
                </div>
                @if($consulta->saldo_pendiente > 0 && $consulta->total_servicios > 0)
                    <span class="badge bg-warning text-dark">Saldo pendiente</span>
                @elseif($consulta->total_servicios > 0)
                    <span class="badge bg-success">Pagado completo</span>
                @endif
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success py-2 d-flex align-items-center" role="alert">
                        <i class="fa fa-check-circle me-2"></i><span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('info'))
                    <div class="alert alert-info py-2 d-flex align-items-center" role="status">
                        <i class="fa fa-hourglass-half me-2"></i><span>{{ session('info') }}</span>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="alert alert-warning py-2 d-flex align-items-center" role="status">
                        <i class="fa fa-exclamation-circle me-2"></i><span>{{ session('warning') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger py-2 d-flex align-items-center" role="alert">
                        <i class="fa fa-times-circle me-2"></i><span>{{ session('error') }}</span>
                    </div>
                @endif

                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-light">
                            <small class="text-muted d-block">Total servicios</small>
                            <strong class="fs-5">Bs {{ number_format($consulta->total_servicios, 2) }}</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-light">
                            <small class="text-muted d-block">Pagado</small>
                            <strong class="fs-5 text-success">Bs {{ number_format($consulta->total_pagado, 2) }}</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-light">
                            <small class="text-muted d-block">Saldo pendiente</small>
                            <strong class="fs-5 {{ $consulta->saldo_pendiente > 0 ? 'text-warning' : 'text-success' }}">
                                Bs {{ number_format($consulta->saldo_pendiente, 2) }}
                            </strong>
                        </div>
                    </div>
                </div>

                @if($consulta->pagos->count() > 0)
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th class="text-end">Monto</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($consulta->pagos->sortByDesc('created_at') as $pago)
                                    <tr class="{{ $pago->estado === \App\Models\Pago::ANULADO ? 'table-secondary text-muted' : '' }}">
                                        <td>{{ $pago->created_at?->format('d/m/Y H:i') }}</td>
                                        <td class="text-end fw-semibold">Bs {{ number_format((float) $pago->monto, 2) }}</td>
                                        <td>
                                            @if($pago->estado === \App\Models\Pago::PAGADO)
                                                <span class="badge bg-success">Pagado</span>
                                            @elseif($pago->estado === \App\Models\Pago::PENDIENTE)
                                                <span class="badge bg-warning text-dark">Pendiente</span>
                                            @else
                                                <span class="badge bg-secondary">Anulado</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-light border text-muted">
                        <i class="fa fa-info-circle me-1"></i> Todavía no hay pagos registrados para esta consulta.
                    </div>
                @endif

                @if($consulta->total_servicios > 0 && $consulta->saldo_pendiente > 0 && auth()->user()?->cannot('create', [\App\Models\Pago::class, $consulta]))
                    <div class="alert alert-light border mb-0">
                        <i class="fa fa-lock me-1"></i> La consulta tiene saldo pendiente. Tu usuario no tiene el permiso «Cobrar consultas»; el cobro lo registra el personal autorizado.
                    </div>
                @elseif($consulta->total_servicios > 0 && $consulta->saldo_pendiente > 0)
                    @if($consulta->total_pagado > 0)
                        <div class="alert alert-warning">Existen pagos activos. Para evitar cobros parciales, revise y regularice los movimientos anteriores antes de un nuevo cobro.</div>
                    @else
                    <form method="POST" action="{{ route('pagos.store', $consulta) }}" class="border rounded p-3 bg-light">
                        @csrf
                        @if($pendiente = $consulta->pagos->firstWhere('estado', \App\Models\Pago::PENDIENTE))
                            <input type="hidden" name="pago_id" value="{{ $pendiente->id }}">
                        @endif
                        @if($consulta->pagos->contains('estado', \App\Models\Pago::ANULADO))
                            <p class="text-muted">Este cobro genera un movimiento vigente. Los pagos anulados se conservan en el historial.</p>
                        @endif
                        <h6>Cobrar consulta completa en efectivo — Bs {{ number_format($consulta->total_servicios, 2) }}</h6>
                        <p class="text-muted small">Se registra un único pago por el total de los servicios. No se aceptan abonos.</p>
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-4">
                                <label for="monto" class="form-label fw-semibold">Efectivo (Bs)</label>
                            </div>
                            <div class="col-md-8">
                                <input type="number" id="monto" name="monto" class="form-control @error('monto') is-invalid @enderror"
                                    step="0.01" min="0.01" max="{{ number_format($consulta->total_servicios, 2, '.', '') }}"
                                    value="{{ number_format($consulta->total_servicios, 2, '.', '') }}" readonly required>
                                @error('monto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
                        <button type="submit" class="btn btn-primary">Confirmar cobro completo</button>
                    </form>
                    @endif
                @elseif($consulta->total_servicios <= 0)
                    <div class="alert alert-warning mb-0">
                        Agrega al menos un servicio antes de registrar un pago.
                    </div>
                @else
                    <div class="alert alert-success mb-0">
                        <i class="fa fa-check-circle me-1"></i> La consulta está completamente pagada.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- FIN PAGOS -->