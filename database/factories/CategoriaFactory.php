<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoriaFactory extends Factory
{
    protected $model = Categoria::class;

    public function definition(): array
    {
        $categorias = [
            ['nombre' => 'Cuadernos y Libretas', 'descripcion' => 'Cuadernos universitarios, espirales, libretas, agendas'],
            ['nombre' => 'Bolígrafos y Lápices', 'descripcion' => 'Lapiceros, lápices de grafito, portaminas, marcadores'],
            ['nombre' => 'Papel y Hojas', 'descripcion' => 'Hojas bond, papel fotográfico, cartulinas, papel craft'],
            ['nombre' => 'Material Escolar', 'descripcion' => 'Tijeras, pegamento, cintas adhesivas, reglas'],
            ['nombre' => 'Archivadores y Carpetas', 'descripcion' => 'Carpetas manila, archivadores, folders, separadores'],
            ['nombre' => 'Oficina', 'descripcion' => 'Clips, grapas, grapadoras, perforadoras, calculadoras'],
            ['nombre' => 'Arte y Manualidades', 'descripcion' => 'Pinturas, pinceles, cartulinas de colores, temperas'],
            ['nombre' => 'Tecnología', 'descripcion' => 'Memorias USB, mouse, teclados, audífonos'],
            ['nombre' => 'Mochilas y Bolsos', 'descripcion' => 'Mochilas escolares, maletines, loncheras'],
            ['nombre' => 'Organización', 'descripcion' => 'Bandejas, portapapeles, organizadores de escritorio'],
        ];

        static $index = 0;
        
        if ($index < count($categorias)) {
            $categoria = $categorias[$index];
            $index++;
            return $categoria;
        }

        return [
            'nombre' => $this->faker->unique()->words(2, true),
            'descripcion' => $this->faker->sentence(),
        ];
    }
}
