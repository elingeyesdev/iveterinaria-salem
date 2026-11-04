<div class="row padding-1 p-1">
    <div class="col-md-12">

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
                                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $veterinario?->nombre) }}" required>
                                    @error('nombre') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                    <label for="apellido">Apellido</label>
                                    <input type="text" name="apellido" class="form-control @error('apellido') is-invalid @enderror" value="{{ old('apellido', $veterinario?->apellido) }}" required>
                                    @error('apellido') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="apellido">Direccion</label>
                                    <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion', $veterinario?->direccion) }}" required>
                                    @error('direccion') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="correo">Correo</label>
                                    <input type="text" name="correo" class="form-control @error('correo') is-invalid @enderror" value="{{ old('correo', $veterinario?->correo) }}" required>
                                    @error('correo') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="telefono">Telefono</label>
                                    <input 
                                        name="telefono" type="tel" inputmode="numeric" minlength="8" maxlength="8" pattern="(5[367]|6[0-57-9]|7[0-9])[0-9]{6}" title="Ingrese 8 numeros. Prefijos: 53, 56, 57, 60-65, 67-69 o 70-79. Sin +591." onbeforeinput="this.dataset.telefonoAnterior = this.value" oninput="const numero = this.value.replace(/[^0-9]/g, '').slice(0, 8); const permitido = /^(?:[567]?|(?:5[367]|6[0-57-9]|7[0-9])[0-9]{0,6})$/; this.value = permitido.test(numero) ? numero : (permitido.test(this.dataset.telefonoAnterior || '') ? (this.dataset.telefonoAnterior || '') : '')" 
                                        class="form-control @error('telefono') is-invalid @enderror" 
                                        value="{{ old('telefono', $veterinario?->telefono) }}" 
                                        id="telefono" 
                                        placeholder="Telefono"
                                                 >
                                    {!! $errors->first('telefono', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                        </div>
                        <div class="col-md-6">
                            <label for="estado" class="form-label">{{ __('Estado') }}</label>
                               <select name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror">
                                    <option value="">Seleccione</option>
                                    <option value="1" {{ old('estado', $veterinario?->estado ?? 1) == 1 ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ old('estado', $veterinario?->estado ?? 1) == 0 ? 'selected' : '' }}>Inactivo</option>
                                </select>
                                {!! $errors->first('estado', '<div class="invalid-feedback">:message</div>') !!}
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
                    <a href="{{ route('veterinarios.index') }}" class="btn btn-secondary">{{ __('Regresar') }}</a>
                </div>
            </div>

    </div>
</div>