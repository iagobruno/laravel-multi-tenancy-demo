<?php

namespace Database\Factories;

use App\Models\ProductVariant;
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
            'image_path' => 'https://demo.vercel.store/_next/image?url=https%3A%2F%2Fcdn.shopify.com%2Fs%2Ffiles%2F1%2F0434%2F0285%2F4564%2Fproducts%2FQZSide-Model.png%3Fv%3D1623256247&w=640&q=85',
            'description' => fake()->paragraphs(2, true),
            'price' => rand(20, 200) * 100,
            'sku' => '12345678',
            'barcode' => 'ABCDEFGH',
            'shippable' => true,
            'returnable' => false,
        ];
    }

    public function withVariants(int $count)
    {
        return $this
            ->has(ProductVariant::factory($count), 'variants')
            ->state(fn(array $attributes) => [
                'has_variants' => true,
            ]);
    }
}
