<?php

$source = __DIR__.'/storage/app/public/mascotas';
$dest = __DIR__.'/public/storage/mascotas';

if (! file_exists(dirname($dest))) {
    mkdir(dirname($dest), 0755, true);
}

// Copiar todos los archivos
$files = scandir($source);
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        copy("$source/$file", "$dest/$file");
        echo "Copiado: $file\n";
    }
}

echo "\nListo!\n";
