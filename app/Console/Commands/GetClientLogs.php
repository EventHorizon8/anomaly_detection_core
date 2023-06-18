<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AwsSystemLog;
use App\Models\Client;
use App\Models\ClientStats;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GetClientLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-client-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get client logs';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        foreach (Client::all() as $client) {
            DB::transaction(function () use ($client) {
                $response = Http::get($client->hostname . '/data', [
                    'access_token' => $client->access_token,
                    'last_ts' => $client->lastLog?->timestamp?->getTimestamp() ?? 0,
                    'last_hash' => $client->lastLog?->hash ?? '',
                ]);
                $logs = [
                    [
                        "timestamp"=> 129.050634,
        "processId"=> 382,
        "threadId"=> 382,
        "parentProcessId"=> 1,
        "userId"=> 101,
        "mountNamespace"=> 4026532232,
        "processName"=> "systemd-resolve",
        "hostName"=> "ip-10-100-1-217",
        "eventId"=> 41,
        "eventName"=> "socket",
        "stackAddresses"=> [
                    140159195621643,
                    140159192455417,
                    94656731598592
                ],
        "argsNum"=> 3,
        "returnValue"=> 15,
        "args"=> "[{'name': 'domain', 'type': 'int', 'value': 'AF_UNIX'}, {'name': 'type', 'type': 'int', 'value': 'SOCK_DGRAM|SOCK_CLOEXEC'}, {'name': 'protocol', 'type': 'int', 'value': 0}]",
                    ]
                ];
                if ($response->successful()) {
                    $client->last_communication_at = now();
                    $client->save(['last_communication_at']);
                    //$logs = $response->json('logs') ?? [];
                    if ($logs) {
                        foreach ($logs as $log) {
                            AwsSystemLog::create([
                                'client_id' => $client->id,
                                'timestamp' => $log['timestamp'] ?? 0,
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
                            ]);
                        }
                    }
                    $logType = $response->json('logsType');
                    $stats = $response->json('stats');
                    if ($stats) {
                        ClientStats::create(collect($stats)->keyBy(function ($value, $key) {
                            return Str::snake($key);
                        })->toArray());
                    }
                } else {
                    $this->error("Can't connect to client hostname {$client->hostname} id {$client->id}");
                }
            });
        }
    }
}
