<?php

namespace App\Models;

use App\Support\ReservaEstado;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reserva';

    /**
     * OWASP ASVS 5.1.2 - Protección contra Mass Assignment.
     *
     * Solo estos atributos pueden ser asignados mediante create()/update().
     * Los campos internos que no estén aquí no se aceptan por asignación masiva.
     */
    protected $fillable = [
        'fecha',
        'hora_inicio',
        'hora_fin',
        'mascota_id',
        'estado',
    ];

    public function scopeEstado($query, $estado)
    {
        if (! empty($estado)) {
            return $query->where('estado', $estado);
        }

        return $query;
    }

    public function mascota()
    {
        return $this->belongsTo(Mascotum::class, 'mascota_id');
    }

    public function consulta()
    {
        return $this->hasOne(Consulta::class, 'reserva_id');
    }

    /**
     * Cliente dueño de la mascota reservada.
     *
     * La reserva no guarda cliente_id propio (solo mascota_id), así que el
     * cliente se obtiene siempre a través de la mascota. Se expone como
     * accessor ($reserva->cliente) para que las vistas puedan usarlo
     * directamente; para evitar N+1 hay que precargar 'mascota.cliente'.
     */
    public function getClienteAttribute()
    {
        return $this->mascota?->cliente;
    }

    public function getClienteIdAttribute()
    {
        return $this->mascota?->cliente_id;
    }

    public function getHoraInicioAttribute($value): string
    {
        return $value ? substr($value, 0, 5) : '';
    }

    public function getHoraFinAttribute($value): string
    {
        return $value ? substr($value, 0, 5) : '';
    }

    /**
     * Marca la reserva como Atendida.
     *
     * Antes esto se repetía como Reserva::where('id', ...)->update(['estado' => 'Atendida'])
     * en store(), update() y marcarCompletada() de ConsultaController.
     */
    public function marcarAtendida(): void
    {
        $this->update(['estado' => ReservaEstado::ATENDIDA]);
    }

    /**
     * Devuelve la reserva a Pendiente, pero SOLO si ya no le queda ninguna
     * consulta asociada (una reserva puede haber tenido su consulta borrada,
     * o haber sido reasignada a otra reserva desde el formulario de edición).
     *
     * $consultaIdExcluida sirve para el caso "editar consulta y cambiarle la
     * reserva": en ese momento la consulta ya apunta a la reserva nueva, así
     * que se excluye su propio id al contar cuántas consultas le quedan a la
     * reserva anterior.
     *
     * Antes esto era un bloque de ~8 líneas duplicado en update(), destroy()
     * y (de forma menos completa) en store() de ConsultaController.
     */
    public function liberarSiSinConsultasActivas(?int $consultaIdExcluida = null): void
    {
        $tieneConsultas = Consulta::where('reserva_id', $this->id)
            ->when($consultaIdExcluida, fn ($q) => $q->where('id', '!=', $consultaIdExcluida))
            ->exists();

        if (! $tieneConsultas) {
            $this->update(['estado' => ReservaEstado::PENDIENTE]);
        }
    }
}
