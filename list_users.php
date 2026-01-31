<?php
use App\Models\User;
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = User::with('roles')->get();
$data = $users->map(function($u) {
    return [
        'id' => $u->id,
        'email' => $u->email,
        'roles' => $u->roles->pluck('name')
    ];
});
echo json_encode($data, JSON_PRETTY_PRINT);
