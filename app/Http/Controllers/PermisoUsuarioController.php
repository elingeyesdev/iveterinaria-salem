<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermisoUsuarioRequest;
use App\Models\Permiso;
use App\Models\User;
use App\Support\PermisoCatalogo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Panel donde el administrador decide a qué submódulos accede cada usuario.
 *
 * Las rutas están protegidas con el middleware role:admin. Cada cambio se
 * registra en el log con el evento "authz_change" (OWASP Logging Vocabulary).
 */
class PermisoUsuarioController extends Controller
{
    public function index(Request $request): View
    {
        $busqueda = trim((string) $request->input('search', ''));

        $usuarios = User::query()
            ->with('roles')
            ->withCount(['permisos' => fn ($consulta) => $consulta->whereIn('clave', PermisoCatalogo::claves())])
            ->when($busqueda !== '', function ($consulta) use ($busqueda): void {
                $consulta->where(function ($filtro) use ($busqueda): void {
                    $filtro->where('name', 'LIKE', "%{$busqueda}%")
                        ->orWhere('email', 'LIKE', "%{$busqueda}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('permiso.index', [
            'usuarios' => $usuarios,
            'busqueda' => $busqueda,
            'totalPermisos' => count(PermisoCatalogo::claves()),
        ]);
    }

    public function edit(User $user): View|RedirectResponse
    {
        if ($user->esAdministrador()) {
            return redirect()->route('permisos.index')
                ->with('info', "{$user->name} es administrador y tiene acceso total; no necesita permisos asignados.");
        }

        Permiso::sincronizarCatalogo();

        $permisosPorModulo = Permiso::query()
            ->whereIn('clave', PermisoCatalogo::claves())
            ->orderBy('orden')
            ->get()
            ->groupBy('modulo');

        return view('permiso.edit', [
            'usuario' => $user->load('roles'),
            'permisosPorModulo' => $permisosPorModulo,
            'asignados' => $user->permisos()->pluck('permisos.id')->all(),
            'dependencias' => $this->dependenciasPorId(),
        ]);
    }

    public function update(PermisoUsuarioRequest $request, User $user): RedirectResponse
    {
        if ($user->esAdministrador()) {
            return redirect()->route('permisos.index')
                ->with('info', "{$user->name} es administrador y tiene acceso total.");
        }

        $nuevos = $request->idsSeleccionados();

        $cambios = DB::transaction(fn (): array => $user->permisos()->sync($nuevos));

        Log::info('authz_change', [
            'admin_id' => $request->user()->id,
            'usuario_id' => $user->id,
            'otorgados' => Permiso::whereIn('id', $cambios['attached'])->pluck('clave')->all(),
            'revocados' => Permiso::whereIn('id', $cambios['detached'])->pluck('clave')->all(),
            'ip' => $request->ip(),
        ]);

        $total = count($nuevos);
        $mensaje = $total === 0
            ? "Se quitaron todos los permisos de {$user->name}. Solo podrá ver la página de inicio."
            : "Permisos de {$user->name} actualizados: {$total} ".($total === 1 ? 'submódulo habilitado.' : 'submódulos habilitados.');

        return redirect()->route('permisos.index')->with('success', $mensaje);
    }

    /**
     * Mapa id => [ids requeridos], usado por la vista para marcar
     * automáticamente las dependencias (la validación real está en el servidor).
     *
     * @return array<int, array<int, int>>
     */
    private function dependenciasPorId(): array
    {
        $idsPorClave = Permiso::query()->pluck('id', 'clave');
        $dependencias = [];

        foreach (PermisoCatalogo::definiciones() as $clave => $definicion) {
            if ($definicion['requiere'] === [] || ! isset($idsPorClave[$clave])) {
                continue;
            }

            $dependencias[$idsPorClave[$clave]] = collect($definicion['requiere'])
                ->map(fn (string $requerida) => $idsPorClave[$requerida] ?? null)
                ->filter()
                ->values()
                ->all();
        }

        return $dependencias;
    }
}
