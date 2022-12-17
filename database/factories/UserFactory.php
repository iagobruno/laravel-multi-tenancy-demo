<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'avatar_url' => 'http://gravatar.com/avatar/0dcae7d6d76f9a3b14588e9671c45879?d=identicon&r=pg&s=100',
            'password' => '12345678',
            'remember_token' => str()->random(10),
        ];
    }
}
