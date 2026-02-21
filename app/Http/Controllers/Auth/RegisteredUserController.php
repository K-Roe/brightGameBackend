<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Http\JsonResponse; // Add this at the top



class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */

public function store(Request $request): JsonResponse // Change return type to JsonResponse
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->string('password')),
    ]);

    event(new Registered($user));

    // 1. Log them in (optional for API, but keeps things consistent)
    Auth::login($user);

    // 2. Generate the Sanctum Token
    $token = $user->createToken('main_token')->plainTextToken;

    // 3. Return the user and the token as JSON
    return response()->json([
        'user' => $user,
        'token' => $token,
    ], 201); // 201 means "Created"
}
}
