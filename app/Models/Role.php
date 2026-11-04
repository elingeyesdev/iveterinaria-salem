<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 *
 * @property $id
 * @property $nombre
 * @property $estado
 * @property $created_at
 * @property $updated_at
 * @property RoleUser[] $roleUsers
 * @property ModelHasRole[] $modelHasRoles
 * @property RoleHasPermission[] $roleHasPermissions
 *
 * @mixin Builder
 */
class Role extends Model
{
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nombre', 'estado'];

    public function roleUsers()
    {
        return $this->hasMany(RoleUser::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
