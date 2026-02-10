<?php

namespace Database\Factories;

use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProveedorFactory extends Factory
{
    protected $model = Proveedor::class;

    public function definition(): array
    {
        return [
            'ruc' => $this->faker->unique()->numerify('##########'), // 10 dígitos para RUC ecuatoriano
            'nombre' => $this->faker->company() . ' ' . $this->faker->randomElement(['S.A.', 'Ltda.', 'Cía.', 'Corp.']),
            'email' => $this->faker->unique()->companyEmail(),
            'telefono_principal' => $this->faker->numerify('09########'), // 10 dígitos
            'telefono_secundario' => $this->faker->boolean(60) ? $this->faker->numerify('09########') : null,
        ];
    }
}
