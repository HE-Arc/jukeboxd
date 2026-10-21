<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['id_spotify_album', 'titre', 'pochette_url', 'date_sortie', 'id_spotify_artiste'])]
class Album extends Model
{
    use HasFactory;

    /**
     * Nom de la table associée au modèle selon le MLD.
     *
     * @var string
     */
    protected $table = 'album';

    /**
     * Clé primaire associée à la table.
     *
     * @var string
     */
    protected $primaryKey = 'id_spotify_album';

    /**
     * Indique si la clé primaire est auto-incrémentée.
     * Dans le MLD, il s'agit d'un VARCHAR (identifiant Spotify).
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Le type de données de la clé primaire.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indique si le modèle doit gérer les timestamps created_at/updated_at.
     * Le MLD ne définissant pas de colonnes timestamps, ils sont désactivés.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Attributs devant être convertis/castés.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_sortie' => 'date',
        ];
    }

    /**
     * Artiste ayant composé l'album (relation "A composé" -> Artiste).
     */
    public function artiste(): BelongsTo
    {
        return $this->belongsTo(Artiste::class, 'id_spotify_artiste', 'id_spotify_artiste');
    }

    /**
     * Utilisateurs ayant évalué ou écouté cet album (relation "Concerne" -> Evalue_Ecoute).
     */
    public function utilisateurs(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'evalue_ecoute', 'id_spotify_album', 'id_utilisateur')
            ->withPivot(['note', 'est_favori']);
    }
}
