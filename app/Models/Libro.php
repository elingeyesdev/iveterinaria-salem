<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Libro
 *
 * @property $id
 * @property $nombre
 * @property $autor
 * @property $categoria_id
 * @property $created_at
 * @property $updated_at
 * @property Categoria $categoria
 *
 * @mixin Builder
 */
class Libro extends Model
{
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nombre', 'autor', 'categoria_id'];

    /**
     * @return BelongsTo
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
