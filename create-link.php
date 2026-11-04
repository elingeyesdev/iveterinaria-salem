<?php

$target = __DIR__.'/storage/app/public';
$link = __DIR__.'/public/storage';

if (file_exists($link)) {
    echo "El enlace ya existe.\n";
} else {
    if (symlink($target, $link)) {
        echo "Enlace creado correctamente.\n";
    } else {
        echo "Error al crear el enlace. Asegúrate de tener permisos.\n";
    }
}
