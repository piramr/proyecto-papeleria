<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de permisos
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // PERMISOS
        $permissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'audit.view',
            'sales.manage',
            'inventory.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // ROLES (con los nombres que YA tienes)
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $empleado = Role::firstOrCreate(['name' => 'Empleado']);
        $auditor = Role::firstOrCreate(['name' => 'Auditor']);

        // ASIGNACIÓN DE PERMISOS
        $admin->syncPermissions($permissions);

        $empleado->syncPermissions([
            'sales.manage',
            'inventory.manage',
        ]);

        $auditor->syncPermissions([
            'users.view',
            'audit.view',
        ]);
    }
}
