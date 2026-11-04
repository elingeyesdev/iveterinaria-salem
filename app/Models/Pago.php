<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    public const PENDIENTE = 'Pendiente';

    public const PAGADO = 'Pagado';

    public const ANULADO = 'Anulado';

    protected $table = 'pago';

    protected $attributes = [
        'estado' => self::PENDIENTE,
    ];

    protected $fillable = [
        'monto',
        'estado',
        'consulta_id',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'estado' => 'string',
    ];

    public function consulta(): BelongsTo
    {
        return $this->belongsTo(Consulta::class, 'consulta_id', 'id');
    }
}
