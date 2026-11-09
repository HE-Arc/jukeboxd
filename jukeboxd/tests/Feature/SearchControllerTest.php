<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('cache.default', 'array');
        Config::set('services.spotify.client_id', 'test_client_id');
        Config::set('services.spotify.client_secret', 'test_client_secret');
        Cache::put('spotify_access_token', 'mock_token', 3600);
    }

    public function test_live_search_returns_empty_array_when_query_is_missing_or_short(): void
    {
        $this->getJson('/search/live')->assertOk()->assertExactJson([]);
        $this->getJson('/search/live?q=a')->assertOk()->assertExactJson([]);
    }

    public function test_live_search_returns_normalized_albums_and_caches_result(): void
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
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        // First call: hits the API
        $response1 = $this->getJson('/search/live?q=Discovery');

        $response1->assertOk()
            ->assertJson([
                [
                    'id' => 'album_123',
                    'title' => 'Discovery',
                    'artist' => 'Daft Punk',
                    'cover_url' => 'https://example.com/discovery_640.jpg',
                    'year' => '2001',
                ],
            ]);

        // Second call with different casing / spaces: should come from cache
        $response2 = $this->getJson('/search/live?q=' . urlencode('  discovery  '));

        $response2->assertOk()
            ->assertJson([
                [
                    'id' => 'album_123',
                    'title' => 'Discovery',
                ],
            ]);

        // Spotify API should only have been called once due to caching
        Http::assertSentCount(1);
    }

    public function test_search_page_loads_with_empty_state(): void
    {
        $response = $this->get('/search');

        $response->assertOk()
            ->assertViewIs('search.index')
            ->assertViewHas('query', '')
            ->assertViewHas('albums', []);
    }

    public function test_search_page_displays_results_for_valid_query(): void
    {
        Http::fake([
            'https://api.spotify.com/v1/search*' => Http::response([
                'albums' => [
                    'items' => [
                        [
                            'id' => 'album_999',
                            'name' => 'Random Access Memories',
                            'release_date' => '2013-05-17',
                            'artists' => [['name' => 'Daft Punk']],
                            'images' => [['url' => 'https://example.com/ram.jpg']],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->get('/search?q=Daft+Punk');

        $response->assertOk()
            ->assertViewIs('search.index')
            ->assertViewHas('query', 'Daft Punk')
            ->assertSee('Random Access Memories')
            ->assertSee('Daft Punk');
    }
}
