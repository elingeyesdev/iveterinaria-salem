<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RoleUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('role_user'); // El parámetro de la ruta para resource('role-users') es 'role_user'

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$userId,
            'password' => $this->isMethod('POST') ? 'required|string|min:12|confirmed' : 'nullable|string|min:8|confirmed',
            'estado' => 'required|integer',
        ];
    }
}
