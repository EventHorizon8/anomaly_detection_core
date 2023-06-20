<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;

class CreateClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-client {name} {hostname} {type} {accessToken}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create client';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $client = Client::create([
            'active' => 1,
            'name' => $this->argument('name'),
            'hostname' => $this->argument('hostname'),
            'type' => $this->argument('type'),
            'access_token' => $this->argument('accessToken'),
        ]);
        $this->info("Client saved id={$client->id}");
    }
}
