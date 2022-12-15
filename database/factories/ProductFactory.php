<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => str(fake()->words(3, true))->ucfirst(),
            // 'slug' => fake()->unique()->slug(3),
            'description' => fake()->paragraphs(2, true),
            'price' => rand(20, 200) * 100,
            'sku' => null,
        ];
    }
}
