<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Permite el paso solo si el usuario autenticado tiene al menos uno de los
     * roles indicados (por nombre). Uso en rutas: ->middleware('role:admin')
     * o ->middleware('role:user') para varios roles válidos.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        $tieneAlgunRol = collect($roles)->contains(fn (string $role) => $user->hasRole($role));

        if (! $tieneAlgunRol) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
