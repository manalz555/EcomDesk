<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateAutomatedReply;
use App\Models\Attachment;
use App\Models\AuditLog;
use App\Models\AutomationRule;
use App\Models\Client;
use App\Models\Conversation;
use App\Support\SqlDialect;
use App\Models\ConversationNote;
use App\Models\Reponse;
use App\Models\Tag;
use App\Models\Team;
use App\Models\User;
use App\Notifications\NewConversationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * The largest controller in the app — everything about the lifecycle of a
 * conversation: listing/filtering, creation (incl. triggering AI automation),
 * replying, tagging, notes, attachments, team assignment, satisfaction, and
 * AI draft approval/rejection.
 */
class ConversationController extends Controller
{
    /** Colonnes triables depuis la liste des conversations, et comment les ordonner. */
    private const SORTABLE = [
        'date' => 'created_at',
        'sujet' => 'sujet',
        // Ordre "metier" (Faible < Moyenne < Haute), pas alphabetique :
        // l'expression SQL est construite a l'execution par triParValeurs().
        'priorite' => 'priorite',
        'statut' => 'statut',
    ];

    /** Traduit une colonne triable en expression SQL d'ordonnancement. */
    private function triParValeurs(string $sort): string
    {
        return match ($sort) {
            'priorite' => SqlDialect::orderByValues('priorite', Conversation::PRIORITES),
            'statut' => SqlDialect::orderByValues('statut', Conversation::STATUTS),
            default => self::SORTABLE[$sort],
        };
    }

