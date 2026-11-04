
<div class="row padding-1 p-1">
    <div class="col-md-12">

        {{-- MENSAJE GENERAL DE ERRORES --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <strong>
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Por favor, corrige los siguientes errores:
                </strong>

                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Cerrar">
                </button>
            </div>
        @endif


        <div class="card card-primary">

            <div class="card-body">

                {{-- =========================
                     NOMBRE
                     ========================= --}}
                <div class="row">

                    <div class="col-md-12">

                        <label for="nombre" class="form-label">
                            {{ __('Nombre') }}
                        </label>

                        <input type="text"
                               name="nombre"
                               id="nombre"
                               class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre', $especie?->nombre) }}"
                               placeholder="Nombre">

                        @error('nombre')
                            <div class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- =========================
                     ESTADO
                     ========================= --}}
                <div class="row mt-3">

                    <div class="col-md-12">

                        <label for="estado">
                            Estado
                        </label>

                        <select name="estado"
                                id="estado"
                                class="form-control @error('estado') is-invalid @enderror">

                            <option value="">Seleccione</option>

                            <option value="1"
                                {{ old('estado', $especie?->estado ?? 1) == 1 ? 'selected' : '' }}>
                                Activo
                            </option>

                            <option value="0"
                                {{ old('estado', $especie?->estado ?? 1) == 0 ? 'selected' : '' }}>
                                Inactivo
                            </option>

                        </select>

                        @error('estado')
                            <div class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror

                    </div>

                </div>

            </div>


            {{-- =========================
                 BOTONES
                 ========================= --}}
            <div class="card-footer">

                <button type="submit" class="btn btn-primary">
                    {{ __('Guardar') }}
                </button>

                <a href="{{ route('especies.index') }}"
                   class="btn btn-secondary">
                    {{ __('Regresar') }}
                </a>

            </div>

        </div>

    </div>
</div>


{{-- =========================================================
     LIMPIAR SOLAMENTE LOS CAMPOS QUE TIENEN ERROR
     ========================================================= --}}
@if ($errors->any())

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
     * Laravel nos entrega únicamente los nombres
     * de los campos que tienen errores.
     */
    const camposConError = @json($errors->keys());


    camposConError.forEach(function (nombreCampo) {

        /*
         * Buscar el campo por su atributo name
         */
        const campo = document.querySelector(
            '[name="' + nombreCampo + '"]'
        );


        /*
         * Si el campo no existe, no hacemos nada
         */
        if (!campo) {
            return;
        }


        /*
         * ==========================================
         * LIMPIAR EL CAMPO
         * ==========================================
         */

        if (campo.tagName === 'SELECT') {

            /*
             * Para SELECT:
             * seleccionar "Seleccione"
             */
            campo.value = '';

        } else {

            /*
             * Para INPUT:
             * limpiar el contenido
             */
            campo.value = '';

        }


        /*
         * ==========================================
         * QUITAR ERROR AL MODIFICAR EL CAMPO
         * ==========================================
         */

        const quitarError = function () {

            /*
             * Quitar borde rojo de Bootstrap
             */
            campo.classList.remove('is-invalid');


            /*
             * Buscar el mensaje de error
             */
            const contenedor = campo.closest('.col-md-12')
                || campo.parentElement;


            if (contenedor) {

                const mensaje = contenedor.querySelector(
                    '.invalid-feedback'
                );


                /*
                 * Ocultar mensaje individual
                 */
                if (mensaje) {
                    mensaje.style.display = 'none';
                }

            }


            /*
             * Ya no necesitamos escuchar más
             * cambios en este campo.
             */
            campo.removeEventListener(
                'input',
                quitarError
            );

            campo.removeEventListener(
                'change',
                quitarError
            );

        };


        /*
         * Para INPUT
         */
        campo.addEventListener(
            'input',
            quitarError
        );


        /*
         * Para SELECT
         */
        campo.addEventListener(
            'change',
            quitarError
        );

    });

});
</script>

@endif
