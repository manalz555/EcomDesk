<?php

namespace App\Notifications;

use App\Models\Conversation;
use App\Models\Reponse;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AutomatedDraftReady extends Notification
{
    use Queueable;

    public function __construct(public Conversation $conversation, public Reponse $reponse)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'conversation_id' => $this->conversation->id,
            'client_nom' => $this->conversation->client->nom_complet,
            'sujet' => $this->conversation->sujet,
            'type' => 'draft_ready',
        ];
    }
}
