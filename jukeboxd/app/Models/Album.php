<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['spotify_album_id', 'title', 'cover_url', 'release_date', 'spotify_artist_id'])]
class Album extends Model
{
    use HasFactory;

    protected $table = 'albums';
    protected $primaryKey = 'spotify_album_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    /**
     * Cast date directly to date format
     */
    protected function casts(): array
    {
        return [
            'release_date' => 'date',
        ];
    }

    /**
     * Artist of the album
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class, 'spotify_artist_id', 'spotify_artist_id');
    }

    /**
     * Users who rated or listened to the album
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ratings', 'spotify_album_id', 'user_id')->withPivot(['rating', 'is_favorite']);
    }
}