    public function index(Request $request)
    {
        $sort = $request->input('sort', 'date');
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        if (! array_key_exists($sort, self::SORTABLE)) {
            $sort = 'date';
        }

        $conversations = Conversation::with(['client', 'agent', 'tags'])
            // Une conversation dont le brouillon IA attend une validation
            // demande une action : la liste doit le signaler sans qu'on ait
            // a ouvrir chaque fiche.
            ->withCount(['reponses as brouillons_en_attente' => fn ($q) => $q->where('is_draft', true)])
            ->recherche($request->q)
            ->when($request->statut, fn ($q) => $q->where('statut', $request->statut))
            ->when($request->priorite, fn ($q) => $q->where('priorite', $request->priorite))
            ->when($request->canal, fn ($q) => $q->where('canal', $request->canal))
            ->when($request->boolean('non_assignees'), fn ($q) => $q->whereNull('agent_id'))
            ->when($request->boolean('mes_conversations'), fn ($q) => $q->where('agent_id', auth()->id()))
            ->orderByRaw($this->triParValeurs($sort).' '.$direction)
            ->paginate(15)
            ->withQueryString();

        return view('conversations.index', [
            'conversations' => $conversations,
            'statuts' => Conversation::STATUTS,
            'priorites' => Conversation::PRIORITES,
            'canaux' => Conversation::CANAUX,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function create(Request $request)
    {
        $clients = Client::orderBy('nom')->get();
        $clientSelectionne = $request->client_id;

        return view('conversations.create', [
            'clients' => $clients,
            'clientSelectionne' => $clientSelectionne,
            'canaux' => Conversation::CANAUX,
            'categories' => Conversation::CATEGORIES,
            'priorites' => Conversation::PRIORITES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => ['nullable', 'exists:clients,id'],
            'nouveau_client_nom' => ['required_without:client_id', 'nullable', 'string', 'max:255'],
            'nouveau_client_prenom' => ['nullable', 'string', 'max:255'],
            'nouveau_client_email' => ['nullable', 'email', 'max:255'],
            'nouveau_client_telephone' => ['nullable', 'string', 'max:30'],
            'sujet' => ['required', 'string', 'max:255'],
            'contenu' => ['required', 'string'],
            'canal' => ['required', 'in:'.implode(',', Conversation::CANAUX)],
            'categorie' => ['required', 'in:'.implode(',', Conversation::CATEGORIES)],
            'priorite' => ['required', 'in:'.implode(',', Conversation::PRIORITES)],
        ]);

        // Un agent peut creer une fiche client a la volee lors d'une nouvelle conversation.
        if (empty($data['client_id'])) {
            $client = Client::create([
                'nom' => $data['nouveau_client_nom'],
                'prenom' => $data['nouveau_client_prenom'] ?? null,
                'email' => $data['nouveau_client_email'] ?? null,
                'telephone' => $data['nouveau_client_telephone'] ?? null,
            ]);
            $clientId = $client->id;
        } else {
            $clientId = $data['client_id'];
        }

        $conversation = Conversation::create([
            'client_id' => $clientId,
            'sujet' => $data['sujet'],
            'contenu' => $data['contenu'],
            'canal' => $data['canal'],
            'categorie' => $data['categorie'],
            'priorite' => $data['priorite'],
            'statut' => 'nouveau',
            'last_message_at' => now(),
        ]);

        AuditLog::record('conversation.created', $conversation, "Conversation créée : {$conversation->sujet}");

        // Notify admins/managers (never the creator themselves) so someone
        // can pick up an unassigned conversation.
        User::where('is_bot', false)
            ->whereIn('role', ['admin', 'manager'])
            ->where('id', '!=', auth()->id())
            ->get()
            ->each(fn (User $user) => $user->notify(new NewConversationNotification($conversation)));

        // The AI automation entry point: if an enabled rule matches this
        // conversation's canal/category, queue a draft reply — see
        // GenerateAutomatedReply and AutomationRule::matches().
        $rule = AutomationRule::where('enabled', true)->get()->first(fn (AutomationRule $rule) => $rule->matches($conversation));

        if ($rule) {
            GenerateAutomatedReply::dispatch($conversation, $rule);
        }

        return redirect()->route('conversations.show', $conversation)->with('success', 'Conversation creee avec succes.');
    }

    public function show(Conversation $conversation)
    {
        $conversation->load(['client', 'agent', 'team', 'tags', 'reponses.agent', 'reponses.attachments', 'notes.user']);

        return view('conversations.show', [
            'conversation' => $conversation,
            'statuts' => Conversation::STATUTS,
            'priorites' => Conversation::PRIORITES,
            'tags' => Tag::orderBy('name')->get(),
            'teams' => auth()->user()->canManageTeam() ? Team::orderBy('name')->get() : collect(),
        ]);
    }

    /** Un agent prend en charge une conversation non assignee (auto-affectation, RG13). */
    public function assign(Conversation $conversation)
    {
        if ($conversation->agent_id) {
            return back()->with('error', 'Cette conversation est deja assignee.');
        }

        $conversation->update([
            'agent_id' => auth()->id(),
            'statut' => $conversation->statut === 'nouveau' ? 'en_cours' : $conversation->statut,
        ]);

        AuditLog::record('conversation.assigned', $conversation, auth()->user()->name.' a pris en charge la conversation.');

        return back()->with('success', 'Conversation prise en charge.');
    }

    public function updateStatut(Request $request, Conversation $conversation)
    {
        $data = $request->validate([
            'statut' => ['required', 'in:'.implode(',', Conversation::STATUTS)],
        ]);

        $conversation->update($data);

        AuditLog::record('conversation.status_changed', $conversation, "Statut changé en « {$data['statut']} »");

        return back()->with('success', 'Statut mis a jour.');
    }

    public function updatePriorite(Request $request, Conversation $conversation)
    {
        $data = $request->validate([
            'priorite' => ['required', 'in:'.implode(',', Conversation::PRIORITES)],
        ]);

        $conversation->update($data);

        AuditLog::record('conversation.priority_changed', $conversation, "Priorité changée en « {$data['priorite']} »");

        return back()->with('success', 'Priorite mise a jour.');
    }

    public function storeReponse(Request $request, Conversation $conversation)
    {
        $data = $request->validate([
            'contenu' => ['required', 'string'],
            'pieces_jointes' => ['nullable', 'array', 'max:5'],
            // Liste blanche de types de fichiers : jamais de scripts executables (php, exe, sh...).
            'pieces_jointes.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,csv,txt'],
        ]);

        $reponse = $conversation->reponses()->create([
            'agent_id' => auth()->id(),
            'contenu' => $data['contenu'],
        ]);

        foreach ($request->file('pieces_jointes', []) as $fichier) {
            // Stockees sur le disque prive : jamais accessibles directement par URL,
            // uniquement via la route de telechargement authentifiee et autorisee.
            $path = $fichier->store('attachments/'.$conversation->id, 'local');

            $reponse->attachments()->create([
                'disk' => 'local',
                'path' => $path,
                'original_name' => $fichier->getClientOriginalName(),
                'mime_type' => $fichier->getClientMimeType(),
                'size' => $fichier->getSize(),
            ]);
        }

        $conversation->last_message_at = now();

        // Repondre a une conversation nouvelle la fait passer automatiquement "en cours".
        if ($conversation->statut === 'nouveau') {
            $conversation->statut = 'en_cours';
        }

        if (! $conversation->agent_id) {
            $conversation->agent_id = auth()->id();
        }

        $conversation->save();

        AuditLog::record('conversation.replied', $conversation, auth()->user()->name.' a répondu à la conversation.');

        return back()->with('success', 'Reponse envoyee.');
    }

    /** Valide un brouillon genere par l'IA : il devient une reponse officielle, envoyee au client. */
    public function approveDraft(Request $request, Conversation $conversation, Reponse $draft)
    {
        // Guards against approving/discarding a draft that belongs to a
        // different conversation, or a reply that was never a draft at all.
        abort_unless($draft->conversation_id === $conversation->id && $draft->is_draft, 404);

        $data = $request->validate([
            'contenu' => ['required', 'string'],
        ]);

        $draft->update([
            'contenu' => $data['contenu'],
            'is_draft' => false,
            'agent_id' => auth()->id(),
        ]);

        $conversation->last_message_at = now();

        if ($conversation->statut === 'nouveau') {
            $conversation->statut = 'en_cours';
        }

        if (! $conversation->agent_id) {
            $conversation->agent_id = auth()->id();
        }

        $conversation->save();

        AuditLog::record('conversation.draft_approved', $conversation, auth()->user()->name.' a validé et envoyé un brouillon généré par IA.');

        return back()->with('success', 'Réponse validée et envoyée.');
    }

    /** Rejette un brouillon genere par l'IA : rien n'est envoye au client. */
    public function discardDraft(Conversation $conversation, Reponse $draft)
    {
        abort_unless($draft->conversation_id === $conversation->id && $draft->is_draft, 404);

        $draft->delete();

        AuditLog::record('conversation.draft_discarded', $conversation, auth()->user()->name.' a rejeté un brouillon généré par IA.');

        return back()->with('success', 'Brouillon rejeté.');
    }

    public function storeTag(Request $request, Conversation $conversation)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:50'],
        ]);

