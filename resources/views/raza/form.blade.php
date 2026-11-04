<div class="row padding-1 p-1">
    <div class="col-md-12">

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <strong><i class="fas fa-exclamation-triangle mr-2"></i> Por favor, corrige los siguientes errores:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        <div class="card card-primary">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $raza?->nombre) }}" id="nombre" placeholder="Nombre">
                        {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label for="estado">Estado</label>
                        <select name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror">
                            <option value="">Seleccione</option>
                            <option value="1" {{ old('estado', $raza?->estado ?? 1) == 1 ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ old('estado', $raza?->estado ?? 1) == 0 ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        {!! $errors->first('estado', '<div class="invalid-feedback">:message</div>') !!}
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
                <a href="{{ route('razas.index') }}" class="btn btn-secondary">
                    {{ __('Regresar') }}
                </a>
            </div>
        </div>
    </div>
</div>
