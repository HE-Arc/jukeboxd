<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Services\SpotifyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function __construct(
        protected SpotifyService $spotify
    ) {}

    /**
     * Toggle listened status for an album.
     */
    public function toggleListened(string $spotify_album_id): JsonResponse
    {
        $query = Rating::where('user_id', Auth::id())
            ->where('spotify_album_id', $spotify_album_id);

        if ($query->exists()) {
            $query->delete();

            return response()->json(['listened' => false]);
        }

        $album = $this->spotify->findOrCreateAlbum($spotify_album_id) ?? abort(404);

        Rating::create([
            'user_id' => Auth::id(),
            'spotify_album_id' => $album->spotify_album_id,
            'rating' => 0,
            'is_favorite' => false,
        ]);

        return response()->json(['listened' => true]);
    }

    /**
     * Add or update rating for an album
     */
    public function rate(Request $request, string $spotify_album_id)
    {
        $validated = $request->validate([
            'rating' => 'required|numeric|min:0|max:5',
        ]);

        $album = $this->spotify->findOrCreateAlbum($spotify_album_id) ?? abort(404);

        Rating::updateOrInsert(
            [
                'user_id' => Auth::id(),
                'spotify_album_id' => $album->spotify_album_id,
            ],
            [
                'rating' => $validated['rating'],
            ]
        );

        return back();//->with('success', 'Note enregistrée !');
    }
}

