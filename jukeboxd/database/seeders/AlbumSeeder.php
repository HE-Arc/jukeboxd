<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Artist;
use Illuminate\Database\Seeder;

class AlbumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $artist = Artist::firstOrCreate(
            ['spotify_artist_id' => '5K4W6rqBFWDnAN6FQUkS6x'],
            [
                'name' => 'Kanye West',
            ]
        );

        Album::firstOrCreate(
            ['spotify_album_id' => '0XTAmejG8F97wF5MWoVbaY'],
            [
                'title' => 'Yeezus',
                'cover_url' => 'https://i.scdn.co/image/ab67616d0000b2731dacfbc31cc873d132958af9',
                'release_date' => '2013-06-18',
                'spotify_artist_id' => $artist->spotify_artist_id,
            ]
        );
    }
}

