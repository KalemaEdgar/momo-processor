<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'client_name' => fake()->name(),
            'description' => fake()->text(30),
            'email' => fake()->unique()->safeEmail(),
            'client_id' => fake()->unique()->randomNumber(6, true),
            'ova' => 'OVA' . fake()->unique()->randomNumber(6, true),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'phone' => fake()->phoneNumber(),
            'created_by' => 'admin',
            'approved' => true,
            'approved_by' => 'admin',
            'approved_at' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function blocked()
    {
        return $this->state(function (array $attributes) {
            return [
                'blocked' => true,
                'blocked_by' => 'edgar',
                'blocked_at' => now(),
            ];
        });
    }
}
