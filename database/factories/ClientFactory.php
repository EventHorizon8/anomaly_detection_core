<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'active' => true,
            'name' => fake()->name(),
            'hostname' => fake()->url,
            'type' => fake()->text(6),
            'access_token' => fake()->iosMobileToken,
            'last_communication_at' => fake()->dateTime,
        ];
    }
}
