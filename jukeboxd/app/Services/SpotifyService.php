<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SpotifyService
{
    protected string $authUrl = 'https://accounts.spotify.com/api/token';
    protected string $apiUrl = 'https://api.spotify.com/v1';

    /**
     * Get a Spotify token using the clients credential flow
     * Caches the token to avoid redundant authentication requests.
     */
    public function getAccessToken(): string
    {
        return Cache::remember('spotify_access_token', 3500, function () {
            $clientId = config('services.spotify.client_id');
            $clientSecret = config('services.spotify.client_secret');

            if (empty($clientId) || empty($clientSecret)) {
                throw new Exception('Spotify Client ID or Client Secret is missing in configuration.');
            }

            $response = Http::asForm()
                ->withBasicAuth($clientId, $clientSecret)
                ->post($this->authUrl, [
                    'grant_type' => 'client_credentials',
                ]);

            if ($response->failed()) {
                throw new Exception('Failed to authenticate with Spotify API: ' . $response->body());
            }

            return $response->json('access_token');
        });
    }

    /**
     * Get a pre-configured HTTP client with the Bearer token and base API URL.
     */
    public function client(): PendingRequest
    {
        return Http::withToken($this->getAccessToken())
            ->baseUrl($this->apiUrl);
    }

    /**
     * Search for albums on Spotify and normalize the results
     */
    public function search(string $query, string $type = 'album,artist', int $limit = 6): array
    {
        if (trim($query) === '') {
            return [];
        }

        $response = $this->client()->get('/search', [
            'q' => $query,
            'type' => $type,
            'limit' => $limit,
        ]);

        if ($response->failed()) {
            return [];
        }

        $albums = $response->json('albums.items') ?? [];

        return array_map(function (array $album): array {
            $artists = array_map(fn (array $artist): string => $artist['name'], $album['artists'] ?? []);

            return [
                'id' => $album['id'],
                'title' => $album['name'],
                'artist' => implode(', ', $artists),
                'cover_url' => $album['images'][0]['url'] ?? null,
                'year' => !empty($album['release_date']) ? substr($album['release_date'], 0, 4) : null,
            ];
        }, $albums);
    }
}
