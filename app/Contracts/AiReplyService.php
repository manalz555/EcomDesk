<?php

namespace App\Contracts;

use App\Models\Conversation;

/**
 * Contract for "generate a reply for this conversation" — the automation
 * Job depends on this interface, not on OpenAiReplyService directly, so the
 * AI provider can be swapped (or mocked in tests) without touching the job.
 */
interface AiReplyService
{
    /**
     * Genere une reponse automatisee pour une conversation, a partir d'un gabarit de prompt.
     * Retourne une reponse simulee si aucune cle API n'est configuree.
     */
    public function generateReply(Conversation $conversation, ?string $promptTemplate = null): string;
}
