<?php

namespace App\Http\Requests;

use App\Models\Pago;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PagoFiltroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'estado' => ['nullable', 'string', Rule::in([Pago::PENDIENTE, Pago::PAGADO, Pago::ANULADO])],
            'perPage' => ['nullable', Rule::in(['all', '5', '10', '15'])],
            'consulta_id' => [Rule::excludeIf(! $this->routeIs('pagos.create')), 'nullable', 'integer', 'min:1'],
        ];
    }
}
