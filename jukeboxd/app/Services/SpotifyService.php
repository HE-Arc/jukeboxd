<?php

namespace App\Services;

use App\Models\Album;
use App\Models\Artist;
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
     * Search for albums on Spotify and normalize the results with automatic caching.
     *
     * @return array<int, array{id: string, title: string, artist: string, cover_url: string|null, year: string|null}>
     */
    public function search(string $query, int $limit = 10): array
    {
        $query = trim($query);

        if (mb_strlen($query) < 2) {
            return [];
        }

        $safeLimit = max(1, min(10, $limit));
        $cacheKey = 'spotify_search_' . md5(mb_strtolower($query)) . "_{$safeLimit}";

        return Cache::remember($cacheKey, now()->addHour(), function () use ($query, $safeLimit) {
            $response = $this->client()->get('/search', [
                'q' => $query,
                'type' => 'album,artist',
                'limit' => $safeLimit,
            ]);

            if ($response->failed()) {
                return [];
            }

            return collect($response->json('albums.items', []))->map(fn (array $album): array => [
                'id' => $album['id'],
                'title' => $album['name'],
                'artist' => collect($album['artists'] ?? [])->pluck('name')->implode(', '),
                'cover_url' => $album['images'][0]['url'] ?? null,
                'year' => !empty($album['release_date']) ? substr($album['release_date'], 0, 4) : null,
            ])->all();
        });
    }

    /**
     * Find an album in local database or fetch it from Spotify and store it
     */
    public function findOrCreateAlbum(string $spotifyAlbumId): ?Album
    {
        $existing = Album::find($spotifyAlbumId);
        if ($existing) {
            return $existing;
        }

        $response = $this->client()->get("/albums/{$spotifyAlbumId}");

        if ($response->failed()) {
            return null;
        }

        $data = $response->json();
        $primaryArtist = $data['artists'][0] ?? null;

        if (!$primaryArtist || empty($data['id'])) {
            return null;
        }

        $artist = Artist::firstOrCreate(
            ['spotify_artist_id' => $primaryArtist['id']],
            ['name' => $primaryArtist['name']]
        );

        $rawDate = $data['release_date'] ?? '1970-01-01';
        $releaseDate = match (strlen($rawDate)) {
            4 => "{$rawDate}-01-01",
            7 => "{$rawDate}-01",
            default => $rawDate,
        };

        return Album::firstOrCreate(
            ['spotify_album_id' => $data['id']],
            [
                'title' => $data['name'],
                'cover_url' => $data['images'][0]['url'] ?? '',
                'release_date' => $releaseDate,
                'spotify_artist_id' => $artist->spotify_artist_id,
            ]
        );
    }
}
