<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RazaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $razaId = $this->route('raza')?->id ?? $this->route('raza');

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('raza', 'nombre')->ignore($razaId),
            ],
            'estado' => 'required|integer',
        ];
    }
}
