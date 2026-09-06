<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateAutomatedReply;
use App\Models\AuditLog;
use App\Models\AutomationRule;
use App\Models\Client;
use App\Models\Conversation;
use App\Models\User;
use App\Notifications\NewConversationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * The live chat widget: the first *real* inbound channel of the platform.
 * These routes are public (a website visitor has no account) — the visitor is
 * identified by a random `widget_token` generated on first contact and kept
 * in their browser's localStorage. Everything downstream reuses the normal
 * pipeline: a widget message is an ordinary Conversation/Reponse, so the
 * inbox, notifications and AI draft engine all work on it unchanged.
 */
class WidgetController extends Controller
{
    /** The chat UI page, loaded inside the <iframe> injected by public/widget.js. */
    public function frame()
    {
        return view('widget.frame');
    }

    /** A visitor sends a message: first contact opens a conversation, follow-ups append to it. */
    public function send(Request $request)
    {
        $data = $request->validate([
            'token' => ['nullable', 'string', 'max:64'],
            'message' => ['required', 'string', 'max:2000'],
            'name' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $client = $this->resolveClient($data);

        // One live conversation per visitor: reuse the latest unresolved
        // live_chat conversation, or open a fresh one (a resolved thread
        // stays closed — a new message starts a new ticket).
        $conversation = $client->conversations()
            ->where('canal', 'live_chat')
            ->where('statut', '!=', 'resolu')
            ->latest()
            ->first();

        if ($conversation) {
            $conversation->reponses()->create([
                'contenu' => $data['message'],
                'is_client' => true,
            ]);
            $conversation->update(['last_message_at' => now()]);
        } else {
            $conversation = Conversation::create([
                'client_id' => $client->id,
                'sujet' => Str::limit($data['message'], 60),
                'contenu' => $data['message'],
                'canal' => 'live_chat',
                'categorie' => 'autre',
                'priorite' => 'moyenne',
                'statut' => 'nouveau',
                'last_message_at' => now(),
            ]);

            AuditLog::record('conversation.created', $conversation, 'Conversation ouverte depuis le widget de chat.');

            User::where('is_bot', false)
                ->whereIn('role', ['admin', 'manager'])
                ->get()
                ->each(fn (User $user) => $user->notify(new NewConversationNotification($conversation)));

            // Same AI entry point as the back-office form: a matching enabled
            // rule queues a draft reply for an agent to approve.
            $rule = AutomationRule::where('enabled', true)->get()
                ->first(fn (AutomationRule $rule) => $rule->matches($conversation));

            if ($rule) {
                GenerateAutomatedReply::dispatch($conversation, $rule);
            }
        }

        return response()->json([
            'token' => $client->widget_token,
            'conversation_id' => $conversation->id,
        ]);
    }

    /** Polled by the widget every few seconds: full message history of the visitor's current conversation. */
    public function messages(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'max:64'],
        ]);

        $client = Client::where('widget_token', $data['token'])->first();

        if (! $client) {
            return response()->json(['messages' => []]);
        }

        $conversation = $client->conversations()
            ->where('canal', 'live_chat')
            ->latest()
            ->first();

        if (! $conversation) {
            return response()->json(['messages' => []]);
        }

        // The visitor sees: their own opening message, their follow-ups, and
        // approved agent replies — never AI drafts (is_draft stays private).
        $messages = collect([[
            'from' => 'client',
            'text' => $conversation->contenu,
            'at' => $conversation->created_at->format('H:i'),
        ]]);

        $conversation->reponses()
            ->where('is_draft', false)
            ->with('agent')
            ->get()
            ->each(function ($reponse) use ($messages) {
                $messages->push([
                    'from' => $reponse->is_client ? 'client' : 'agent',
                    'author' => $reponse->is_client ? null : ($reponse->agent?->prenom ?: $reponse->agent?->name),
                    'text' => $reponse->contenu,
                    'at' => $reponse->created_at->format('H:i'),
                ]);
            });

        return response()->json([
            'messages' => $messages->values(),
            'statut' => $conversation->statut,
        ]);
    }

    /** Finds the visitor's Client record by token, or creates one (with a fresh token) on first contact. */
    private function resolveClient(array $data): Client
    {
        // Optional fields absent from the request are absent from $data too —
        // normalize them before use.
        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;

        if (! empty($data['token'])) {
            $existing = Client::where('widget_token', $data['token'])->first();

            if ($existing) {
                // A visitor who later gives their name/email upgrades their record.
                if ($name && str_starts_with($existing->nom, 'Visiteur')) {
                    $existing->update(['nom' => $name]);
                }
                if ($email && ! $existing->email) {
                    $existing->update(['email' => $email]);
                }

                return $existing;
            }
        }

        return Client::create([
            'nom' => $name ?: 'Visiteur '.Str::upper(Str::random(4)),
            'email' => $email,
            'widget_token' => Str::random(40),
        ]);
    }
}
