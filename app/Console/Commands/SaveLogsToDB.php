<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SaveLogsToDB extends Command
{
    private const MAX_BATCH_ITEMS_COUNT = 1000;
    private const MAX_HOURS_BEFORE_DATE = 24;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:save-logs-to-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load logs to save into db';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $filePath = storage_path(env('LOGS_DATA'));

        $client = Client::all()->first();

        $scriptStartAt = Carbon::now();
        $this->info("Fill logs before datetime: {$scriptStartAt->toIso8601ZuluString('milliseconds')}");

        $saveLogsStartDateTime = Carbon::now()->subtract('hours', self::MAX_HOURS_BEFORE_DATE);
        $startedAtTimestamp  = $saveLogsStartDateTime->getTimestamp();

        $this->info("Save log at timestamp: {$saveLogsStartDateTime->toIso8601ZuluString('milliseconds')}");
        $this->info('Start time: '. $scriptStartAt->toIso8601ZuluString('milliseconds'));

        while ($startedAtTimestamp < $scriptStartAt->getTimestamp()) {
            $this->info("Open file {$filePath}");

            $file = fopen($filePath, 'rb');
            $header = fgetcsv($file);
            $currentBatchItem = 0;
            $batchNumber = 0;
            $batchLogs = [];
            while ($row = fgetcsv($file)) {
                $log = array_combine($header, $row);

                $batchLogs[] = [
                    'client_id' => $client->id,
                    'timestamp' => Carbon::createFromTimestamp($startedAtTimestamp + ($log['timestamp'] ?? 0))->format('Y-m-d H:i:s.u'),
                    'process_id' => $log['processId'] ?? 0,
                    'thread_id' => $log['threadId'] ?? 0,
                    'parent_process_id' => $log['parentProcessId'] ?? 0,
                    'user_id' => $log['userId'] ?? 0,
                    'mount_namespace' => $log['mountNamespace'] ?? 0,
                    'process_name' => $log['processName'] ?? '',
                    'host_name' => $log['hostName'] ?? '',
                    'event_id' => $log['eventId'] ?? 0,
                    'event_name' => $log['eventName'] ?? '',
                    'stack_address' => json_encode($log['stackAddresses'] ?? [], JSON_THROW_ON_ERROR),
                    'args_num' => $log['argsNum'] ?? '',
                    'return_value' => $log['returnValue'] ?? '',
                    'args' => $log['args'] ?? '',
                    'hash' => $log['hash'] ?? '',
                    'anomaly_detected' => $log['sus'] ?? '',
                ];

                if ($currentBatchItem >= self::MAX_BATCH_ITEMS_COUNT) {
                    ++$batchNumber;
                    $this->info("Current batch: {$batchNumber}");
                    DB::table('aws_system_logs')->insert($batchLogs);
                    $currentBatchItem = 0;
                    $batchLogs = [];
                    break ;
                }
                ++$currentBatchItem;
            }
            $endTime = Carbon::now();
            $this->info('One file End time: '. $endTime->toIso8601ZuluString('milliseconds'));
            fclose($file);

            //start at lastlog timestamp
            $lastLog = $client->lastLog()->first();
            $lastLogDatTime = Carbon::create($lastLog->timestamp);
            $startedAtTimestamp = $lastLogDatTime->getTimestamp() - 120;
            $this->info("Last log at: {$lastLogDatTime->toIso8601ZuluString('milliseconds')}");
        }
        $this->info('One file Execution time: '. $scriptStartAt->diff($endTime)->format('%Y-%M-%D\T%H:%I:%S.%F\Z'));

       $this->info('Logs saved');
    }
}
