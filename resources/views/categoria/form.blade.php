
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

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Cerrar">
                </button>
            </div>
        @endif


        {{-- NOMBRE --}}
        <div class="form-group mb-2 mb20">

            <label for="nombre" class="form-label">
                {{ __('Nombre') }}
            </label>

            <input type="text"
                   name="nombre"
                   id="nombre"
                   class="form-control @error('nombre') is-invalid @enderror"
                   value="{{ old('nombre', $categoria?->nombre) }}"
                   placeholder="Nombre">

            @error('nombre')
                <div class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </div>
            @enderror

        </div>

    </div>


    <div class="col-md-12 mt20 mt-2">

        <button type="submit" class="btn btn-primary">
            {{ __('Submit') }}
        </button>

    </div>
</div>


{{-- =========================================================
     LIMPIAR SOLAMENTE EL CAMPO QUE TIENE ERROR
     ========================================================= --}}
@if ($errors->has('nombre'))

<script>
document.addEventListener('DOMContentLoaded', function () {

    const campo = document.getElementById('nombre');

    if (!campo) {
        return;
    }

    /*
     * Limpiar únicamente el campo que tiene error
     */
    campo.value = '';


    /*
     * Cuando el usuario empiece a escribir,
     * quitar el borde rojo y el mensaje.
     */
    campo.addEventListener('input', function () {

        // Quitar borde rojo
        campo.classList.remove('is-invalid');

        // Buscar mensaje de error
        const mensaje = campo.parentElement.querySelector(
            '.invalid-feedback'
        );

        // Ocultar mensaje
        if (mensaje) {
            mensaje.style.display = 'none';
        }

    }, {
        once: true
    });

});
</script>

@endif
