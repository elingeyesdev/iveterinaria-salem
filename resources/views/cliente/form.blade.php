
<div class="row padding-1 p-1">
    <div class="col-md-12">

        {{-- Mensaje general de errores --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Por favor, corrige los siguientes errores:
                </strong>

                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card card-primary">

            <div class="card-body">

                {{-- NOMBRE --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">

                            <label for="nombre">Nombre</label>

                            <input type="text"
                                   name="nombre"
                                   id="nombre"
                                   class="form-control @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre', $cliente?->nombre) }}"
                                   required>

                            @error('nombre')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror

                        </div>
                    </div>


                    {{-- APELLIDO --}}
                    <div class="col-md-6">
                        <div class="form-group">

                            <label for="apellido">Apellido</label>

                            <input type="text"
                                   name="apellido"
                                   id="apellido"
                                   class="form-control @error('apellido') is-invalid @enderror"
                                   value="{{ old('apellido', $cliente?->apellido) }}"
                                   required>

                            @error('apellido')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror

                        </div>
                    </div>
                </div>


                {{-- DIRECCIÓN Y CORREO --}}
                <div class="row">

                    <div class="col-md-6">

                        <label for="direccion">Direccion</label>

                        <input type="text"
                               name="direccion"
                               id="direccion"
                               class="form-control @error('direccion') is-invalid @enderror"
                               value="{{ old('direccion', $cliente?->direccion) }}"
                               required>

                        @error('direccion')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    <div class="col-md-6">

                        <label for="correo">Correo</label>

                        <input type="email"
                               name="correo"
                               id="correo"
                               class="form-control @error('correo') is-invalid @enderror"
                               value="{{ old('correo', $cliente?->correo) }}"
                               required>

                        @error('correo')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>

                </div>


                {{-- TELÉFONO Y ESTADO --}}
                <div class="row">

                    <div class="col-md-6">

                        <label for="telefono">Telefono</label>

                        <input
                               name="telefono" type="tel" inputmode="numeric" minlength="8" maxlength="8" pattern="(5[367]|6[0-57-9]|7[0-9])[0-9]{6}" title="Ingrese 8 numeros. Prefijos: 53, 56, 57, 60-65, 67-69 o 70-79. Sin +591." onbeforeinput="this.dataset.telefonoAnterior = this.value" oninput="const numero = this.value.replace(/[^0-9]/g, '').slice(0, 8); const permitido = /^(?:[567]?|(?:5[367]|6[0-57-9]|7[0-9])[0-9]{0,6})$/; this.value = permitido.test(numero) ? numero : (permitido.test(this.dataset.telefonoAnterior || '') ? (this.dataset.telefonoAnterior || '') : '')"
                               id="telefono"
                               class="form-control @error('telefono') is-invalid @enderror"
                               value="{{ old('telefono', $cliente?->telefono) }}"
                               placeholder="Telefono"
                               >

                        @error('telefono')
                            <div class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror

                    </div>


                    <div class="col-md-6">

                        <label for="estado" class="form-label">
                            {{ __('Estado') }}
                        </label>

                        <select name="estado"
                                id="estado"
                                class="form-control @error('estado') is-invalid @enderror">

                            <option value="">Seleccione</option>

                            <option value="1"
                                {{ old('estado', $cliente?->estado ?? 1) == 1 ? 'selected' : '' }}>
                                Activo
                            </option>

                            <option value="0"
                                {{ old('estado', $cliente?->estado ?? 1) == 0 ? 'selected' : '' }}>
                                Inactivo
                            </option>

                        </select>

                        @error('estado')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

            </div>


            {{-- BOTONES --}}
            <div class="card-footer">

                <button type="submit" class="btn btn-primary">
                    {{ __('Guardar') }}
                </button>

                <a href="{{ request('from') === 'reserva'
                    ? route('reserva.create')
                    : route('clientes.index') }}"
                   class="btn btn-secondary">

                    {{ __('Regresar') }}

                </a>

            </div>

        </div>

    </div>
</div>


{{-- =========================================================
     LIMPIEZA AUTOMÁTICA DE CAMPOS CON ERROR
     ========================================================= --}}
@if ($errors->any())

<script>
    document.addEventListener('DOMContentLoaded', function () {

        // Campos que Laravel indicó que tienen errores
        const camposConError = @json($errors->keys());

        camposConError.forEach(function (nombreCampo) {

            const campo = document.querySelector(
                '[name="' + nombreCampo + '"]'
            );

            if (!campo) {
                return;
            }

            /*
            * 1. Limpiar solamente el campo que tiene error
            */
            campo.value = '';


            /*
            * 2. Cuando el usuario empiece a corregirlo,
            *    quitar el borde rojo
            */
            const quitarError = function () {

                campo.classList.remove('is-invalid');

                /*
                * Buscar el mensaje de error correspondiente
                */
                const contenedor = campo.closest('.form-group')
                    || campo.parentElement;

                if (contenedor) {

                    const mensaje = contenedor.querySelector(
                        '.invalid-feedback'
                    );

                    if (mensaje) {
                        mensaje.style.display = 'none';
                    }
                }

                /*
                * Ya no necesitamos estos eventos
                */
                campo.removeEventListener('input', quitarError);
                campo.removeEventListener('change', quitarError);
            };


            /*
            * Para input, correo, teléfono, nombre, etc.
            */
            campo.addEventListener('input', quitarError);


            /*
            * Para select Estado
            */
            campo.addEventListener('change', quitarError);

        });

    });
</script>

@endif
