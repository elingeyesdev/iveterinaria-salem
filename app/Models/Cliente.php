<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'cliente';

    protected $perPage = 20;

    protected $fillable = [
        'nombre',
        'apellido',
        'direccion',
        'correo',
        'telefono',
        'estado',
    ];

    public function mascotas()
    {
        return $this->hasMany(Mascotum::class, 'cliente_id');
    }
}
