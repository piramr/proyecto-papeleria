<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Copiando datos de tipos_pago a tipo_pagos ===\n";

// Obtener datos de tipos_pago
$datos = DB::table('tipos_pago')->get();
echo "Encontrados " . count($datos) . " registros en tipos_pago\n";

foreach ($datos as $fila) {
    DB::table('tipo_pagos')->insert([
        'id' => $fila->id,
        'nombre' => $fila->nombre,
        'descripcion' => $fila->descripcion,
        'created_at' => $fila->created_at,
        'updated_at' => $fila->updated_at,
    ]);
}

echo "Datos copiados exitosamente\n";

// Verificar
echo "\n=== Verificando tipo_pagos ===\n";
$nuevos = DB::table('tipo_pagos')->get();
foreach ($nuevos as $tipo) {
    echo "ID: {$tipo->id}, Nombre: {$tipo->nombre}, Descripción: {$tipo->descripcion}\n";
}

echo "\nTotal registros en tipo_pagos: " . count($nuevos) . "\n";
