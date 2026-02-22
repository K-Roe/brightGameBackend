<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Character;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CharacterController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validation
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'hero_class'     => 'required|string',
            'str'            => 'required|integer',
            'dex'            => 'required|integer',
            'agi'            => 'required|integer',
            'int'            => 'required|integer',
            'height'         => 'required|numeric',
            'current_weight' => 'required|numeric',
            'items'          => 'required|array' // Pass the starting items from React
        ]);

        // 2. Create the Character linked to the logged-in user
        // Eloquent's way of saying $character->setUser($user)
        $character = Auth::user()->character()->create([
            'name'            => $validated['name'],
            'hero_class'      => $validated['hero_class'],
            'str'             => $validated['str'],
            'dex'             => $validated['dex'],
            'agi'             => $validated['agi'],
            'int'             => $validated['int'],
            'height'          => $validated['height'],
            'starting_weight' => $validated['current_weight'],
            'current_weight'  => $validated['current_weight'],
        ]);

        // 3. Save Starter Gear
        // We map the React items to the DB schema columns
        $starterItems = collect($validated['items'])->map(function ($item) {
            return [
                'name'        => $item['name'],
                'description' => $item['description'],
                'icon'        => $item['icon'] ?? '🛡️',
                'slot'        => $this->determineSlot($item['name']),
            ];
        });

        $character->items()->createMany($starterItems->toArray());

        return response()->json([
            'message'   => 'Hero created!',
            'character' => $character->load('items')
        ], 201);
    }

    // Quick helper to categorize items
    private function determineSlot($name) {
        if (str_contains($name, 'Robe') || str_contains($name, 'Plate') || str_contains($name, 'Tunic')) return 'armor';
        return 'weapon';
    }

   public function show()
{
    // Simply return the character with their items
    $character = Auth::user()->character()->with('items')->first();

    if (!$character) {
        return response()->json(['message' => 'No hero found'], 404);
    }

    return response()->json($character);
}


}