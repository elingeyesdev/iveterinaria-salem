<?php

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo PHP_EOL.'=== UNIQUE CONSTRAINTS DE mascota ==='.PHP_EOL;
$idx = DB::select('SHOW INDEX FROM mascota WHERE Key_name != ?', ['PRIMARY']);
foreach ($idx as $i) {
    echo ($i->Non_unique ? 'INDEX   ' : 'UNIQUE  ').$i->Key_name.' -> '.$i->Column_name.PHP_EOL;
}

echo PHP_EOL.'=== ENUM pago.tipo ==='.PHP_EOL;
$r = DB::select("SHOW COLUMNS FROM pago WHERE Field='tipo'");
echo $r[0]->Type.PHP_EOL;

echo PHP_EOL.'=== ENUM reserva.estado ==='.PHP_EOL;
$r = DB::select("SHOW COLUMNS FROM reserva WHERE Field='estado'");
echo $r[0]->Type.PHP_EOL;

echo PHP_EOL.'=== COLUMNAS DE consulta (estado_consulta NO debe existir) ==='.PHP_EOL;
$cols = DB::select('SHOW COLUMNS FROM consulta');
$foundEstadoConsulta = false;
foreach ($cols as $c) {
    echo '  - '.$c->Field.' ['.$c->Type.']'.PHP_EOL;
    if ($c->Field === 'estado_consulta') {
        $foundEstadoConsulta = true;
    }
}
echo PHP_EOL.'[CHECK] Existe estado_consulta en consulta? '.($foundEstadoConsulta ? '⚠️ SI (incorrecto)' : '✅ NO (correcto)').PHP_EOL;
