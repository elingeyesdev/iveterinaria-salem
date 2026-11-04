<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * OWASP ASVS V2.1.1: Normalización de espacios previa a la validación.
     * Aplica trim + colapsado de espacios múltiples sobre password y password_confirmation
     * antes de invocar al validador y al método create().
     *
     * @return RedirectResponse|JsonResponse
     */
    public function register(Request $request)
    {
        $normalized = [];

        if ($request->has('password') && is_string($request->input('password'))) {
            $normalized['password'] = preg_replace('/\s+/', ' ', trim($request->input('password')));
        }

        if ($request->has('password_confirmation') && is_string($request->input('password_confirmation'))) {
            $normalized['password_confirmation'] = preg_replace('/\s+/', ' ', trim($request->input('password_confirmation')));
        }

        if ($normalized !== []) {
            $request->merge($normalized);
        }

        return $this->traitRegister($request);
    }

    /**
     * Proxy al método register() original del trait para evitar recursividad.
     *
     * @return RedirectResponse|JsonResponse
     */
    protected function traitRegister(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect($this->redirectPath());
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * OWASP ASVS V2.1.1: Longitud mínima de 12 caracteres en la contraseña
     * (se valida sobre el valor normalizado proveniente de register()).
     *
     * @param  array<string, mixed>  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ], [
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 12 caracteres (después de normalizar espacios).',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array<string, mixed>  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
