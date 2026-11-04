<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recordatorio extends Model
{
    protected $table = 'recordatorio';

    protected $fillable = [
        'mascota_id',
        'reserva_id',
        'consulta_id',
        'servicio_id',
        'tipo',
        'titulo',
        'descripcion',
        'fecha',
        'estado',
        'fecha_envio',
        'enviado_at',
        'canal',
        'error_envio',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_envio' => 'datetime',
        'enviado_at' => 'datetime',
    ];

    public function mascota()
    {
        return $this->belongsTo(Mascotum::class, 'mascota_id');
    }

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'reserva_id');
    }

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'consulta_id');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    public function estaEnviado(): bool
    {
        return ! is_null($this->enviado_at);
    }
}
