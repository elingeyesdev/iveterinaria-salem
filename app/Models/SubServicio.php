<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubServicio extends Model
{
    protected $table = 'sub_servicio';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    public function servicios()
    {
        return $this->hasMany(Servicio::class, 'sub_servicio_id', 'id');
    }
}
