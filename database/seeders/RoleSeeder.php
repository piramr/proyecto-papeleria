<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions (Placeholder for now, can expand later)
        // Permission::create(['name' => 'gestion usuarios']);
        // Permission::create(['name' => 'ver auditoria']);

        // Create Roles
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Empleado']);
        Role::firstOrCreate(['name' => 'Auditor']);

        // Assign permissions to roles can be done here
        // $role = Role::findByName('Admin');
        // $role->givePermissionTo(Permission::all());
    }
}
