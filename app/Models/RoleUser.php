<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class RoleUser
 *
 * @property $id
 * @property $user_id
 * @property $role_id
 * @property $created_at
 * @property $updated_at
 * @property Role $role
 * @property User $user
 *
 * @mixin Builder
 */
class RoleUser extends Model
{
    protected $table = 'role_user';

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_id', 'role_id'];

    /**
     * @return BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
