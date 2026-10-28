<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_suiveur', 'id_suivi'])]
class Suivi extends Model
{
    use HasFactory;

    protected $table = 'suivi';
    public $incrementing = false;
    public $timestamps = false;

    /**
     * Utilisateur qui suit
     */
    public function suiveur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_suiveur', 'id_utilisateur');
    }

    /**
     * Utilisateur qui est suivi
     */
    public function suivi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_suivi', 'id_utilisateur');
    }
}
