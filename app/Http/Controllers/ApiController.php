<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Character;

class ApiController extends Controller
{
    public function index()
    {
        // Tüm karakterleri getir
        $characters = Character::all();

        return response()->json([
            'data' => $characters,
            'message' => 'Success',
        ], 200);
    }

    public function show($id)
    {
        // Belirli bir karakteri getir
        $character = Character::find($id);

        if (!$character) {
            return response()->json([
                'message' => 'Character not found',
            ], 404);
        }

        return response()->json([
            'data' => $character,
            'message' => 'Success',
        ], 200);
    }
}
