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
                                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $servicio->nombre) }}" required>
                                    @error('nombre') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                                    <label for="descripcion">Descripcion</label>
                                    <input type="text" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" value="{{ old('descripcion', $servicio->descripcion) }}" required>
                                    @error('descripcion') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                                    <label for="precio">Precio</label>
                                    <input type="text" name="precio" class="form-control @error('precio') is-invalid @enderror" value="{{ old('precio', $servicio->precio) }}" required>
                                    @error('precio') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                            <div class="form-group">
                                    <label for="sub_servicio_id">Sub Servicio</label>
                                    <select name="sub_servicio_id" id="sub_servicio_id" class="form-control @error('sub_servicio_id') is-invalid @enderror">
                                        <option value="">Seleccione</option>
                                        @foreach($subServicios as $SubServicio)
                                            <option value="{{ $SubServicio->id }}" {{ old('sub_servicio_id', $servicio?->sub_servicio_id ?? null) == $SubServicio->id ? 'selected' : '' }}>
                                                {{ $SubServicio->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('servicio_id', '<div class="invalid-feedback">:message</div>') !!}
                        </div>
                    </div>
                </div>
                 <div class="row">
                    <div class="col-md-6">
                        <label for="estado" class="form-label">{{ __('Estado') }}</label>
                                <select name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror">
                                    <option value="">Seleccione</option>
                                    <option value="1" {{ old('estado', $servicio?->estado ?? 1) == 1 ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ old('estado', $servicio?->estado ?? 1) == 0 ? 'selected' : '' }}>Inactivo</option>
                                </select>
                                {!! $errors->first('estado', '<div class="invalid-feedback">:message</div>') !!}
                        
                    </div>
                </div>
            </div>
            <div class="card-footer">
                    <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
                    <a href="{{ route('servicios.index') }}" class="btn btn-secondary">{{ __('Regresar') }}</a>
                </div>
        </div>
    </div>
</div>