<?php

namespace Database\Factories;

use App\Models\ProveedorDireccion;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProveedorDireccionFactory extends Factory
{
    protected $model = ProveedorDireccion::class;

    public function definition(): array
    {
        $provincias = ['Pichincha', 'Guayas', 'Azuay', 'Manabí', 'El Oro', 'Tungurahua', 'Los Ríos', 'Esmeraldas', 'Imbabura', 'Loja'];
        $provincia = $this->faker->randomElement($provincias);
        
        $ciudades = [
            'Pichincha' => ['Quito', 'Cayambe', 'Sangolquí', 'Machachi'],
            'Guayas' => ['Guayaquil', 'Durán', 'Samborondón', 'Daule'],
            'Azuay' => ['Cuenca', 'Gualaceo', 'Paute'],
            'Manabí' => ['Manta', 'Portoviejo', 'Bahía de Caráquez'],
            'El Oro' => ['Machala', 'Huaquillas', 'Pasaje'],
        ];
        
        $ciudad = isset($ciudades[$provincia]) 
            ? $this->faker->randomElement($ciudades[$provincia]) 
            : $this->faker->city();

        return [
            'provincia' => $provincia,
            'ciudad' => $ciudad,
            'calle' => $this->faker->streetName() . ' y ' . $this->faker->streetName(),
            'referencia' => $this->faker->randomElement([
                'Cerca del parque central',
                'Frente al centro comercial',
                'A dos cuadras de la gasolinera',
                'Junto al supermercado',
                'Esquina con la avenida principal',
                'Al lado del banco',
            ]),
        ];
    }
}
