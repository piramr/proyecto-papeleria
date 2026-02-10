<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seeders para datos base
        $this->call([
            TipoPagoSeeder::class,
            CompraSeeder::class,
            RoleAndPermissionSeeder::class,
            SecurityQuestionsSeeder::class,

        ]);

        // User::factory(10)->create();

        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);

        $this->call([
            AuditoriaTipoOperacionSeeder::class,
            LogLoginResultadosSeeder::class,
            LogNivelSeeder::class
        ]);

        // Seeder de inventario (proveedores, categorías, productos)
        // Comentar esta línea si no deseas datos de prueba
        $this->call([
            InventarioSeeder::class,
        ]);
    }
}
