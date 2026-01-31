<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = User::with('roles')->get();

echo "Total Users: " . $users->count() . "\n";
foreach ($users as $user) {
    echo "ID: {$user->id} | Name: {$user->name} | Email: {$user->email} | Roles: " . $user->roles->pluck('name')->implode(', ') . "\n";
}

$roles = Role::all();
echo "\nAvailable Roles:\n";
foreach ($roles as $role) {
    echo " - {$role->name} (" . $role->permissions->pluck('name')->implode(', ') . ")\n";
}
