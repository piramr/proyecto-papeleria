<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Producto;

$producto = Producto::with('proveedores')->first();

if ($producto) {
    echo "Producto: " . $producto->nombre . "\n";
    echo "Proveedores:\n";
    foreach ($producto->proveedores as $proveedor) {
        echo "  - " . $proveedor->nombre . " (RUC: " . $proveedor->ruc . ")\n";
        echo "    Precio de costo: " . $proveedor->pivot->precio_costo . "\n";
    }
} else {
    echo "No hay productos\n";
}
