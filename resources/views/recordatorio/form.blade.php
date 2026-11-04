@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="fas fa-exclamation-triangle mr-1"></i> Corrige los siguientes errores:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="mascota_id">Mascota <span class="text-danger">*</span></label>
                    <select name="mascota_id" id="mascota_id" class="form-control @error('mascota_id') is-invalid @enderror" required>
                        <option value="">Seleccione una mascota</option>
                        @foreach ($mascotas as $mascota)
                            <option value="{{ $mascota->id }}" @selected(old('mascota_id', $recordatorio->mascota_id) == $mascota->id)>
                                {{ $mascota->nombre }}
                                — {{ $mascota->cliente?->nombre }} {{ $mascota->cliente?->apellido }}
                            </option>
                        @endforeach
                    </select>
                    @error('mascota_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="tipo">Tipo <span class="text-danger">*</span></label>
                    <select name="tipo" id="tipo" class="form-control @error('tipo') is-invalid @enderror" required>
                        <option value="">Seleccione un tipo</option>
                        @foreach (['Vacunación', 'Desparasitación', 'Control veterinario', 'Baño/Peluquería', 'Limpieza dental', 'Otro'] as $tipo)
                            <option value="{{ $tipo }}" @selected(old('tipo', $recordatorio->tipo) === $tipo)>{{ $tipo }}</option>
                        @endforeach
                    </select>
                    @error('tipo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-8">
                <div class="form-group">
                    <label for="titulo">Título <span class="text-danger">*</span></label>
                    <input type="text" name="titulo" id="titulo" maxlength="255"
                           class="form-control @error('titulo') is-invalid @enderror"
                           value="{{ old('titulo', $recordatorio->titulo) }}"
                           placeholder="Ej. Vacuna antirrábica" required>
                    @error('titulo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="fecha">Fecha <span class="text-danger">*</span></label>
                    <input type="date" name="fecha" id="fecha"
                           class="form-control @error('fecha') is-invalid @enderror"
                           value="{{ old('fecha', $recordatorio->fecha?->format('Y-m-d')) }}" required>
                    @error('fecha')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="estado">Estado <span class="text-danger">*</span></label>
                    <select name="estado" id="estado" class="form-control" required>
                        @foreach (['Pendiente', 'Completado', 'Cancelado'] as $estado)
                            <option value="{{ $estado }}" @selected(old('estado', $recordatorio->estado ?? 'Pendiente') === $estado)>{{ $estado }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-8">
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="3" maxlength="1000"
                              class="form-control @error('descripcion') is-invalid @enderror"
                              placeholder="Detalles adicionales del recordatorio...">{{ old('descripcion', $recordatorio->descripcion) }}</textarea>
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer bg-white">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i> Guardar recordatorio
        </button>
        <a href="{{ route('recordatorio.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Regresar
        </a>
    </div>
</div>
