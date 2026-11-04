<?php

namespace App\Policies;

use App\Models\Consulta;
use App\Models\Pago;
use App\Models\User;
use App\Support\PermisoCatalogo;

/**
 * Registrar un pago requiere el permiso "Cobrar consultas".
 * Anular un pago es exclusivo del administrador (segregación de funciones).
 */
class PagoPolicy
{
    public function create(User $user, Consulta $consulta): bool
    {
        return $user->tienePermiso(PermisoCatalogo::PAGOS);
    }

    public function view(User $user, Pago $pago): bool
    {
        return $user->tienePermiso(PermisoCatalogo::PAGOS);
    }

    public function anular(User $user, Pago $pago): bool
    {
        return $user->esAdministrador();
    }

    public function delete(User $user, Pago $pago): bool
    {
        return false;
    }
}
