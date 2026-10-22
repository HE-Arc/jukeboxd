<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['spotify_artist_id', 'name'])]
class Artist extends Model
{
    use HasFactory;

    protected $table = 'artists';
    protected $primaryKey = 'spotify_artist_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    /**
     * Albums composed by the artist
     */
    public function albums(): HasMany
    {
        return $this->hasMany(Album::class, 'spotify_artist_id', 'spotify_artist_id');
    }
}
