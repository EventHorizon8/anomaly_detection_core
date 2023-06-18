<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         $clients = \App\Models\Client::factory(5)->create();
         foreach ($clients as $client) {
             \App\Models\AwsSystemLog::factory(10)->create();
             \App\Models\ClientStats::factory(20)->create();
         }


        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
