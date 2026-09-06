<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/** A reusable, color-coded label agents can attach to conversations (many-to-many, no owner). */
class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
    ];

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class);
    }
}
