<?php

namespace App\Providers;

use App\Models\Consulta;
use App\Models\Pago;
use App\Models\Reserva;
use App\Support\PermisoCatalogo;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * OWASP ASVS V2.1.1: Configuración global de la regla de contraseña.
     * Todas las validaciones que utilicen Rules\Password::defaults()
     * heredan el mínimo de 12 caracteres.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Gate central para ocultar y proteger la administración desde el menú.
        Gate::define('manage-users', fn ($user): bool => $user->hasRole('admin'));

        // Eliminar registros de catálogo (clientes, mascotas, servicios, etc.):
        // solo administradores. El resto del personal puede crear y editar.
        Gate::define('eliminar-registros', fn ($user): bool => $user->hasRole('admin'));

        $this->registrarPermisosPorModulo();

        Password::defaults(
            static fn (): Password => Password::min(12)
        );
    }

    /**
     * Registra un Gate por cada permiso del catálogo ("permiso.clientes",
     * "permiso.reportes.ingresos", etc.).
     *
     * - El administrador pasa todas las verificaciones (Gate::before).
     * - El resto solo accede a los submódulos que el administrador le asignó
     *   en el panel de permisos (OWASP: denegar por defecto, mínimo privilegio).
     */
    private function registrarPermisosPorModulo(): void
    {
        Gate::before(function ($user, string $ability, array $arguments): ?bool {
            $consulta = $arguments[0] ?? null;
            if (($consulta instanceof Pago || $consulta === Pago::class)
                && in_array($ability, ['delete', 'forceDelete', 'deleteAny', 'forceDeleteAny'], true)) {
                return false;
            }

            if ($consulta instanceof Consulta && in_array($ability, ['update', 'delete', 'priority'], true)) {
                return null;
            }

            if ($consulta instanceof Reserva && $ability === 'update') {
                return null;
            }

            return method_exists($user, 'esAdministrador') && $user->esAdministrador() ? true : null;
        });

        foreach (PermisoCatalogo::claves() as $clave) {
            Gate::define(
                PermisoCatalogo::gate($clave),
                fn ($user): bool => $user->tienePermiso($clave)
            );
        }

        Gate::define(PermisoCatalogo::GATE_REPORTES, fn ($user): bool => collect(PermisoCatalogo::clavesDeReportes())
            ->contains(fn (string $clave): bool => $user->tienePermiso($clave)));
    }
}
