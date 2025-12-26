<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class JamendoService
{
    protected $clientId;
    protected $baseUrl = 'https://api.jamendo.com/v3.0';

    public function __construct()
    {
        $this->clientId = config('services.jamendo.client_id');
    }

    public function searchTracks($query, $limit = 20)
    {
        $cacheKey = "jamendo_search_{$query}_{$limit}";

        return Cache::remember($cacheKey, 3600, function () use ($query, $limit) {
            $response = Http::get("{$this->baseUrl}/tracks/", [
                'client_id' => $this->clientId,
                'format' => 'json',
                'limit' => $limit,
                'search' => $query,
                'audioformat' => 'mp32',
                'include' => 'musicinfo'
            ]);

            return $response->json()['results'] ?? [];
        });
    }

    public function getTracksByGenre($genre, $limit = 20)
    {
        $cacheKey = "jamendo_genre_{$genre}_{$limit}";

        return Cache::remember($cacheKey, 3600, function () use ($genre, $limit) {
            $response = Http::get("{$this->baseUrl}/tracks/", [
                'client_id' => $this->clientId,
                'format' => 'json',
                'limit' => $limit,
                'tags' => $genre,
                'audioformat' => 'mp32',
                'include' => 'musicinfo'
            ]);

            return $response->json()['results'] ?? [];
        });
    }

    public function getPopularTracks($limit = 20)
    {
        $cacheKey = "jamendo_popular_{$limit}";

        return Cache::remember($cacheKey, 3600, function () use ($limit) {
            $response = Http::get("{$this->baseUrl}/tracks/", [
                'client_id' => $this->clientId,
                'format' => 'json',
                'limit' => $limit,
                'order' => 'popularity_week',
                'audioformat' => 'mp32',
                'include' => 'musicinfo'
            ]);

            return $response->json()['results'] ?? [];
        });
    }

    public function getTrackById($trackId)
    {
        $response = Http::get("{$this->baseUrl}/tracks/", [
            'client_id' => $this->clientId,
            'format' => 'json',
            'id' => $trackId,
            'audioformat' => 'mp32',
            'include' => 'musicinfo'
        ]);

        $results = $response->json()['results'] ?? [];
        return $results[0] ?? null;
    }
}
