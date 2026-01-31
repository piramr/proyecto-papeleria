<?php

namespace Database\Seeders;

use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Proveedor;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class CompraSeeder extends Seeder {
    /**
     * Run the database seeds.
     * 
     * NOTA: Ejecuta esto solo si tienes proveedores y productos creados
     * php artisan db:seed --class=CompraSeeder
     */
    public function run(): void {
        $proveedores = Proveedor::all();
        
        if ($proveedores->isEmpty()) {
            $this->command->warn('No hay proveedores registrados. Crea proveedores primero.');
            return;
        }

        // Crear 5 compras de ejemplo
        foreach (range(1, 5) as $i) {
            $proveedor = $proveedores->random();
            
            $compra = Compra::create([
                'numero_compra' => Compra::generarNumeroCompra(),
                'fecha_compra' => now()->subDays(rand(1, 30)),
                'proveedor_ruc' => $proveedor->ruc,
                'descripcion' => "Compra de prueba #{$i}",
                'estado' => collect(['pendiente', 'recibida'])->random(),
                'usuario_id' => 1,
                'tipo_pago_id' => rand(1, 4),
            ]);

            // Obtener productos del proveedor
            $productos = \DB::table('productos_proveedores')
                ->where('proveedor_ruc', $proveedor->ruc)
                ->limit(rand(2, 5))
                ->get();

            if ($productos->isNotEmpty()) {
                foreach ($productos as $prod) {
                    $cantidad = rand(1, 10);
                    $precioUnitario = $prod->precio_costo ?? rand(10, 100);
                    $subtotal = $cantidad * $precioUnitario;

                    CompraDetalle::create([
                        'compra_id' => $compra->id,
                        'producto_id' => $prod->producto_id,
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'subtotal' => $subtotal,
                    ]);
                }

                // Calcular totales
                $compra->calcularTotal();
            }
        }

        $this->command->info('Se crearon 5 compras de prueba.');
    }
}
