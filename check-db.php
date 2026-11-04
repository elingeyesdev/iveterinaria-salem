<?php

use App\Models\Mascotum;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo '=== Última mascota ==='.PHP_EOL;
$mascotum = Mascotum::latest()->first();
if ($mascotum) {
    echo json_encode($mascotum, JSON_PRETTY_PRINT).PHP_EOL;
    echo PHP_EOL.'=== Ruta completa de la imagen ==='.PHP_EOL;
    echo $mascotum->imagen.PHP_EOL;
    echo '=== URL ==='.PHP_EOL;
    echo asset($mascotum->imagen).PHP_EOL;
} else {
    echo 'No hay mascotas registradas.'.PHP_EOL;
}
