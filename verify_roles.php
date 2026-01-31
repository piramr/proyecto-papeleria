<?php

use Illuminate\Contracts\Console\Kernel;
use App\Models\User;

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

$admin = User::where('email', 'admin@papeleria.com')->first();
$empleado = User::where('email', 'empleado@papeleria.com')->first();
$auditor = User::where('email', 'auditor@papeleria.com')->first();

echo "Admin: " . ($admin?->hasRole('Admin') ? 'OK' : 'FAIL') . "\n";
echo "Empleado: " . ($empleado?->hasRole('Empleado') ? 'OK' : 'FAIL') . "\n";
echo "Auditor: " . ($auditor?->hasRole('Auditor') ? 'OK' : 'FAIL') . "\n";

