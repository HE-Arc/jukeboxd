<?php

namespace App\Http\Controllers;

use App\Services\SpotifyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        return view('search.index', [
            'query' => $query,
            'albums' => $this->spotify->search($query, 10),
        ]);
    }

    /**
     * Real-time search endpoint for the popover.
     */
    public function live(Request $request): JsonResponse
    {
        return response()->json(
            $this->spotify->search((string) $request->input('q', ''), 6)
        );
    }
}
