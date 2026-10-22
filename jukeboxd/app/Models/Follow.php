<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['follower_id', 'followed_id'])]
class Follow extends Model
{
    use HasFactory;

    protected $table = 'follows';
    public $incrementing = false;
    public $timestamps = false;

    /**
     * User who is following
     */
    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follower_id', 'id');
    }

    /**
     * User who is followed
     */
    public function followed(): BelongsTo
    {
        return $this->belongsTo(User::class, 'followed_id', 'id');
    }
}
