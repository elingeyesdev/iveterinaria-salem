<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Veterinario
 *
 * @mixin Builder
 */
class Veterinario extends Model
{
    protected $table = 'veterinario';

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nombre', 'apellido', 'direccion', 'correo', 'telefono', 'estado'];

    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'veterinario_id', 'id');
    }
}
