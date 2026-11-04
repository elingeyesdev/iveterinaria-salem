<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Elimina un registro de catálogo de forma segura.
     *
     * - Solo los administradores pueden eliminar (Gate 'eliminar-registros').
     * - Si el registro tiene datos relacionados (llaves foráneas RESTRICT),
     *   se devuelve un mensaje claro en lugar de un error 500.
     */
    protected function eliminarRegistro(Model $registro, string $rutaIndex, string $mensajeExito): RedirectResponse
    {
        $this->authorize('eliminar-registros');

        try {
            $registro->delete();
        } catch (QueryException $e) {
            return redirect()->route($rutaIndex)->with(
                'error',
                'No se puede eliminar porque tiene registros relacionados. Puede desactivarlo cambiando su estado.'
            );
        }

        return redirect()->route($rutaIndex)->with('success', $mensajeExito);
    }
}
