<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Configuration for the AI automation engine: "when a conversation comes in
 * on this channel/category, generate a draft reply using this prompt."
 * Matched against new conversations in ConversationController::store(); the
 * matching rule is what triggers the GenerateAutomatedReply job.
 */
class AutomationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bot_user_id',
        'canal',
        'categorie',
        'enabled',
        'prompt_template',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
        ];
    }

    // The "author" attributed to AI-generated drafts (a User row with
    // is_bot = true) — lets drafts show up in the UI like any other reply.
    public function bot(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bot_user_id');
    }

    /** La regle s'applique-t-elle a cette conversation (canal/categorie, ou joker si non renseigne) ? */
    public function matches(Conversation $conversation): bool
    {
        return $this->enabled
            && ($this->canal === null || $this->canal === $conversation->canal)
            && ($this->categorie === null || $this->categorie === $conversation->categorie);
    }
}
