<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_utilisateur', 'id_spotify_album', 'note', 'est_favori'])]
class EvalueEcoute extends Model
{
    use HasFactory;

    protected $table = 'evalue_ecoute';
    public $incrementing = false;
    public $timestamps = false;

    /**
     * Cast de la note et du favori
     */
    protected function casts(): array
    {
        return [
            'note' => 'float',
            'est_favori' => 'boolean',
        ];
    }

    /**
     * Utilisateur ayant évalué ou écouté
     */
    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_utilisateur', 'id_utilisateur');
    }

    /**
     * Album évalué ou écouté
     */
    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class, 'id_spotify_album', 'id_spotify_album');
    }
}
