<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        $productos = [
            'Cuaderno universitario 100 hojas',
            'Cuaderno espiral A4',
            'Libreta pequeña tapa dura',
            'Agenda 2026',
            'Bolígrafo azul',
            'Bolígrafo negro',
            'Lápiz HB',
            'Lápiz 2B',
            'Marcador permanente negro',
            'Marcador resaltador amarillo',
            'Papel bond A4 (resma)',
            'Cartulina blanca',
            'Cartulina de colores',
            'Tijeras escolares',
            'Pegamento en barra',
            'Cinta adhesiva transparente',
            'Regla 30cm',
            'Carpeta manila',
            'Archivador palanca',
            'Clips metálicos caja x100',
            'Grapas estándar caja',
            'Grapadora metálica',
            'Perforadora 2 huecos',
            'Calculadora científica',
            'Temperas x12 colores',
            'Pinceles set x5',
            'Colores x24 unidades',
            'Memoria USB 16GB',
            'Mouse inalámbrico',
            'Teclado USB',
            'Mochila escolar',
            'Lonchera térmica',
        ];

        $marcas = ['Norma', 'Pelikan', 'Bic', 'Faber-Castell', 'Artesco', 'Pilot', 'Stabilo', 'Office Max', 'Kingston', 'Logitech', 'HP'];
        
        $precioUnitario = $this->faker->randomFloat(2, 0.50, 50.00);
        $enOferta = $this->faker->boolean(30);
        $precioOferta = $enOferta ? $precioUnitario * $this->faker->randomFloat(2, 0.70, 0.90) : null;

        return [
            'codigo_barras' => $this->faker->unique()->ean13(),
            'nombre' => $this->faker->randomElement($productos),
            'caracteristicas' => $this->faker->sentence(8),
            'cantidad_stock' => $this->faker->numberBetween(0, 100),
            'stock_minimo' => $this->faker->numberBetween(5, 15),
            'stock_maximo' => $this->faker->numberBetween(50, 150),
            'tiene_iva' => $this->faker->boolean(80),
            'ubicacion' => $this->faker->randomElement(['Estante A', 'Estante B', 'Estante C', 'Bodega 1', 'Bodega 2', 'Vitrina']),
            'precio_unitario' => $precioUnitario,
            'marca' => $this->faker->randomElement($marcas),
            'en_oferta' => $enOferta,
            'precio_oferta' => $precioOferta,
            'categoria_id' => Categoria::inRandomOrder()->first()?->id ?? Categoria::factory(),
        ];
    }
}
