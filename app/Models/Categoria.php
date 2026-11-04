<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Categoria
 *
 * @property $id
 * @property $nombre
 * @property $created_at
 * @property $updated_at
 * @property Libro[] $libros
 *
 * @mixin Builder
 */
class Categoria extends Model
{
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nombre'];

    /**
     * @return HasMany
     */
    public function libros()
    {
        return $this->hasMany(Libro::class);
    }

    /**
     * Autocorrección: Asegura que el nombre siempre empiece con mayúscula.
     */
    protected function nombre(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => ucfirst(mb_strtolower($value)),
        );
    }
}
