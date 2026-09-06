<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** An internal note on a conversation — visible to staff only, never to the client. */
class ConversationNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'contenu',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
