<?php

namespace App\Notifications;

use App\Models\Conversation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewConversationNotification extends Notification
{
    use Queueable;

    public function __construct(public Conversation $conversation)
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
            'canal' => $this->conversation->canal,
        ];
    }
}
