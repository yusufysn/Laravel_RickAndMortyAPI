<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Character;
use App\Models\Location;
use App\Models\Episode;

class CharacterController extends Controller
{
    public function index()
    {
        // Tüm karakterleri saklayacağımız boş bir dizi oluşturuyoruz
        $allCharacters = [];

        // İlk sayfayı alıyoruz
        $response = $this->fetchFromApi('https://rickandmortyapi.com/api/character');
        $allCharacters = array_merge($allCharacters, $response->results);

        // Tüm sayfaları dolaşıyoruz
        while ($response->info->next) {
            $response = $this->fetchFromApi($response->info->next);
            $allCharacters = array_merge($allCharacters, $response->results);
        }

        // Tüm karakterleri veritabanına kaydediyoruz
        $this->saveToDatabase($allCharacters);

        return response()->json($allCharacters);
    }

    private function fetchFromApi($url)
    {
        $client = new Client();
        $response = $client->get($url);
        return json_decode($response->getBody()->getContents());
    }

    private function saveToDatabase($characters)
    {
        foreach ($characters as $char) {
            Character::create([
                'name' => $char->name,
                'status' => $char->status,
                'species' => $char->species,
                'type' => $char->type ?? null,
                'gender' => $char->gender,
                'origin' => $char->origin->name,
                'location' => $char->location->name,
                'image' => $char->image
            ]);
        }
    }

}
