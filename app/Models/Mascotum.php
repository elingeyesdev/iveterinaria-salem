<?php

namespace App\Models;

use App\Support\ReservaEstado;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Mascotum
 *
 * @property $id
 * @property $cliente_id
 * @property $nombre
 * @property $especie
 * @property $fecha_nac
 * @property $peso
 * @property $sexo
 * @property $estado
 * @property $created_at
 * @property $updated_at
 * @property Cliente $cliente
 * @property CitaMascotum[] $citaMascotas
 * @property Consultum[] $consultas
 * @property Consultum[] $consultas
 * @property Consultum[] $consultas
 * @property Citum[] $citas
 * @property Citum[] $citas
 * @property CitaMascotum[] $citaMascotas
 * @property CitaMascotum[] $citaMascotas
 * @property CitaMascotum[] $citaMascotas
 *
 * @mixin Builder
 */
class Mascotum extends Model
{
    protected $table = 'mascota';

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cliente_id',
        'especie_id',
        'raza_id',
        'nombre',
        'fecha_nac',
        'peso',
        'sexo',
        'imagen',
        'imagen_hash',
        'estado',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function raza()
    {
        return $this->belongsTo(Raza::class, 'raza_id');
    }

    public function especie()
    {
        return $this->belongsTo(Especie::class, 'especie_id');
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'mascota_id', 'id');
    }

    public function tieneReservaPendiente(): bool
    {
        return $this->reservas()->where('estado', ReservaEstado::PENDIENTE)->exists();
    }

    public function recordatorios()
    {
        return $this->hasMany(Recordatorio::class, 'mascota_id', 'id');
    }
}
