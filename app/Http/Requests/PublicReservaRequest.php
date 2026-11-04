<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PublicReservaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $fecha = $this->input('fecha');
        $esHoy = $fecha && Carbon::parse($fecha)->isToday();
        $horaMinima = Carbon::now()->format('H:i');

        $horaRules = ['required', 'date_format:H:i'];
        if ($esHoy) {
            $horaRules[] = 'after_or_equal:'.$horaMinima;
        }

        return [
            // Datos del cliente. El correo identifica al cliente existente; si no
            // existe, el controlador lo crea dentro de la misma transacción.
            'nombre_cliente' => ['required', 'string', 'max:255'],
            'apellido_cliente' => ['nullable', 'string', 'max:255'],
            'correo' => ['required', 'email', 'max:255'],
            'telefono' => ['required', 'string', 'regex:/\A(?:5[367]|6[0-57-9]|7[0-9])[0-9]{6}\z/'],
            'direccion' => ['nullable', 'string', 'max:255'],

            // Datos de la mascota. No se exige mascota_id porque puede ser nueva.
            'nombre_mascota' => ['required', 'string', 'max:255'],
            'especie_id' => ['required', 'integer', 'exists:especie,id'],
            'raza_id' => ['required', 'integer', 'exists:raza,id'],
            'fecha_nac' => ['required', 'date', 'before_or_equal:today'],
            'peso' => ['required', 'numeric', 'min:0', 'max:1000'],
            'sexo' => ['required', Rule::in(['Macho', 'Hembra', 'No especificado'])],

            // Solo se recibe el inicio; el servidor calcula hora_fin = +20 minutos.
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'hora_inicio' => $horaRules,
            'sitio_web' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_cliente.required' => 'Ingrese el nombre del cliente.',
            'correo.required' => 'Ingrese el correo del cliente.',
            'correo.email' => 'Ingrese un correo válido.',
            'telefono.required' => 'Ingrese el teléfono del cliente.',
            'nombre_mascota.required' => 'Ingrese el nombre de la mascota.',
            'especie_id.required' => 'Seleccione la especie de la mascota.',
            'especie_id.exists' => 'La especie seleccionada no existe.',
            'raza_id.required' => 'Seleccione la raza de la mascota.',
            'raza_id.exists' => 'La raza seleccionada no existe.',
            'fecha_nac.required' => 'Ingrese la fecha de nacimiento de la mascota.',
            'fecha_nac.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'peso.required' => 'Ingrese el peso de la mascota.',
            'peso.numeric' => 'El peso debe ser numérico.',
            'peso.min' => 'El peso no puede ser negativo.',
            'sexo.required' => 'Seleccione el sexo de la mascota.',
            'fecha.required' => 'Seleccione una fecha para la reserva.',
            'fecha.after_or_equal' => 'La fecha de la reserva no puede ser anterior a hoy.',
            'hora_inicio.required' => 'Seleccione un horario.',
            'hora_inicio.date_format' => 'El horario no tiene un formato válido.',
            'hora_inicio.after_or_equal' => 'La hora seleccionada ya pasó. Elija otra hora.',
        ];
    }
}
