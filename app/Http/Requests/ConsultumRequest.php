<?php

namespace App\Http\Requests;

use App\Models\Consulta;
use App\Support\ConsultaTipo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConsultumRequest extends FormRequest
{
    public function authorize(): bool
    {
        $consulta = $this->route('consulta');

        return $consulta instanceof Consulta
            ? (bool) $this->user()?->can('update', $consulta)
            : (bool) $this->user()?->can('create', Consulta::class);
    }

    /**
     * Reglas de validación de una consulta veterinaria.
     */
    public function rules(): array
    {
        return [
            /*
             * Fecha de la consulta.
             */
            'fecha' => [
                'required',
                'date_format:Y-m-d',
            ],

            /*
             * Tipo de consulta.
             */
            'tipo' => [
                'nullable',
                'string',
                Rule::in(ConsultaTipo::all()),
            ],

            /*
             * Motivo.
             */
            'motivo' => [
                'required',
                'string',
                'min:3',
                'max:160',
            ],

            /*
             * Diagnóstico.
             */
            'diagnostico' => [
                'required',
                'string',
                'min:3',
                'max:160',
            ],

            /*
             * Tratamiento.
             */
            'tratamiento' => [
                'nullable',
                'string',
                'max:160',
            ],

            /*
             * Peso.
             */
            'peso' => [
                'required_without_all:__fake__',
                'nullable',
                'numeric',
                'min:0',
                'max:1000',
            ],

            /*
             * Veterinario.
             */
            'veterinario_id' => [
                'required',
                'integer',
                Rule::exists('veterinario', 'id')
                    ->where(fn ($query) => $query->where('estado', 1)),
            ],

            /*
             * Reserva.
             */
            'reserva_id' => [
                'nullable',
                'integer',
                'exists:reserva,id',
                'required_if:tipo,Reserva',
            ],

            /*
             * Servicios.
             *
             * Cada elemento debe tener:
             *
             * servicios.*.servicio_id
             * servicios.*.fecha_prox
             */
            'servicios' => [
                'required',
                'array',
                'min:1',
            ],

            /*
             * ID del servicio.
             */
            'servicios.*.servicio_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('servicio', 'id')
                    ->where(fn ($query) => $query->where('estado', 1)),
            ],

            /*
             * Fecha próxima de cada servicio.
             *
             * Es opcional porque no todos los servicios necesariamente
             * requieren una próxima atención.
             *
             * Ejemplo:
             *
             * Vacunación       -> 2026-10-22
             * Desparasitación  -> 2026-12-22
             * Corte de uñas    -> null
             */
            'servicios.*.fecha_prox' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:fecha',
            ],
        ];
    }

    /**
     * Mensajes personalizados.
     */
    public function messages(): array
    {
        return [
            'servicios.required' => 'Debes agregar al menos un servicio.',

            'servicios.min' => 'Debes agregar al menos un servicio.',

            'servicios.*.servicio_id.required' => 'Cada servicio debe tener un servicio seleccionado.',

            'servicios.*.servicio_id.exists' => 'Uno de los servicios seleccionados ya no está disponible.',

            'servicios.*.servicio_id.distinct' => 'No puedes agregar el mismo servicio más de una vez.',

            'servicios.*.fecha_prox.date_format' => 'La fecha próxima debe tener un formato válido.',

            'servicios.*.fecha_prox.after_or_equal' => 'La fecha próxima no puede ser anterior a la fecha de la consulta.',
        ];
    }
}
