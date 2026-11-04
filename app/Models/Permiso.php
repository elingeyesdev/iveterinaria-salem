<?php

namespace App\Models;

use App\Support\PermisoCatalogo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Permiso de acceso a un submódulo del sistema.
 *
 * @property int $id
 * @property string $clave
 * @property string $nombre
 * @property string $modulo
 * @property string|null $descripcion
 * @property int $orden
 */
class Permiso extends Model
{
    protected $table = 'permisos';

    protected $fillable = ['clave', 'nombre', 'modulo', 'descripcion', 'orden'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'permiso_user')->withTimestamps();
    }

    /**
     * Crea o actualiza los registros a partir de PermisoCatalogo.
     * Es idempotente: puede ejecutarse en cada despliegue sin duplicar datos.
     */
    public static function sincronizarCatalogo(): void
    {
        $orden = 0;

        foreach (PermisoCatalogo::definiciones() as $clave => $definicion) {
            static::query()->updateOrCreate(
                ['clave' => $clave],
                [
                    'nombre' => $definicion['nombre'],
                    'modulo' => $definicion['modulo'],
                    'descripcion' => $definicion['descripcion'],
                    'orden' => ++$orden,
                ]
            );
        }
    }
}
