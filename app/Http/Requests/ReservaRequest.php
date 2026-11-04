<?php

namespace App\Http\Requests;

use App\Services\ReservaService;
use App\Support\ReservaEstado;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ReservaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $fechaSeleccionada = $this->input('fecha');
        $esHoy = is_string($fechaSeleccionada)
            && $fechaSeleccionada === Carbon::today()->toDateString();
        $horaMinima = Carbon::now()->format('H:i');

        $rulesHoraInicio = [
            'required',
            'date_format:H:i',
            Rule::unique('reserva', 'hora_inicio')
                ->where(fn ($query) => $query->where('fecha', $this->input('fecha')))
                ->ignore($this->route('reserva')),
        ];

        if ($esHoy) {
            $rulesHoraInicio[] = 'after_or_equal:'.$horaMinima;
        }

        return [
            'cliente_id' => ['required', 'integer', 'exists:cliente,id'],
            'mascota_id' => ['required', 'integer', 'exists:mascota,id'],
            'fecha' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'hora_inicio' => $rulesHoraInicio,
            // hora_fin ya no se toma del navegador: el servidor la calcula
            // (hora_inicio + duración del turno). Se acepta por compatibilidad.
            'hora_fin' => ['nullable', 'date_format:H:i'],
            'estado' => ['nullable', Rule::in(ReservaEstado::all())],
        ];
    }

    /**
     * Verifica que la hora elegida sea un turno real de la clínica para esa
     * fecha (día hábil, dentro del horario y en un intervalo válido).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->hasAny(['fecha', 'hora_inicio'])) {
                return;
            }

            $servicio = app(ReservaService::class);

            if (! $servicio->esHorarioValido((string) $this->input('fecha'), (string) $this->input('hora_inicio'))) {
                $validator->errors()->add(
                    'hora_inicio',
                    'El horario seleccionado no está dentro de los turnos de atención de la clínica.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'cliente_id.required' => 'Debe seleccionar un cliente.',
            'cliente_id.exists' => 'El cliente seleccionado no existe.',
            'mascota_id.required' => 'Debe seleccionar una mascota.',
            'mascota_id.exists' => 'La mascota seleccionada no existe.',
            'fecha.required' => 'Debe seleccionar una fecha.',
            'fecha.date_format' => 'La fecha no tiene un formato válido.',
            'fecha.after_or_equal' => 'La fecha no puede ser anterior a hoy.',
            'hora_inicio.required' => 'Debe seleccionar una hora.',
            'hora_inicio.date_format' => 'La hora no tiene un formato válido.',
            'hora_inicio.unique' => 'Ese horario ya fue reservado. Seleccione otra hora.',
            'hora_inicio.after_or_equal' => 'La hora seleccionada ya pasó. Elija una hora posterior al momento actual.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errores = $validator->errors();

        // Se muestra el primer mensaje concreto, en orden de relevancia.
        $message = 'No se pudo guardar la reserva. Verifique los datos ingresados.';
        foreach (['hora_inicio', 'fecha', 'cliente_id', 'mascota_id'] as $campo) {
            if ($errores->has($campo)) {
                $message = $errores->first($campo);
                break;
            }
        }

        if ($this->route('reserva')) {
            $redirect = redirect()->route('reserva.edit', $this->route('reserva'));
        } else {
            $redirect = redirect()->route('reserva.create');
        }

        throw new HttpResponseException(
            $redirect->with('error', $message)->withInput()
        );
    }
}
