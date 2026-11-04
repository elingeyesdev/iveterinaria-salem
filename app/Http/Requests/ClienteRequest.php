<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string',
            'apellido' => 'required|string',
            'direccion' => 'required|string',
            'correo' => [
                'required',
                'email',
                Rule::unique('cliente', 'correo')->ignore($this->route('cliente')),
            ],
            'telefono' => ['required', 'string', 'regex:/\A(?:5[367]|6[0-57-9]|7[0-9])[0-9]{6}\z/'],
            'estado' => 'required',
        ];
    }
}
