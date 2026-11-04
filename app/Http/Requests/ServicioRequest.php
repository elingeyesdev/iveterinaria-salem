<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServicioRequest extends FormRequest
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
            'nombre' => [
                'required',
                'string',
                Rule::unique('servicio', 'nombre')->ignore($this->route('servicio')),
            ],
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric',
            'sub_servicio_id' => ['nullable', 'exists:sub_servicio,id'],
            'estado' => 'required|integer',
        ];
    }
}
