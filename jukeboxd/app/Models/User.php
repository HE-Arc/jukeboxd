<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['pseudo', 'email', 'mot_de_passe'])]
#[Hidden(['mot_de_passe', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'utilisateur';
    protected $primaryKey = 'id_utilisateur';

    /**
     * Indique si le modèle doit gérer les timestamps created_at/updated_at.
     * Le MLD ne définissant pas de timestamps, ils sont désactivés par défaut.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Hash auto du mdp
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'mot_de_passe' => 'hashed',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'mot_de_passe';
    }

    /**
     * Relation de suivi entre les utilisateurs
     */
    public function suivis(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'suivi', 'id_suiveur', 'id_suivi');
    }

    /**
     * Tous les users qui suivent l'utilisateur concerné
     */
    public function suiveurs(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'suivi', 'id_suivi', 'id_suiveur');
    }

    /**
     * Albums évalués ou écoutés
     */
    public function albums(): BelongsToMany
    {
        return $this->belongsToMany(Album::class, 'evalue_ecoute', 'id_utilisateur', 'id_spotify_album')->withPivot(['note', 'est_favori']);
    }
}
