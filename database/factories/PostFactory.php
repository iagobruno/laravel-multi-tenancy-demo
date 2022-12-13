<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->sentence(),
            'slug' => fake()->unique()->slug(),
            'content' => fake()->paragraphs(5, true),
        ];
    }

    public function setStatusRandomly()
    {
        $possibleStatuses = ['draft', 'published', 'scheduled', 'trashed'];
        $status = $possibleStatuses[array_rand($possibleStatuses)];
        return $this->{$status}();
    }

    public function published()
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => now()
        ]);
    }

    public function draft()
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => null
        ]);
    }

    public function scheduled()
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => now()->addDays(7),
        ]);
    }

    public function trashed()
    {
        return $this->state(fn (array $attributes) => [
            'deleted_at' => now(),
        ]);
    }

    public function setStatus($state)
    {
        return $this->{$state}();
    }
}
