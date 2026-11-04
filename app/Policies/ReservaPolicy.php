<?php

namespace App\Policies;

use App\Models\Reserva;
use App\Models\User;
use App\Support\PermisoCatalogo;
use App\Support\ReservaEstado;

/**
 * Acceso al submódulo "Administrar reservas" (gestión interna).
 *
 * Eliminar sigue siendo exclusivo del administrador.
 */
class ReservaPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->puedeUsarReservas($user);
    }

    public function view(User $user, Reserva $reserva): bool
    {
        return $this->puedeUsarReservas($user);
    }

    public function create(User $user): bool
    {
        return $this->puedeUsarReservas($user);
    }

    public function update(User $user, Reserva $reserva): bool
    {
        return $this->puedeUsarReservas($user)
            && ! in_array($reserva->estado, [ReservaEstado::CANCELADA, ReservaEstado::ATENDIDA], true);
    }

    public function delete(User $user, Reserva $reserva): bool
    {
        return $user->esAdministrador();
    }

    public function changeStatus(User $user, Reserva $reserva): bool
    {
        return $this->puedeUsarReservas($user);
    }

    private function puedeUsarReservas(User $user): bool
    {
        return $user->tienePermiso(PermisoCatalogo::RESERVAS);
    }
}
