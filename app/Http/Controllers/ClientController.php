<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientStats;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(Client::all()->toArray());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $clientData = $request->validate([
            'active' => ['required', 'boolean'],
            'name' => ['required', 'max:255'],
            'hostname' => ['required', 'max:255'],
            'type' => ['required', 'max:255'],
            'access_token' => ['filled', 'max:255'],
            'last_communication_at' => ['filled', 'date'],
        ]);
        return response()->json(Client::create($clientData));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        return response()->json(Client::find($id)?->toArray());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $clientData = $request->validate([
            'active' => ['filled', 'boolean'],
            'name' => ['filled', 'max:255'],
            'hostname' => ['filled', 'max:255'],
            'type' => ['filled', 'max:255'],
            'access_token' => ['filled', 'max:255'],
            'last_communication_at' => ['filled', 'date'],
        ]);

        $client = Client::find($id);
        if ($client) {
            $client->update($clientData);
            $client->fresh();
        }

        return response()->json($client->toArray());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $client = Client::find($id);
        if ($client === null) {
            return response()->json(['message' => 'Client not found'], 404);
        }
        return response()->json(['result' => $client->delete()]);
    }

    public function dashboard(Request $request, string $id): JsonResponse
    {
        $periodOnDashboard = $request->validate([
            //max - 1 year
            'period_seconds' => ['required', 'integer', 'min:1', 'max:31536000'],
        ]);
        /**
         * @param $client Client
         */
        $client = Client::find($id);
        if ($client === null) {
            return response()->json(['message' => 'Client not found'], 404);
        }

        //load last stat
        $lastLog = $client->lastLog;
        if ($lastLog === null) {
            return response()->json(['message' => 'Logs not found'], 404);
        }
        $fromDatetime = $lastLog->timestamp;
        $fromDatetime->subtract($periodOnDashboard['period_seconds'], 'seconds');

        $clientStats = $client->clientStats()
            ->where('created_at', '>', $fromDatetime)
            ->get()
            ?->map(function (ClientStats $stats) {
                $stats->dateTime = $stats->created_at->toIso8601ZuluString();
                return collect($stats->toArray())->keyBy(function ($value, $key) {
                    return Str::camel($key);
                });
            })->toArray();

        $clientLogs = $client->systemLogs()
            ->select('timestamp')
            ->where('anomaly_detected', '=', 1)
            ->where('timestamp', '>', $fromDatetime)
            ->get();
        $preparedAnomalyLogs = [];
        foreach ($clientLogs as $key => $log) {
            $preparedAnomalyLogs[$key]['timestampMkS'] = $log->timestamp;
        }
        return response()->json([
            'statList' => $clientStats,
            'anomalyList' => $preparedAnomalyLogs,
        ]);
    }
}
