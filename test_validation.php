<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Simular un request POST
$_POST['tipo_pago_id'] = '1';

use Illuminate\Http\Request;

$request = Request::capture();
$request->merge(['tipo_pago_id' => '1']);

echo "Valor enviado: " . $request->input('tipo_pago_id') . "\n";
echo "Tipo: " . gettype($request->input('tipo_pago_id')) . "\n";

// Intentar validar
try {
    $validated = $request->validate([
        'tipo_pago_id' => 'required|exists:tipo_pagos,id',
    ]);
    echo "Validación OK\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
