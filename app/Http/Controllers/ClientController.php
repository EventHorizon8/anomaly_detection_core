<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
}
