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

        {{-- nombre --}}
        <div class="form-group mb-2 mb20">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user?->name) }}" id="name" placeholder="Name">
            {!! $errors->first('name', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        {{-- email --}}
        <div class="form-group mb-2 mb20">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user?->email) }}" id="email" placeholder="Email">
            {!! $errors->first('email', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        {{-- password --}}
        <div class="form-group mb-2 mb20">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="{{ __('Password') }}">
            {!! $errors->first('password', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
       
        {{-- password confirmation --}}
        <div class="form-group mb-2 mb20">
            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
            <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" placeholder="{{ __('Confirm Password') }}">
            {!! $errors->first('password_confirmation', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}   
        </div>
        {{-- estado --}}
        <div class="form-group mb-2 mb20">
            <label for="estado" class="form-label">{{ __('Estado') }}</label>
            <select name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror">
                <option value="">Seleccione</option>
                <option value="1" {{ old('estado', $user?->estado ?? 1) == 1 ? 'selected' : '' }}>Activo</option>
                <option value="0" {{ old('estado', $user?->estado ?? 1) == 0 ? 'selected' : '' }}>Inactivo</option>
            </select>
            {!! $errors->first('estado', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
    </div>
</div>