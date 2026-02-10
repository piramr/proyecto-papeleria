<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\ProveedorDireccion;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class InventarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear categorías
        $this->command->info('Creando categorías...');
        $categorias = Categoria::factory(10)->create();
        $this->command->info('✓ 10 categorías creadas');

        // 2. Crear proveedores con sus direcciones
        $this->command->info('Creando proveedores...');
        $proveedores = Proveedor::factory(15)->create()->each(function ($proveedor) {
            // Crear 1-3 direcciones por proveedor
            ProveedorDireccion::factory(rand(1, 3))->create([
                'proveedor_ruc' => $proveedor->ruc,
            ]);
        });
        $this->command->info('✓ 15 proveedores creados con sus direcciones');

        // 3. Crear productos
        $this->command->info('Creando productos...');
        $productos = Producto::factory(50)->create();
        $this->command->info('✓ 50 productos creados');

        // 4. Asociar productos con proveedores (relación many-to-many)
        $this->command->info('Asociando productos con proveedores...');
        foreach ($productos as $producto) {
            // Cada producto tendrá entre 1 y 3 proveedores
            $cantidadProveedores = rand(1, 3);
            $proveedoresSeleccionados = $proveedores->random($cantidadProveedores);

            foreach ($proveedoresSeleccionados as $proveedor) {
                // Precio de costo entre 50% y 80% del precio unitario
                $precioCosto = $producto->precio_unitario * (rand(50, 80) / 100);
                
                $producto->proveedores()->attach($proveedor->ruc, [
                    'precio_costo' => round($precioCosto, 2),
                ]);
            }
        }
        $this->command->info('✓ Productos asociados con proveedores');

        $this->command->info('');
        $this->command->info('=================================================');
        $this->command->info('          INVENTARIO CREADO EXITOSAMENTE         ');
        $this->command->info('=================================================');
        $this->command->info('📦 10 Categorías');
        $this->command->info('🏢 15 Proveedores (con 1-3 direcciones cada uno)');
        $this->command->info('📋 50 Productos (asociados con 1-3 proveedores)');
        $this->command->info('=================================================');
    }
}
