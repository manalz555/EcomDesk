<?php

namespace App\Jobs;

use App\Contracts\AiReplyService;
use App\Models\AuditLog;
use App\Models\AutomationRule;
use App\Models\Conversation;
use App\Models\User;
use App\Notifications\AutomatedDraftReady;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Runs in the background (queue) so the agent who created the conversation
 * gets their HTTP response immediately instead of waiting on the AI call.
 * Dispatched from ConversationController::store() when a matching
 * AutomationRule is found.
 */
class GenerateAutomatedReply implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(public Conversation $conversation, public AutomationRule $rule)
    {
    }

    /**
     * Genere un brouillon de reponse IA : il n'est PAS envoye automatiquement, un agent doit
     * le valider (ou le modifier puis le valider) avant qu'il ne soit visible du client et
     * ne compte comme reponse officielle.
     */
    public function handle(AiReplyService $aiReplyService): void
    {
        $reply = $aiReplyService->generateReply($this->conversation, $this->rule->prompt_template);

        // is_draft = true is what keeps this invisible to the client and out
        // of the "sent" history until an agent approves it — the conversation
        // row itself is left completely untouched at this point.
        $draft = $this->conversation->reponses()->create([
            'agent_id' => $this->rule->bot_user_id,
            'contenu' => $reply,
            'is_draft' => true,
        ]);

        AuditLog::record(
            'conversation.draft_generated',
            $this->conversation,
            "Brouillon de réponse généré par la règle « {$this->rule->name} », en attente de validation."
        );

        // Everyone on staff is notified — any of them can review and approve
        // the draft, not just whoever created the conversation.
        User::where('is_bot', false)
            ->whereIn('role', ['admin', 'manager', 'agent'])
            ->get()
            ->each(fn (User $user) => $user->notify(new AutomatedDraftReady($this->conversation, $draft)));
    }
}
