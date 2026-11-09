<?php

namespace App\Http\Controllers;

use App\Services\SpotifyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SearchController extends Controller
{
    public function __construct(
        protected SpotifyService $spotifyService
    ) {}

    /**
     * Live search endpoint returning JSON for the search popover.
     */
    public function live(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:2'],
        ]);

        $query = trim($validated['q']);
        $cacheKey = 'spotify_search_' . md5(mb_strtolower($query));

        $results = Cache::remember($cacheKey, now()->addHour(), function () use ($query) {
            return $this->spotifyService->search($query);
        });

        return response()->json($results);
    }
}

