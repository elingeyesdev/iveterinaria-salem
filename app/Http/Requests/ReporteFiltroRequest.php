<?php

namespace App\Http\Requests;

use App\Support\Reportes\Filtros;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ReporteFiltroRequest extends FormRequest
{
    private const MAX_DIAS = 366;

    /** La autorización por reporte se resuelve en el controlador (Gate). */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'desde' => ['nullable', 'date_format:Y-m-d'],
            'hasta' => ['nullable', 'date_format:Y-m-d'],
            'veterinario_id' => ['nullable', 'integer', 'exists:veterinario,id'],
            'tipo_pago' => ['nullable', 'in:Efectivo'],
        ];
    }

    public function messages(): array
    {
        return [
            'desde.date_format' => 'La fecha inicial no es válida.',
            'hasta.date_format' => 'La fecha final no es válida.',
            'veterinario_id.exists' => 'El veterinario seleccionado no existe.',
            'tipo_pago.in' => 'El método de pago no es válido.',
        ];
    }

    /** @return array<int, callable> */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $filtros = $this->filtros();

                if ($filtros->hasta->lt($filtros->desde)) {
                    $validator->errors()->add('hasta', 'La fecha final no puede ser anterior a la inicial.');
                } elseif ($filtros->dias() > self::MAX_DIAS) {
                    $validator->errors()->add('hasta', 'El rango máximo permitido es de '.self::MAX_DIAS.' días.');
                }
            },
        ];
    }

    /**
     * Por defecto: del primer día del mes actual hasta hoy.
     */
    public function filtros(): Filtros
    {
        $hoy = CarbonImmutable::today();

        $desde = $this->filled('desde')
            ? CarbonImmutable::createFromFormat('Y-m-d', $this->string('desde')->toString())->startOfDay()
            : $hoy->startOfMonth();

        $hasta = $this->filled('hasta')
            ? CarbonImmutable::createFromFormat('Y-m-d', $this->string('hasta')->toString())->startOfDay()
            : $hoy;

        return new Filtros(
            desde: $desde,
            hasta: $hasta,
            veterinarioId: $this->filled('veterinario_id') ? $this->integer('veterinario_id') : null,
            tipoPago: $this->filled('tipo_pago') ? $this->string('tipo_pago')->toString() : null,
        );
    }
}
