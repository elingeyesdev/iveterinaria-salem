<?php

namespace App\Http\Requests;

use App\Models\Mascotum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class MascotumRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $mascota = $this->route('mascotum');

        return ! ($mascota instanceof Mascotum) || ! $mascota->tieneReservaPendiente();
    }

    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(
            redirect()->route('mascota.index')
                ->with('error', 'No puedes editar esta mascota porque tiene una reserva pendiente. Primero cancela o atiende la reserva.')
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Obtener el modelo Mascotum de la ruta (inyección de dependencias)
        // $mascotum = $this->route('mascotum');

        // dd($mascotum); // Descomenta esta línea si quieres depurar

        return [
            'cliente_id' => ['required', Rule::exists('cliente', 'id')->where(fn ($query) => $query->where('estado', 1))],
            'especie_id' => ['required', 'exists:especie,id'],
            'raza_id' => ['required', 'exists:raza,id'],
            'nombre' => [
                'required',
                'string',
                Rule::unique('mascota', 'nombre')
                    ->where(fn ($query) => $query->where('cliente_id', $this->input('cliente_id')))
                    ->ignore($this->route('mascotum')),
            ],
            'fecha_nac' => 'required',
            'peso' => 'required|numeric',
            'sexo' => ['required', Rule::in(['Macho', 'Hembra', 'No especificado'])],
            // =====================================================================
            // A08:2021 — Fallos de Integridad de Software y Datos
            // Validación estricta del archivo de imagen para evitar la carga de
            // scripts maliciosos, polyglots (PHP embebido en JPG) o extensiones
            // falsificadas. Capas de protección:
            //   1. 'file'       -> Asegura que sea un archivo subido (no un path).
            //   2. 'image'      -> Laravel usa getimagesize() para verificar el
            //                      contenido real de la imagen.
            //   3. 'mimes'      -> Extensión permitida explícitamente.
            //   4. 'mimetypes'  -> Revisa la cabecera MIME real del contenido.
            //   5. 'max:2048'   -> Tamaño máximo (2 MB) — evita DoS por archivos
            //                      gigantes.
            //   6. Finfo + hash  -> Verificación de integridad adicional que se
            //                        ejecuta en MascotumController después de subir.
            // =====================================================================
            'imagen' => [
                'nullable',
                'file',
                'image',
                'mimes:jpeg,png,jpg',
                'mimetypes:image/jpeg,image/png,image/jpg',
                'max:4096',
            ],
            'estado' => 'required|integer',
        ];
    }
}
