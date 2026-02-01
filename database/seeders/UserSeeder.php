<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        $admin = User::firstOrCreate(
            ['email' => 'viwic25197@juhxs.com'],
            [
                'nombres' => 'Administrador',
                'apellidos' => 'Sistema',
                'cedula' => '0000000001',
                'telefono' => '0999999999',
                'genero' => 'Otro',
                'fecha_nacimiento' => '1990-01-01',
                'direccion' => 'Oficina Central',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('Admin');

        // Employee User
        $empleado = User::firstOrCreate(
            ['email' => 'empleado@papeleria.com'],
            [
                'nombres' => 'Juan',
                'apellidos' => 'Perez',
                'cedula' => '0000000002',
                'telefono' => '0988888888',
                'genero' => 'Masculino',
                'fecha_nacimiento' => '1995-05-15',
                'direccion' => 'Calle 123',
                'password' => Hash::make('password'),
            ]
        );
        $empleado->assignRole('Empleado');

        // Auditor User
        $auditor = User::firstOrCreate(
            ['email' => 'auditor@papeleria.com'],
            [
                'nombres' => 'Maria',
                'apellidos' => 'Gomez',
                'cedula' => '0000000003',
                'telefono' => '0977777777',
                'genero' => 'Femenino',
                'fecha_nacimiento' => '1985-10-20',
                'direccion' => 'Av. Principal',
                'password' => Hash::make('password'),
            ]
        );
        $auditor->assignRole('Auditor');
    }
}
