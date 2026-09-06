<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * The end customer on the other side of a conversation. The four *_id/handle
 * columns identify the client on each external channel (WhatsApp, Instagram,
 * Messenger, Telegram) so an inbound message can be matched to the right
 * client record instead of creating a duplicate every time.
 */
class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'nom',
        'prenom',
        'email',
        'telephone',
        'notes',
        'avatar_path',
        'whatsapp_id',
        'instagram_handle',
        'messenger_psid',
        'telegram_chat_id',
        'widget_token',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function customFieldValues(): HasMany
    {
        return $this->hasMany(CustomFieldValue::class);
    }

    /** Convenience accessor: "prenom nom" already trimmed, used across the views. */
    public function getNomCompletAttribute(): string
    {
        return trim($this->prenom.' '.$this->nom);
    }
}