        $tag = Tag::firstOrCreate(['name' => trim($data['nom'])]);

        $conversation->tags()->syncWithoutDetaching($tag->id);

        AuditLog::record('conversation.tagged', $conversation, "Étiquette ajoutée : {$tag->name}");

        return back()->with('success', 'Étiquette ajoutée.');
    }

    public function removeTag(Conversation $conversation, Tag $tag)
    {
        $conversation->tags()->detach($tag->id);

        AuditLog::record('conversation.untagged', $conversation, "Étiquette retirée : {$tag->name}");

        return back()->with('success', 'Étiquette retirée.');
    }

    public function storeNote(Request $request, Conversation $conversation)
    {
        $data = $request->validate([
            'contenu' => ['required', 'string'],
        ]);

        ConversationNote::create([
            'conversation_id' => $conversation->id,
            'user_id' => auth()->id(),
            'contenu' => $data['contenu'],
        ]);

        return back()->with('success', 'Note ajoutée.');
    }

    public function updateSatisfaction(Request $request, Conversation $conversation)
    {
        $data = $request->validate([
            'satisfaction' => ['nullable', 'integer', 'in:'.implode(',', Conversation::SATISFACTIONS)],
        ]);

        $conversation->update(['satisfaction' => $data['satisfaction'] ?? null]);

        AuditLog::record('conversation.satisfaction_recorded', $conversation, 'Satisfaction client enregistrée : '.($data['satisfaction'] ?? '—').'/5');

        return back()->with('success', 'Satisfaction enregistrée.');
    }

    public function updateTeam(Request $request, Conversation $conversation)
    {
        abort_unless(auth()->user()->canManageTeam(), 403);

        $data = $request->validate([
            'team_id' => ['nullable', 'exists:teams,id'],
        ]);

        $conversation->update(['team_id' => $data['team_id'] ?? null]);

        AuditLog::record('conversation.team_changed', $conversation, 'Équipe assignée mise à jour.');

        return back()->with('success', 'Équipe mise à jour.');
    }

    // The only way to reach a private-disk attachment: checks the attachment
    // really belongs to this conversation before streaming it, so an agent
    // can't guess another conversation's attachment URL to download it.
    public function downloadAttachment(Conversation $conversation, Attachment $attachment)
    {
        abort_unless($attachment->reponse->conversation_id === $conversation->id, 404);

        return Storage::disk($attachment->disk)->download($attachment->path, $attachment->original_name);
    }
}
