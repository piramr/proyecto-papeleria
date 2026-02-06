<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Actualizar el precio de costo a 1.50
DB::table('producto_proveedores')
    ->where('producto_id', 1)
    ->where('proveedor_ruc', '1004782522001')
    ->update(['precio_costo' => 1.50]);

echo "✓ Precio de costo actualizado a 1.50\n";
