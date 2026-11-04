<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 *
 * @property $id
 * @property $name
 * @property $email
 * @property $email_verified_at
 * @property $password
 * @property $estado
 * @property $remember_token
 * @property $created_at
 * @property $updated_at
 * @property RoleUser[] $roleUsers
 *
 * @mixin Builder
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'email', 'estado', 'password'];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function getRoleAttribute()
    {
        return $this->roles->first();
    }

    // Método para verificar si el usuario tiene un rol específico
    public function hasRole($roleName)
    {
        // Usa la relación ya cargada: una sola consulta por petición en lugar
        // de una por cada verificación de rol.
        return $this->roles->contains('nombre', $roleName);
    }

    /**
     * Permisos (submódulos) asignados por el administrador.
     */
    public function permisos(): BelongsToMany
    {
        return $this->belongsToMany(Permiso::class, 'permiso_user')->withTimestamps();
    }

    public function esAdministrador(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Indica si el usuario puede usar un submódulo.
     *
     * El administrador tiene acceso total. El resto solo accede a lo que el
     * administrador le asignó (denegar por defecto). Usa la relación cargada,
     * por lo que hace una sola consulta por petición.
     */
    public function tienePermiso(string $clave): bool
    {
        if ($this->esAdministrador()) {
            return true;
        }

        return $this->permisos->contains('clave', $clave);
    }
}
