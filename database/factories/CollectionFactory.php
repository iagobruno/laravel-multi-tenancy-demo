<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Collection>
 */
class CollectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $name = str(fake()->unique()->words(rand(1, 2), true))->ucfirst(),
            'slug' => str($name)->slug(),
            'description' => fake()->sentence(10, false),
        ];
    }
}
