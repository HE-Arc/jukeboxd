<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'spotify_album_id', 'rating', 'is_favorite'])]
class Rating extends Model
{
    use HasFactory;

    protected $table = 'ratings';
    public $incrementing = false;
    public $timestamps = false;

    /**
     * Cast rating and favorite attributes
     */
    protected function casts(): array
    {
        return [
            'rating' => 'float',
            'is_favorite' => 'boolean',
        ];
    }

    /**
     * User who rated or listened
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Album rated or listened to
     */
    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class, 'spotify_album_id', 'spotify_album_id');
    }
}
