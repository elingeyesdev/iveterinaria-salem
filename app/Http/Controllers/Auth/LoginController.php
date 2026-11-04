<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Además de email y password, exigimos que el usuario indique el rol
     * con el que intenta ingresar.
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
            'role' => 'required|string|in:admin,user,veterinario',
        ]);
    }

    /**
     * Se ejecuta justo después de una autenticación exitosa (email/password
     * correctos). Verificamos que el rol elegido en el formulario coincida
     * con alguno de los roles reales del usuario; si no, cerramos la sesión
     * recién iniciada y devolvemos un error de validación.
     */
    /**
     * Solo pueden iniciar sesión los usuarios activos (users.estado = 1).
     */
    protected function credentials(Request $request)
    {
        return array_merge(
            $request->only($this->username(), 'password'),
            ['estado' => 1]
        );
    }

    protected function authenticated(Request $request, $user)
    {
        if (! $user->hasRole($request->input('role'))) {
            $this->guard()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'role' => ['El rol seleccionado no corresponde a este usuario.'],
            ]);
        }
    }
}
