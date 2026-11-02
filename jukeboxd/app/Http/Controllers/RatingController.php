<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    /**
     * Add or update rating for an album
     */
    public function rate(Request $request, string $spotify_album_id)
    {
        $validated = $request->validate([
            'rating' => 'required|numeric|min:0|max:5',
        ]);

        $album = Album::findOrFail($spotify_album_id);

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

