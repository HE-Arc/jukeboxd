<?php

namespace Tests\Unit;

use App\Models\Album;
use App\Models\Artist;
use App\Services\SpotifyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SpotifyServiceTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_find_or_create_album_fetches_and_stores_artist_and_album(): void
    {
        Http::fake([
            'https://api.spotify.com/v1/albums/album_123' => Http::response([
                'id' => 'album_123',
                'name' => 'Discovery',
                'release_date' => '2001',
                'artists' => [
                    ['id' => 'artist_1', 'name' => 'Daft Punk'],
                ],
                'images' => [
                    ['url' => 'https://example.com/discovery.jpg'],
                ],
            ], 200),
        ]);

        $service = new SpotifyService();
        $album = $service->findOrCreateAlbum('album_123');

        $this->assertNotNull($album);
        $this->assertSame('Discovery', $album->title);
        $this->assertDatabaseHas('artists', [
            'spotify_artist_id' => 'artist_1',
            'name' => 'Daft Punk',
        ]);
        $this->assertDatabaseHas('albums', [
            'spotify_album_id' => 'album_123',
            'title' => 'Discovery',
            'spotify_artist_id' => 'artist_1',
        ]);

        // Second call should return from local DB without calling Spotify API again
        $albumAgain = $service->findOrCreateAlbum('album_123');
        $this->assertSame('album_123', $albumAgain->spotify_album_id);
        Http::assertSentCount(1);
    }
}
