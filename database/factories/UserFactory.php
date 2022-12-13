<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Enums\UserRoles;

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
            'age' => fake()->numberBetween(16, 60),
            'avatar_url' => 'http://gravatar.com/avatar/0dcae7d6d76f9a3b14588e9671c45879?d=identicon&r=pg&s=100',
            'password' => '12345678',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function admin()
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRoles::ADMIN->value,
            'email' => fake()->unique()->userName() . '@admin.com',
            'email_verified_at' => now(),
        ]);
    }
}
