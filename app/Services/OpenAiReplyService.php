<?php

namespace App\Services;

use App\Contracts\AiReplyService;
use App\Models\ChannelIntegration;
use App\Models\Conversation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Real implementation of AiReplyService, backed by the OpenAI Chat
 * Completions API. Bound to the AiReplyService interface in
 * AppServiceProvider — swapping providers later means changing one binding,
 * not every call site.
 */
class OpenAiReplyService implements AiReplyService
{
    public function generateReply(Conversation $conversation, ?string $promptTemplate = null): string
    {
        $apiKey = $this->apiKey();

        // No key configured yet (fresh install / demo mode) — degrade
        // gracefully instead of failing the whole automation flow.
        if (! $apiKey) {
            return $this->simulatedReply($conversation);
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(15)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => $promptTemplate ?: "Tu es l'assistant du service client d'EcomDesk. Reponds poliment et brievement, en francais."],
                        ['role' => 'user', 'content' => "Sujet : {$conversation->sujet}\n\nMessage du client : {$conversation->contenu}"],
                    ],
                    'max_tokens' => 300,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');

                if (filled($content)) {
                    return trim($content);
                }
            }

            // Logged as a warning, not an error: a failed AI call is an
            // expected, handled case (falls back below), not a bug.
            Log::warning('OpenAI reply generation failed, falling back to simulated reply.', [
                'status' => $response->status(),
                'conversation_id' => $conversation->id,
            ]);
        } catch (\Throwable $e) {
            Log::warning('OpenAI reply generation threw an exception, falling back to simulated reply.', [
                'message' => $e->getMessage(),
                'conversation_id' => $conversation->id,
            ]);
        }

        return $this->simulatedReply($conversation);
    }

    /** Reads the OpenAI key from the encrypted `config` column of the "openai" ChannelIntegration row, if connected. */
    private function apiKey(): ?string
    {
        $integration = ChannelIntegration::where('channel', 'openai')->where('is_connected', true)->first();

        return $integration?->config['api_key'] ?? null;
    }

    /** Reponse simulee, utilisee tant qu'aucune cle OpenAI reelle n'est configuree. */
    private function simulatedReply(Conversation $conversation): string
    {
        return "Bonjour, merci pour votre message concernant « {$conversation->sujet} ». ".
            "Notre équipe a bien reçu votre demande et revient vers vous très rapidement.";
    }
}
