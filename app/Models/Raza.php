<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Raza extends Model
{
    protected $table = 'raza';

    protected $fillable = ['nombre', 'estado'];

    public function mascotas()
    {
        return $this->hasMany(Mascotum::class, 'raza_id', 'id');
    }
}
