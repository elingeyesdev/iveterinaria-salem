<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * OWASP ASVS V2.1.1: Sobrescribe las reglas por defecto del trait ResetsPasswords.
     * Obliga a una longitud mínima de 12 caracteres.
     *
     * @return array<string, array<int, Password|string>|string>
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Password::min(12)],
        ];
    }

    /**
     * OWASP ASVS V2.1.1: Normalización de espacios previa a la validación.
     * Aplica trim + colapsado de espacios múltiples sobre password y password_confirmation
     * para que la longitud se mida sobre el valor real.
     *
     * @return RedirectResponse|JsonResponse
     */
    public function reset(Request $request)
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

        return $this->traitReset($request);
    }

    /**
     * Proxy al método reset() original del trait, renombrado para evitar recursividad.
     *
     * @return RedirectResponse|JsonResponse
     */
    protected function traitReset(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        return $response == \Illuminate\Support\Facades\Password::PASSWORD_RESET
            ? $this->sendResetResponse($request, $response)
            : $this->sendResetFailedResponse($request, $response);
    }

    /**
     * Mensajes de error personalizados para la política de contraseña OWASP.
     *
     * @return array<string, string>
     */
    protected function validationErrorMessages()
    {
        return [
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 12 caracteres (después de normalizar espacios).',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ];
    }
}
