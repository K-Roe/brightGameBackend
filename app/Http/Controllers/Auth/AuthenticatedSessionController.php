<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // Use JsonResponse
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request (LOGIN).
     */
    public function store(LoginRequest $request): JsonResponse
    {
        // 1. Validate credentials
        $request->authenticate();

        // 2. Get the authenticated user
        $user = $request->user();

        // 3. Create a Sanctum Token (This replaces sessions/cookies)
        $token = $user->createToken('game-token')->plainTextToken;

        // 4. Return the token to Expo
        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Destroy an authenticated session (LOGOUT).
     */
    public function destroy(Request $request): JsonResponse
    {
        // 1. Revoke the token that was used for the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}