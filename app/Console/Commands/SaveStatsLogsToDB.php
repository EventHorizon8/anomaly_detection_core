<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SaveStatsLogsToDB extends Command
{
    private const MAX_HOURS_BEFORE_DATE = 24;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:save-stats-logs-to-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //save logs which load by every minute from now to first log
        $client = Client::all()->first();
        $scriptStartAt = Carbon::now();
        $saveLogsStartDateTime = Carbon::now()->subtract('hours', self::MAX_HOURS_BEFORE_DATE);
        $startedAtTimestamp = $saveLogsStartDateTime->getTimestamp();

        $this->info("Save log at timestamp: {$saveLogsStartDateTime->toIso8601ZuluString('milliseconds')}");
        $this->info('Start time: ' . $scriptStartAt->toIso8601ZuluString('milliseconds'));

        $preparedLogs = [];
        while ($startedAtTimestamp < $scriptStartAt->getTimestamp()) {
            $ram = 1400 + random_int(0, 50);
            $preparedLogs[] = [
                'client_id' => $client->id,
                'cpu' => 50 + random_int(0, 15),
                'ram' => $ram,
                'free_ram' => 16000 - $ram,
                'network_io' => random_int(0, 50),
                'disk_space' => 12.3,
                'disk_io' => random_int(0, 10),
                'created_at' => Carbon::createFromTimestamp($startedAtTimestamp + ($log['timestamp'] ?? 0))->format('Y-m-d H:i:s'),
            ];
            $startedAtTimestamp += 60;
        }

        DB::table('client_stats')->insert($preparedLogs);
    }
}
