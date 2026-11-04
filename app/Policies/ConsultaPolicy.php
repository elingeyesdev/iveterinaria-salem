<?php

namespace App\Policies;

use App\Models\Consulta;
use App\Models\User;
use App\Support\PermisoCatalogo;

/**
 * Acceso al submódulo "Consultas y emergencias".
 *
 * Quien tiene el permiso asignado por el administrador puede ver, registrar
 * y atender consultas. Eliminar sigue siendo exclusivo del administrador.
 */
class ConsultaPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->puedeUsarConsultas($user);
    }

    public function view(User $user, Consulta $consulta): bool
    {
        return $this->puedeUsarConsultas($user);
    }

    public function create(User $user): bool
    {
        return $this->puedeUsarConsultas($user);
    }

    public function update(User $user, Consulta $consulta): bool
    {
        return $consulta->estado_clinico !== Consulta::FINALIZADA && $this->puedeUsarConsultas($user);
    }

    public function delete(User $user, Consulta $consulta): bool
    {
        return $consulta->estado_clinico !== Consulta::FINALIZADA && $user->esAdministrador();
    }

    public function priority(User $user, Consulta $consulta): bool
    {
        return $consulta->estado_clinico !== Consulta::FINALIZADA && $this->puedeUsarConsultas($user);
    }

    public function complete(User $user, Consulta $consulta): bool
    {
        return $this->puedeUsarConsultas($user);
    }

    private function puedeUsarConsultas(User $user): bool
    {
        return $user->tienePermiso(PermisoCatalogo::CONSULTAS);
    }
}
