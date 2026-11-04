<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EspecieRequest extends FormRequest
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
        $especieId = $this->route('especie')?->id ?? $this->route('especie');

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('especie', 'nombre')->ignore($especieId),
            ],
            'estado' => 'required|integer',
        ];
    }
}
