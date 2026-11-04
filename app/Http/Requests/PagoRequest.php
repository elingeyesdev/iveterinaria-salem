<?php

namespace App\Http\Requests;

use App\Models\Consulta;
use App\Models\Pago;
use Illuminate\Foundation\Http\FormRequest;

class PagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $consulta = $this->route('consulta');

        return $consulta instanceof Consulta && ($this->user()?->can('create', [Pago::class, $consulta]) ?? false);
    }

    public function rules(): array
    {
        return [
            'pago_id' => ['nullable', 'integer', 'min:1'],
            'monto' => ['required', 'numeric', 'decimal:0,2', 'gt:0', 'max:99999999.99'],
            'tipo' => ['prohibited'],
            'pagos' => ['prohibited'],
        ];
    }
}
