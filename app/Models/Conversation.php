<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * The core entity of the whole app: one customer conversation on one channel.
 * `agent_id` and `team_id` are nullable — an unassigned conversation is a
 * normal, expected state (see the "Non assignees" filter in the index view).
 */
class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'agent_id',
        'team_id',
        'sujet',
        'contenu',
        'canal',
        'statut',
        'priorite',
        'satisfaction',
        'categorie',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    // Central list of allowed enum-like values, referenced by both the
    // validation rules in the controller and the <select> options in Blade,
    // so the two never drift apart.
    public const STATUTS = ['nouveau', 'en_cours', 'en_attente', 'resolu'];
    public const PRIORITES = ['faible', 'moyenne', 'haute'];
    public const SATISFACTIONS = [1, 2, 3, 4, 5];
    public const CANAUX = ['email', 'whatsapp', 'instagram', 'messenger', 'telegram', 'live_chat', 'formulaire'];
    public const CATEGORIES = ['livraison', 'paiement', 'remboursement', 'produit', 'reclamation', 'autre'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ConversationNote::class)->orderBy('created_at');
    }

    // Includes both sent replies AND pending AI drafts (is_draft = true) —
    // callers that only want the sent history filter on is_draft themselves
    // (see conversations/show.blade.php).
    public function reponses(): HasMany
    {
        return $this->hasMany(Reponse::class)->orderBy('created_at');
    }

    /** Query scope: free-text search across subject/content and the client's name. */
    public function scopeRecherche($query, ?string $terme)
    {
        if (! $terme) {
            return $query;
        }

        return $query->where(function ($q) use ($terme) {
            $q->where('sujet', 'like', "%{$terme}%")
              ->orWhere('contenu', 'like', "%{$terme}%")
              ->orWhereHas('client', function ($cq) use ($terme) {
                  $cq->where('nom', 'like', "%{$terme}%")
                     ->orWhere('prenom', 'like', "%{$terme}%");
              });
        });
    }
}
