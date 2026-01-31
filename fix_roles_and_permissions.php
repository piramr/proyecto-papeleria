<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting Role & Permission Fix...\n";

// 1. Reset Cache
app(PermissionRegistrar::class)->forgetCachedPermissions();

// 2. Define Permissions
$permissions = [
    'users.view', 'users.create', 'users.edit', 'users.delete',
    'audit.view',
    'sales.manage',
    'inventory.manage',
];

foreach ($permissions as $permission) {
    Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
}
echo "Permissions ensured.\n";

// 3. Define Roles
$adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
$auditorRole = Role::firstOrCreate(['name' => 'Auditor', 'guard_name' => 'web']);
$empleadoRole = Role::firstOrCreate(['name' => 'Empleado', 'guard_name' => 'web']);

// 4. Assign Permissions to Roles
$adminRole->syncPermissions($permissions); // Admin gets all
$empleadoRole->syncPermissions(['sales.manage', 'inventory.manage']);
$auditorRole->syncPermissions(['users.view', 'audit.view']);
echo "Roles configured.\n";

// 5. Assign Roles to Users
$users = User::all();
foreach ($users as $user) {
    $email = strtolower($user->email);
    $roleAssigned = false;

    if (str_contains($email, 'admin')) {
        $user->syncRoles(['Admin']);
        $roleAssigned = 'Admin';
    } elseif (str_contains($email, 'auditor')) {
        $user->syncRoles(['Auditor']);
        $roleAssigned = 'Auditor';
    } elseif (str_contains($email, 'empleado') || str_contains($email, 'employee')) {
        $user->syncRoles(['Empleado']);
        $roleAssigned = 'Empleado';
    }
    
    // Fallback: If User ID is 20 (the one from logs), force Admin if no other verified role
    if ($user->id == 20 && !$roleAssigned) {
         $user->syncRoles(['Admin']);
         $roleAssigned = 'Admin (Forced by ID 20)';
    }

    if ($roleAssigned) {
        echo "User {$user->id} ({$user->email}) -> Assigned: $roleAssigned\n";
    } else {
        echo "User {$user->id} ({$user->email}) -> No matching role pattern found.\n";
    }
}

file_put_contents('fix_output.txt', ob_get_contents()); // Capture buffer just in case
echo "Done.\n";
