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
                    <div class="col-md-3">
                        <div class="text-center">
                            <label for="imagen" style="cursor: pointer; display: block; width: 100%;">
                                <div id="imagen-preview-container" style="width: 100%; aspect-ratio: 1/1; background-color: #f0f4f0; border: 2px dashed #c8d6c8; border-radius: 16px; display: flex; flex-direction: column; align-items: center; justify-content: center; margin-bottom: 10px; position: relative;">
                                    <div id="imagen-placeholder">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#2d6a4f" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2 2V9z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="mt-2 fw-semibold" style="color: #2d6a4f;">Subir imagen</span>
                                    </div>
                                    <img id="imagen-preview" style="display: none; width: 100%; height: 100%; object-fit: cover; border-radius: 14px; position: absolute;">
                                </div>
                            </label>
                            <input type="file" name="imagen" id="imagen" class="form-control form-control-sm" style="display: none;" accept="image/*">
                        
                            <p class="text-muted small mt-2 mb-0">
                                JPG o PNG, máx. 4 MB. Se recomienda una imagen cuadrada.
                            </p>
                        </div>
                    </div>
             
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="cliente_id" class="form-label">{{ __('Dueño') }}</label>
                                <select name="cliente_id" class="form-control @error('cliente_id') is-invalid @enderror" id="cliente_id">
                                    <option value="">{{ __('Seleccione al Dueño') }}</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}"
                                            @selected(old('cliente_id', $mascotum?->cliente_id ?? request('cliente_id')) == $cliente->id)>
                                            {{ $cliente->nombre }} {{ $cliente->apellido }}
                                        </option>
                                    @endforeach
                                </select>
                                {!! $errors->first('cliente_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
                                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $mascotum?->nombre) }}" id="nombre" placeholder="Nombre">
                                {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="sexo">Sexo</label>
                                <select name="sexo" id="sexo" class="form-control @error('sexo') is-invalid @enderror">
                                    <option value="">Seleccione</option>
                                    <option value="Macho" {{ old('sexo', $mascotum?->sexo) === 'Macho' ? 'selected' : '' }}>Macho</option>
                                    <option value="Hembra" {{ old('sexo', $mascotum?->sexo) === 'Hembra' ? 'selected' : '' }}>Hembra</option>
                                    <option value="No especificado" {{ old('sexo', $mascotum?->sexo) === 'No especificado' ? 'selected' : '' }}>No especificado</option>
                                </select>
                                {!! $errors->first('sexo', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                            </div>
                            <div class="col-md-4 ">
                                <label for="peso">Peso</label>
                                    <input type="text" 
                                        name="peso" 
                                        class="form-control @error('peso') is-invalid @enderror" 
                                        value="{{ old('peso', $mascotum?->peso) }}" 
                                        id="peso" 
                                        placeholder="Peso"
                                        maxlength="8"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,8)">
                                    {!! $errors->first('peso', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                            
                            </div>
                            <div class="col-md-4 ">
                                <label for="fecha_nac" class="form-label">{{ __('Fecha Nac') }}</label>
                                <input type="date" name="fecha_nac" class="form-control @error('fecha_nac') is-invalid @enderror" value="{{ old('fecha_nac', $mascotum?->fecha_nac) }}" id="fecha_nac" placeholder="Fecha Nac">
                                {!! $errors->first('fecha_nac', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6 mb-2">
                                <label for="especie_id" class="form-label fw-bold">{{ __('Especie') }}</label>
                                <select name="especie_id" id="especie_id" class="form-control @error('especie_id') is-invalid @enderror">
                                    <option value="">Seleccione una especie</option>
                                    @foreach($especies as $especie)
                                        <option value="{{ $especie->id }}" 
                                            {{ (isset($mascotum) && $mascotum->especie_id == $especie->id) ? 'selected' : '' }}>
                                            {{ $especie->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('especie_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Selector Independiente de Raza -->
                            <div class="form-group col-md-6 mb-3">
                                <label for="raza_id" class="form-label fw-bold">{{ __('Raza') }}</label>
                                <select name="raza_id" id="raza_id" class="form-control @error('raza_id') is-invalid @enderror">
                                    <option value="">Seleccione una raza</option>
                                    @foreach($razas as $raza)
                                        <option value="{{ $raza->id }}" {{ (isset($mascotum) && $mascotum->raza_id == $raza->id) ? 'selected' : '' }}>
                                            {{ $raza->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('raza_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <label for="estado">Estado</label>
                                <select name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror">
                                    <option value="">Seleccione</option>
                                    <option value="1" {{ old('estado', $mascotum?->estado ?? 1) == 1 ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ old('estado', $mascotum?->estado ?? 1) == 0 ? 'selected' : '' }}>Inactivo</option>
                                </select>
                                {!! $errors->first('estado', '<div class="invalid-feedback">:message</div>') !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
                <a href="{{ request('from') === 'consulta'
                    ? route('consulta.create', ['reserva_id' => request('reserva_id')])
                    : (request('from') === 'reserva'
                        ? route('reserva.create', ['cliente_id' => request('cliente_id')])
                        : route('mascota.index')) }}"
                     class="btn btn-secondary">
                    {{ __('Regresar') }}
                </a>
            </div>
        </div>
    </div>
</div>

<script>
        document.getElementById('imagen').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('imagen-preview').src = event.target.result;
                    document.getElementById('imagen-preview').style.display = 'block';
                    document.getElementById('imagen-placeholder').style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        });

        @if($mascotum?->imagen)
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('imagen-preview').src = '{{ Storage::url($mascotum->imagen) }}';
                document.getElementById('imagen-preview').style.display = 'block';
                document.getElementById('imagen-placeholder').style.display = 'none';
            });
        @endif
</script>