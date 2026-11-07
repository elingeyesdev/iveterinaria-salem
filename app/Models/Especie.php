<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especie extends Model
{
    protected $table = 'especie';

    protected $fillable = ['nombre', 'estado'];

    public function mascotas()
    {
        return $this->hasMany(Mascotum::class, 'especie_id', 'id');
    }
}
