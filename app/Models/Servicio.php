<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Servicio
 *
 * @property $id
 * @property $nombre
 * @property $descripcion
 * @property $precio
 * @property $estado
 * @property $created_at
 * @property $updated_at
 * @property CitaMascotaServicio[] $citaMascotaServicios
 * @property DetalleConsultaServicio[] $detalleConsultaServicios
 * @property Citaservicio[] $citaservicios
 * @property Citaservicio[] $citaservicios
 * @property CitaMascotaServicio[] $citaMascotaServicios
 * @property CitaMascotaServicio[] $citaMascotaServicios
 * @property CitaMascotaServicio[] $citaMascotaServicios
 *
 * @mixin Builder
 */
class Servicio extends Model
{
    protected $perPage = 20;

    protected $table = 'servicio';

    protected $fillable =
        [
            'nombre',
            'descripcion',
            'precio',
            'estado',
            'sub_servicio_id',
        ];

    public function subServicio()
    {
        return $this->belongsTo(SubServicio::class, 'sub_servicio_id', 'id');
    }

    public function consultas()
    {
        return $this->belongsToMany(Consulta::class, 'consulta_servicio', 'servicio_id', 'consulta_id')
            ->withPivot(['fecha_actual', 'fecha_prox', 'peso', 'precio'])
            ->withTimestamps();
    }
}
