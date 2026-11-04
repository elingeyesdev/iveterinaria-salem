<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->hasRole('admin');
    }

    /**
     * OWASP ASVS V2.1.1: Normalización del valor de contraseña previo a la validación.
     * - Elimina espacios en blanco de los extremos (trim).
     * - Colapsa espacios múltiples internos en un único espacio (preg_replace).
     * - Garantiza que la longitud se cuente sobre caracteres reales, no inflada por espacios.
     * - Aplica la misma normalización a password_confirmation para que coincidan.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('password') && is_string($this->input('password'))) {
            $normalized = preg_replace('/\s+/', ' ', trim($this->input('password')));
            $this->merge(['password' => $normalized]);
        }

        if ($this->has('password_confirmation') && is_string($this->input('password_confirmation'))) {
            $normalizedConfirmation = preg_replace('/\s+/', ' ', trim($this->input('password_confirmation')));
            $this->merge(['password_confirmation' => $normalizedConfirmation]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     *
     * OWASP ASVS V2.1.1: La contraseña debe tener al menos 12 caracteres (longitud
     * contada después de la normalización realizada en prepareForValidation).
     *
     * POST (store):   La contraseña es obligatoria.
     * PUT/PATCH (update): La contraseña es opcional; si se envía, debe cumplir min:12.
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string',
            'email' => [
                'required',
                'string',
                'email',
                Rule::unique('users', 'email')->ignore($this->route('user')),
            ],
            'estado' => 'required|boolean',
            'role_id' => 'required|integer|exists:roles,id',
        ];

        if ($this->isMethod('post')) {
            $rules['password'] = 'required|string|min:12|confirmed';
        } else {
            $rules['password'] = 'nullable|string|min:12|confirmed';
        }

        return $rules;
    }

    /**
     * Mensajes de error personalizados ligados a la política de contraseña OWASP.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 12 caracteres (después de normalizar espacios).',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ];
    }
}
