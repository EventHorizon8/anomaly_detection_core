<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AwsSystemLog>
 */
class AwsSystemLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $args = [
            'timestamp' => $this->faker->dateTime->getTimestamp(),
            'process_id' => random_int(0, 99999),
            'thread_id' => random_int(0, 99999),
            'parent_process_id' => random_int(0, 99999),
            'user_id' => random_int(0, 99999),
            'mount_namespace' => random_int(0, 99999),
            'process_name' => $this->faker->text(255),
            'host_name' => $this->faker->ipv4,
            'event_id' => random_int(0, 99999),
            'event_name' => $this->faker->text(255),
            'stack_address' => $this->faker->text(255),
            'args_num' => random_int(0, 99999),
            'return_value' => random_int(0, 99999),
            'args' => json_encode($this->faker->shuffleArray(), JSON_THROW_ON_ERROR),
        ];

        return [
            ...$args,
            'client_id' => Client::factory(),
            'hash' => hash('md5', implode('', $args)),
            'outlier_anomaly_score' => $this->faker->randomFloat(2, 0, 999),
            'anomaly_detected' => random_int(0, 1),
        ];
    }
}
