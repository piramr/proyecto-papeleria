<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Eliminar relaciones en producto_proveedores
    DB::table('producto_proveedores')->delete();
    
    // Eliminar productos
    DB::table('productos')->delete();
    
    echo "✓ Todos los productos han sido eliminados\n";
    
} catch (\Exception $e) {
    echo "✗ Error al eliminar: " . $e->getMessage() . "\n";
}
