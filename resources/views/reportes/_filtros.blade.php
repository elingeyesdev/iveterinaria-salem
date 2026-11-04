<form method="GET" action="{{ route('reportes.show', $tipo) }}" class="card card-outline card-secondary shadow-sm">
    <div class="card-body pb-2">
        <div class="form-row align-items-end">
            <div class="form-group col-6 col-md-2">
                <label for="desde" class="small text-muted mb-1">Desde</label>
                <input type="date" id="desde" name="desde" value="{{ $filtros->desde->toDateString() }}"
                       class="form-control form-control-sm @error('desde') is-invalid @enderror">
                @error('desde')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group col-6 col-md-2">
                <label for="hasta" class="small text-muted mb-1">Hasta</label>
                <input type="date" id="hasta" name="hasta" value="{{ $filtros->hasta->toDateString() }}"
                       class="form-control form-control-sm @error('hasta') is-invalid @enderror">
                @error('hasta')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>

            @if (in_array('veterinario', $def['filtros'], true))
                <div class="form-group col-md-3">
                    <label for="veterinario_id" class="small text-muted mb-1">Veterinario</label>
                    <select id="veterinario_id" name="veterinario_id" class="form-control form-control-sm @error('veterinario_id') is-invalid @enderror">
                        <option value="">Todos</option>
                        @foreach ($veterinarios as $vet)
                            <option value="{{ $vet->id }}" @selected($filtros->veterinarioId === (int) $vet->id)>
                                {{ trim($vet->nombre . ' ' . $vet->apellido) }}
                            </option>
                        @endforeach
                    </select>
                    @error('veterinario_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
            @endif

            @if (in_array('tipo_pago', $def['filtros'], true))
                <div class="form-group col-md-2">
                    <label for="tipo_pago" class="small text-muted mb-1">Método de pago</label>
                    <select id="tipo_pago" name="tipo_pago" class="form-control form-control-sm @error('tipo_pago') is-invalid @enderror">
                        <option value="">Todos</option>
                        @foreach (['Efectivo'] as $metodo)
                            <option value="{{ $metodo }}" @selected($filtros->tipoPago === $metodo)>{{ $metodo }}</option>
                        @endforeach
                    </select>
                    @error('tipo_pago')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
            @endif

            <div class="form-group col-auto">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-filter mr-1"></i> Aplicar
                </button>
                <a href="{{ route('reportes.show', $tipo) }}" class="btn btn-outline-secondary btn-sm">Restablecer</a>
            </div>
        </div>

        <div class="mb-2">
            <span class="small text-muted mr-1">Rangos rápidos:</span>
            @foreach ($atajos as $etiqueta => $rango)
                <a href="{{ route('reportes.show', array_merge(['tipo' => $tipo], $filtros->toQuery(), $rango)) }}"
                   class="btn btn-xs btn-outline-secondary">{{ $etiqueta }}</a>
            @endforeach
        </div>
    </div>
</form>
