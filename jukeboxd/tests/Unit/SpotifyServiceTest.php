<?php

namespace Tests\Unit;

use App\Services\SpotifyService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SpotifyServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('cache.default', 'array');
        Config::set('services.spotify.client_id', 'test_client_id');
        Config::set('services.spotify.client_secret', 'test_client_secret');
        Cache::put('spotify_access_token', 'mock_token', 3600);
    }

    public function test_search_returns_empty_array_for_blank_query(): void
    {
        $service = new SpotifyService();

        $this->assertSame([], $service->search(''));
        $this->assertSame([], $service->search('   '));
    }

    public function test_search_calls_spotify_and_normalizes_albums(): void
    {
        Http::fake([
            'https://api.spotify.com/v1/search*' => Http::response([
                'albums' => [
                    'items' => [
                        [
                            'id' => 'album_123',
                            'name' => 'Discovery',
                            'release_date' => '2001-03-12',
                            'artists' => [
                                ['name' => 'Daft Punk'],
                            ],
                            'images' => [
                                ['url' => 'https://example.com/discovery_640.jpg'],
                                ['url' => 'https://example.com/discovery_300.jpg'],
                            ],
                        ],
                        [
                            'id' => 'album_456',
                            'name' => 'Collaborations',
                            'release_date' => '2020',
                            'artists' => [
                                ['name' => 'Artist A'],
                                ['name' => 'Artist B'],
                            ],
                            'images' => [],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = new SpotifyService();
        $results = $service->search('Discovery');

        $this->assertCount(2, $results);

        $this->assertSame([
            'id' => 'album_123',
            'title' => 'Discovery',
            'artist' => 'Daft Punk',
            'cover_url' => 'https://example.com/discovery_640.jpg',
            'year' => '2001',
        ], $results[0]);

        $this->assertSame([
            'id' => 'album_456',
            'title' => 'Collaborations',
            'artist' => 'Artist A, Artist B',
            'cover_url' => null,
            'year' => '2020',
        ], $results[1]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'https://api.spotify.com/v1/search')
                && $request['q'] === 'Discovery'
                && $request['type'] === 'album,artist'
                && (int) $request['limit'] === 10;
        });
    }

    public function test_search_returns_empty_array_when_api_fails(): void
    {
        Http::fake([
            'https://api.spotify.com/v1/search*' => Http::response(['error' => 'Unauthorized'], 401),
        ]);

        $service = new SpotifyService();
        $results = $service->search('Discovery');

        $this->assertSame([], $results);
    }
}
