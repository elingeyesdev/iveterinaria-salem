<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultaServicio extends Model
{
    //
    protected $table = 'consulta_servicio';

    protected $fillable = [
        'fecha_actual',
        'fecha_prox',
        'precio',
        'peso',
        'consulta_id',
        'servicio_id',
    ];

    protected $casts = [
        'fecha_actual' => 'date:Y-m-d',
        'fecha_prox' => 'date:Y-m-d',
        'precio' => 'float',
        'peso' => 'float',
    ];
}
