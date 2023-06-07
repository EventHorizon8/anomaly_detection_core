<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function logoutUser(Request $request): JsonResponse
    {
        return response()->json(['result' => $request->user()->currentAccessToken()->delete()]);
    }
}
