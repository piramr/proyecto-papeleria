<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\TipoPago;
use Illuminate\Support\Facades\DB;

echo "=== Tipos de Pago en la BD ===\n";
$tipos = TipoPago::all();
foreach ($tipos as $tipo) {
    echo "ID: {$tipo->id}, Nombre: {$tipo->nombre}, Descripción: {$tipo->descripcion}\n";
}

echo "\n=== Query Raw ===\n";
$rawTipos = DB::table('tipo_pagos')->get();
foreach ($rawTipos as $tipo) {
    echo "ID: {$tipo->id}, Nombre: {$tipo->nombre}, Descripción: {$tipo->descripcion}\n";
}

echo "\nTotal registros: " . count($tipos) . "\n";
