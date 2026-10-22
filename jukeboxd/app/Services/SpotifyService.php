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
}
