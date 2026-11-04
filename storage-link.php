<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

try {
    $exitCode = $kernel->call('storage:link');
    echo "Comando storage:link ejecutado. Código de salida: $exitCode\n";
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
}
