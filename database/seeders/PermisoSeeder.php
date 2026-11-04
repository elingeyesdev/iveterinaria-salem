<?php

namespace Database\Seeders;

use App\Models\Permiso;
use Illuminate\Database\Seeder;

/**
 * Carga el catálogo de permisos definido en App\Support\PermisoCatalogo.
 *
 * Uso: php artisan db:seed --class=PermisoSeeder
 */
class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        Permiso::sincronizarCatalogo();
    }
}
