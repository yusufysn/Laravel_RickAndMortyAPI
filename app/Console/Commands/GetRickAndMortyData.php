<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Character;

class GetRickAndMortyData extends Command
{
    protected $signature = 'rickandmorty:getdata';
    protected $description = 'Fetches character data from the Rick and Morty API and stores it in the database';

    public function handle()
    {
        $baseUrl = 'https://rickandmortyapi.com/api/character';
        $response = Http::get($baseUrl);

        if ($response->ok()) {
            $info = $response->json()['info'];
            $pages = $info['pages'];
            $characters = $response->json()['results'];

            // İlk sayfa işlendi, şimdi diğer sayfaları işle
            for ($i = 2; $i <= $pages; $i++) {
                $response = Http::get($baseUrl . '?page=' . $i);
                if ($response->ok()) {
                    $characters = array_merge($characters, $response->json()['results']);
                }
            }

            // Veritabanına kaydet
            foreach ($characters as $character) {
                Character::updateOrCreate(
                    ['id' => $character['id']],
                    [
                        'name' => $character['name'],
                        'status' => $character['status'],
                        'species' => $character['species'],
                        'type' => $character['type'] ?? null,
                        'gender' => $character['gender'],
                        'origin' => $character['origin']['name'],
                        'location' => $character['location']['name'],
                        'image' => $character['image']
                    ]
                );
            }

            $this->info('Character data has been fetched and stored successfully.');
        } else {
            $this->error('Failed to fetch character data from the API.');
        }
    }
}
