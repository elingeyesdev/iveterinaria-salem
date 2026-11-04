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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $subServicio->nombre) }}" required>
                            @error('nombre') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="descripcion">Descripcion</label>
                            <input type="text" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" value="{{ old('descripcion', $subServicio->descripcion) }}" required>
                            @error('descripcion') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="estado" class="form-label">{{ __('Estado') }}</label>
                        <select name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror">
                            <option value="">Seleccione</option>
                            <option value="1" {{ old('estado', $subServicio?->estado ?? 1) == 1 ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ old('estado', $subServicio?->estado ?? 1) == 0 ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        {!! $errors->first('estado', '<div class="invalid-feedback">:message</div>') !!}
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
                <a href="{{ route('sub_servicios.index') }}" class="btn btn-secondary">{{ __('Regresar') }}</a>
            </div>
        </div>
    </div>
</div>
