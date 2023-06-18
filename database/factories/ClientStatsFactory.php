<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientStats>
 */
class ClientStatsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'cpu' => $this->faker->randomFloat(2, 0, 100),
            'ram' => $this->faker->randomFloat(2, 0, 100),
            'free_ram' => $this->faker->randomFloat(2, 0, 100),
            'network_io' => $this->faker->randomFloat(2, 0, 100),
            'disk_space' => $this->faker->randomFloat(2, 0, 100),
            'disk_io' => $this->faker->randomFloat(2, 0, 100),
        ];
    }
}
