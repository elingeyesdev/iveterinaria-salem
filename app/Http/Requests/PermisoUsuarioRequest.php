<?php

namespace App\Http\Requests;

use App\Models\Permiso;
use App\Support\PermisoCatalogo;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Valida la asignación de permisos de un usuario desde el panel del admin.
 */
class PermisoUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->hasRole('admin');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'permisos' => ['nullable', 'array'],
            'permisos.*' => [
                'integer',
                'distinct',
                Rule::exists('permisos', 'id')->whereIn('clave', PermisoCatalogo::claves()),
            ],
        ];
    }

    /**
     * Comprueba las dependencias declaradas en el catálogo
     * (por ejemplo, "Cobrar consultas" requiere "Consultas y emergencias").
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $clavesSeleccionadas = Permiso::query()
                    ->whereIn('id', $this->idsSeleccionados())
                    ->pluck('clave')
                    ->all();

                foreach ($clavesSeleccionadas as $clave) {
                    $definicion = PermisoCatalogo::definiciones()[$clave];

                    foreach ($definicion['requiere'] as $claveRequerida) {
                        if (! in_array($claveRequerida, $clavesSeleccionadas, true)) {
                            $validator->errors()->add('permisos', sprintf(
                                'El permiso «%s» requiere también «%s».',
                                $definicion['nombre'],
                                PermisoCatalogo::definiciones()[$claveRequerida]['nombre']
                            ));
                        }
                    }
                }
            },
        ];
    }

    /**
     * @return array<int, int>
     */
    public function idsSeleccionados(): array
    {
        return array_map('intval', $this->input('permisos', []));
    }

    public function messages(): array
    {
        return [
            'permisos.*.exists' => 'Uno de los permisos seleccionados no es válido.',
        ];
    }
}
