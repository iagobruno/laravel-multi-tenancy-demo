<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->randomElement([
                'Azul',
                'Preto',
                'Vermelho',
                'Branco',
                'Amarelo',
                'Verde',
                'Rosa',
                'P',
                'M',
                'G',
                'GG',
            ]),
            'price' => rand(20, 100) * 100,
            'stock' => rand(0, 50),
        ];
    }
}
