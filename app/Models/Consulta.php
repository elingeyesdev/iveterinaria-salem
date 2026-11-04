<?php

namespace App\Models;

use App\Casts\CompactEncryptedText;
use App\Support\ConsultaTipo;
use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    public const EN_ATENCION = 'En atención';

    public const FINALIZADA = 'Finalizada';

    protected $table = 'consulta';

    protected $attributes = [
        'estado_clinico' => self::EN_ATENCION,
    ];

    /**
     * OWASP ASVS 5.1.2 - Protección contra Mass Assignment.
     *
     * Solo estos atributos pueden ser asignados mediante create()/update().
     * Los campos internos que no estén aquí no se aceptan por asignación masiva.
     */
    /**
     * OWASP ASVS V6.1.2 - Datos de salud cifrados en reposo.
     *
     * Motivo, diagnóstico y tratamiento contienen información clínica de la
     * atención veterinaria. Se cifran automáticamente con AES-256-GCM usando
     * APP_KEY; al leer el modelo Laravel entrega el texto descifrado a la vista.
     */
    protected $casts = [
        'fecha' => 'date:Y-m-d',
        'finalizada_at' => 'datetime',
        'peso' => 'float',
        'motivo' => CompactEncryptedText::class,
        'diagnostico' => CompactEncryptedText::class,
        'tratamiento' => CompactEncryptedText::class,
    ];

    protected $fillable = [
        'fecha',
        'tipo',
        'motivo',
        'diagnostico',
        'tratamiento',
        'peso',
        'reserva_id',
        'veterinario_id',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'reserva_id', 'id');
    }

    public function veterinario()
    {
        return $this->belongsTo(Veterinario::class, 'veterinario_id', 'id');
    }

    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'consulta_servicio', 'consulta_id', 'servicio_id')
            ->withPivot(['fecha_actual', 'fecha_prox', 'peso', 'precio'])
            ->withTimestamps();
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'consulta_id', 'id');
    }

    public function pagosActivos()
    {
        return $this->hasMany(Pago::class, 'consulta_id', 'id')->where('estado', Pago::PAGADO);
    }

    public function getTotalServiciosAttribute(): float
    {
        if (! $this->relationLoaded('servicios')) {
            $this->load('servicios');
        }

        return round((float) $this->servicios->sum(fn ($servicio) => (float) ($servicio->pivot->precio ?? 0)), 2);
    }

    public function getTotalPagadoAttribute(): float
    {
        if (! $this->relationLoaded('pagos')) {
            $this->load('pagos');
        }

        return round((float) $this->pagos->where('estado', Pago::PAGADO)->sum('monto'), 2);
    }

    public function getSaldoPendienteAttribute(): float
    {
        return round(max(0, $this->total_servicios - $this->total_pagado), 2);
    }

    /** Resumen económico para la interfaz; no administra cobros. */
    public function getEstadoPagoResumenAttribute(): string
    {
        $total = $this->total_servicios;
        if ($total <= 0) {
            return 'Sin cargos';
        }

        return $this->saldo_pendiente <= 0 ? 'Pagado' : 'Pendiente';
    }

    /**
     * Mascota atendida en esta consulta.
     *
     * La tabla "consulta" no tiene columna mascota_id: para una consulta
     * "Reserva" la mascota siempre es la de su reserva de origen. Se expone
     * como accessor ($consulta->mascotum) para las vistas; para evitar N+1
     * hay que precargar 'reserva.mascota' (y sus relaciones anidadas).
     */
    public function getMascotumAttribute()
    {
        return $this->reserva?->mascota;
    }

    public function getEstadoBadgeAttribute(): string
    {
        return match ($this->estado_clinico) {
            self::FINALIZADA => '<span class="badge bg-success">Finalizada</span>',
            self::EN_ATENCION => '<span class="badge bg-primary">En atención</span>',
            default => '<span class="badge bg-secondary">Por revisar</span>',
        };
    }

    public function getEstadoClaveAttribute(): string
    {
        if ($this->estado_clinico === self::FINALIZADA) {
            return 'completada';
        }

        if ($this->estado_clinico !== self::EN_ATENCION) {
            return 'por_revisar';
        }

        return $this->tipo === ConsultaTipo::EMERGENCIA ? 'emergencia' : 'pendiente';
    }
}
