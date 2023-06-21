<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AwsSystemLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CheckClientLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-client-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check clients logs in ML api';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $logs = AwsSystemLog::query()->select([
                'id',
                'client_id',
                'timestamp',
                'process_id',
                'thread_id',
                'parent_process_id',
                'user_id',
                'mount_namespace',
                'process_name',
                'host_name',
                'event_id',
                'event_name',
                'stack_address',
                'args_num',
                'return_value',
                'args',
                'hash']
        )->whereNull('anomaly_detected')->get()->map(
            function (AwsSystemLog $log) {
                return collect($log->toArray())->keyBy(function ($value, $key) {
                    return Str::camel($key);
                });
            }
        )->toArray();

        if (!$logs) {
            $this->info('New logs are not existed');
            return;
        }

        $response = Http::withBody(
            json_encode([
                'logs' => $logs,
                'secret' => env('SECRET_KEY'),
            ], JSON_THROW_ON_ERROR),
            'application/json'
        )->post(env('ML_API_RESOURCE_URL') . '/logs');

        if ($response->successful()) {
            foreach ($response->json('logs') as $log) {
                if (isset($log['id'], $log['outlierAnomalyScore'], $log['anomalyDetected'])) {
                    AwsSystemLog::find($log['id'])->update([
                        'outlier_anomaly_score' => $log['outlierAnomalyScore'],
                        'anomaly_detected' => $log['anomalyDetected'],
                    ]);
                } else {
                    $this->error(sprintf(
                        "Can't load check log result id=%s outlierAnomalyScore=%s anomalyDetected=%s",
                        $log['id'] ?? '',
                        $log['outlierAnomalyScore'] ?? '',
                        $log['anomalyDetected'] ?? ''
                    ));
                }
            }
            $this->info('Result saved');
        }
    }
}
