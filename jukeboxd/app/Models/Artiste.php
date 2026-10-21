<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['id_spotify_artiste', 'nom'])]
class Artiste extends Model
{
    use HasFactory;

    protected $table = 'artiste';
    protected $primaryKey = 'id_spotify_artiste';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    /**
     * Albums composés par l'artiste
     */
    public function albums(): HasMany
    {
        return $this->hasMany(Album::class, 'id_spotify_artiste', 'id_spotify_artiste');
    }
}
