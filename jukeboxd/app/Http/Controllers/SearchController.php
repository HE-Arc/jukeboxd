<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Services\SpotifyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __construct(
        protected SpotifyService $spotify
    ) {}

    /**
     * Search results page.
     */
    public function index(Request $request): View
    {
        $query = (string) $request->input('q', '');
        $albums = $this->withListenedStatus($this->spotify->search($query, 10));

        return view('search.index', [
            'query' => $query,
            'albums' => $albums,
        ]);
    }

    /**
     * Real-time search endpoint for the popover.
     */
    public function live(Request $request): JsonResponse
    {
        $albums = $this->withListenedStatus(
            $this->spotify->search((string) $request->input('q', ''), 5)
        );

        return response()->json($albums);
    }

    /**
     * Attach is_listened status for the authenticated user.
     */
    private function withListenedStatus(array $albums): array
    {
        if (empty($albums) || !Auth::check()) {
            return array_map(fn (array $album) => [...$album, 'is_listened' => false], $albums);
        }

        $listenedIds = Rating::where('user_id', Auth::id())
            ->whereIn('spotify_album_id', array_column($albums, 'id'))
            ->pluck('spotify_album_id')
            ->all();

        $listenedMap = array_flip($listenedIds);

        return array_map(fn (array $album) => [
            ...$album,
            'is_listened' => isset($listenedMap[$album['id']]),
        ], $albums);
    }
}
