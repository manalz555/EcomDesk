<x-app-layout :title="$conversation->sujet">
    <x-slot name="header">
        <x-page-header :title="$conversation->sujet" />
    </x-slot>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-2">
            <x-card>
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm text-ink-500 dark:text-sand-400">
                        {{ $conversation->client->nom_complet }} &middot;
                        <span class="capitalize">{{ str_replace('_', ' ', $conversation->canal) }}</span> &middot;
                        {{ $conversation->created_at->format('d/m/Y H:i') }}
                    </p>
                    @if (! $conversation->agent_id)
                        <form method="POST" action="{{ route('conversations.assign', $conversation) }}">
                            @csrf
                            <x-secondary-button type="submit">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                                Prendre en charge
                            </x-secondary-button>
                        </form>
                    @endif
                </div>
                <p class="mt-4 whitespace-pre-line text-sm text-ink-900 dark:text-sand-50">{{ $conversation->contenu }}</p>

                <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-sand-200 pt-4 dark:border-ink-800">
                    @foreach ($conversation->tags as $tag)
                        <span class="inline-flex items-center gap-1.5 rounded-full py-1 pl-2.5 pr-1 text-xs font-medium" style="background-color: {{ $tag->color }}22; color: {{ $tag->color }};">
                            {{ $tag->name }}
                            <form method="POST" action="{{ route('conversations.tags.destroy', [$conversation, $tag]) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="rounded-full p-0.5 hover:bg-black/10">
                                    <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                </button>
                            </form>
                        </span>
                    @endforeach

                    <div x-data="{ adding: false }" class="inline-flex items-center">
                        <button type="button" x-show="!adding" @click="adding = true; $nextTick(() => $refs.tagInput.focus())" class="inline-flex items-center gap-1 rounded-full border border-dashed border-sand-300 px-2.5 py-1 text-xs font-medium text-ink-500 hover:border-ink-400 hover:text-ink-700 dark:border-ink-700 dark:text-sand-400">
                            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            Étiquette
                        </button>
                        <form x-show="adding" x-cloak method="POST" action="{{ route('conversations.tags.store', $conversation) }}" @click.outside="adding = false" class="inline-flex items-center gap-1">
                            @csrf
                            <input x-ref="tagInput" type="text" name="nom" list="tags-list" placeholder="Nom de l'étiquette" class="w-36 rounded-full border-sand-300 bg-white px-2.5 py-1 text-xs text-ink-900 focus:border-ink-500 focus:ring-ink-500 dark:border-ink-700 dark:bg-ink-800 dark:text-sand-50" required>
                            <datalist id="tags-list">
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->name }}">
                                @endforeach
                            </datalist>
                            <button type="submit" class="text-xs font-medium text-ink-600 dark:text-sand-300">Ajouter</button>
                        </form>
                    </div>
                </div>
            </x-card>

            @php $drafts = $conversation->reponses->where('is_draft', true); @endphp
            @if ($drafts->isNotEmpty())
                <x-card class="border-sand-400 dark:border-sand-500">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-sand-600 dark:text-sand-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" /></svg>
                        <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Brouillon généré par IA — en attente de validation</h2>
                    </div>
                    <p class="mt-1 text-xs text-ink-500 dark:text-sand-400">
                        Rien n'est envoyé au client tant que vous n'avez pas validé (ou modifié puis validé) ce brouillon.
                    </p>

                    @foreach ($drafts as $draft)
                        <div class="mt-4">
                            <form id="draft-approve-{{ $draft->id }}" method="POST" action="{{ route('conversations.drafts.approve', [$conversation, $draft]) }}">
                                @csrf
                                <x-textarea-input name="contenu" rows="3">{{ $draft->contenu }}</x-textarea-input>
                            </form>

                            <div class="mt-2 flex items-center gap-2">
                                <x-primary-button type="submit" form="draft-approve-{{ $draft->id }}">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                    Valider et envoyer
                                </x-primary-button>

                                <form method="POST" action="{{ route('conversations.drafts.discard', [$conversation, $draft]) }}" onsubmit="return confirm('Rejeter ce brouillon ? Il ne sera pas envoyé.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-rose-600 hover:underline dark:text-rose-400">
                                        Rejeter
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </x-card>
            @endif

            <x-card>
                <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Historique des échanges</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($conversation->reponses->where('is_draft', false) as $rep)
                        {{-- Message du client (ex : widget de chat) vs réponse d'un agent : fond distinct + badge. --}}
                        <div class="rounded-lg border p-3 {{ $rep->is_client ? 'border-sand-300 bg-sand-100/70 dark:border-ink-600 dark:bg-ink-800/70' : 'border-sand-200 dark:border-ink-700' }}">
                            <div class="mb-1 flex items-center justify-between text-xs text-ink-400 dark:text-sand-500">
                                <span class="flex items-center gap-2 font-semibold text-ink-700 dark:text-sand-200">
                                    @if ($rep->is_client)
                                        {{ $conversation->client->nom_complet }}
                                        <span class="rounded-full bg-ink-900 px-2 py-0.5 text-[10px] font-medium uppercase tracking-wide text-sand-100 dark:bg-sand-100 dark:text-ink-900">Client</span>
                                    @else
                                        {{ $rep->agent?->name ?? 'Agent' }}
                                    @endif
                                </span>
                                <span>{{ $rep->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <p class="whitespace-pre-line text-sm text-ink-800 dark:text-sand-100">{{ $rep->contenu }}</p>

                            @if ($rep->attachments->isNotEmpty())
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach ($rep->attachments as $piece)
                                        <a href="{{ route('conversations.attachments.download', [$conversation, $piece]) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-sand-200 bg-sand-50 px-2.5 py-1.5 text-xs font-medium text-ink-600 hover:bg-sand-100 dark:border-ink-700 dark:bg-ink-800 dark:text-sand-300 dark:hover:bg-ink-700">
                                            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                                            {{ $piece->original_name }} ({{ $piece->humanSize() }})
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-ink-400 dark:text-sand-500">Aucune réponse pour le moment.</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('conversations.reponses.store', $conversation) }}" class="mt-4" enctype="multipart/form-data">
                    @csrf
                    <x-textarea-input name="contenu" rows="3" placeholder="Écrire une réponse…" required></x-textarea-input>
                    <div class="mt-2 flex items-center justify-between gap-3">
                        <input type="file" name="pieces_jointes[]" multiple class="block flex-1 text-xs text-ink-500 file:mr-3 file:rounded-lg file:border-0 file:bg-sand-100 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-ink-700 hover:file:bg-sand-200 dark:text-sand-400 dark:file:bg-ink-800 dark:file:text-sand-200 dark:hover:file:bg-ink-700">
                        <x-primary-button class="shrink-0">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.126A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.876L5.999 12Zm0 0h7.5" /></svg>
                            Envoyer
                        </x-primary-button>
                    </div>
                </form>
            </x-card>

            <x-card>
                <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Notes internes</h2>
                <p class="text-xs text-ink-400 dark:text-sand-500">Visibles uniquement par l'équipe, jamais par le client.</p>
                <div class="mt-4 space-y-3">
                    @forelse ($conversation->notes as $note)
                        <div class="rounded-lg bg-amber-50 p-3 dark:bg-amber-950/40">
                            <div class="mb-1 flex items-center justify-between text-xs text-amber-700 dark:text-amber-400">
                                <span class="font-semibold">{{ $note->user->name }}</span>
                                <span>{{ $note->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <p class="whitespace-pre-line text-sm text-amber-900 dark:text-amber-200">{{ $note->contenu }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-ink-400 dark:text-sand-500">Aucune note pour le moment.</p>
                    @endforelse
                </div>
                <form method="POST" action="{{ route('conversations.notes.store', $conversation) }}" class="mt-4">
                    @csrf
                    <x-textarea-input name="contenu" rows="2" placeholder="Ajouter une note interne…" required></x-textarea-input>
                    <div class="mt-2">
                        <x-secondary-button type="submit">Ajouter la note</x-secondary-button>
                    </div>
                </form>
            </x-card>
        </div>

        <div class="space-y-4">
            <x-card>
                <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Suivi</h2>
                <form method="POST" action="{{ route('conversations.statut', $conversation) }}" class="mt-3">
                    @csrf @method('PATCH')
                    <x-input-label value="Statut" />
                    <x-select-input name="statut" onchange="this.form.submit()">
                        @foreach ($statuts as $s)
                            <option value="{{ $s }}" @selected($conversation->statut == $s)>{{ str_replace('_', ' ', ucfirst($s)) }}</option>
                        @endforeach
                    </x-select-input>
                </form>
                <form method="POST" action="{{ route('conversations.priorite', $conversation) }}" class="mt-4">
                    @csrf @method('PATCH')
                    <x-input-label value="Priorité" />
                    <x-select-input name="priorite" onchange="this.form.submit()">
                        @foreach ($priorites as $p)
                            <option value="{{ $p }}" @selected($conversation->priorite == $p)>{{ ucfirst($p) }}</option>
                        @endforeach
                    </x-select-input>
                </form>

                @if ($teams->isNotEmpty())
                    <form method="POST" action="{{ route('conversations.team', $conversation) }}" class="mt-4">
                        @csrf @method('PATCH')
                        <x-input-label value="Équipe" />
                        <x-select-input name="team_id" onchange="this.form.submit()">
                            <option value="">Aucune</option>
                            @foreach ($teams as $team)
                                <option value="{{ $team->id }}" @selected($conversation->team_id == $team->id)>{{ $team->name }}</option>
                            @endforeach
                        </x-select-input>
                    </form>
                @endif

                <form method="POST" action="{{ route('conversations.satisfaction', $conversation) }}" class="mt-4">
                    @csrf @method('PATCH')
                    <x-input-label value="Satisfaction client" />
                    <x-select-input name="satisfaction" onchange="this.form.submit()">
                        <option value="">Non renseignée</option>
                        @foreach (\App\Models\Conversation::SATISFACTIONS as $note)
                            <option value="{{ $note }}" @selected($conversation->satisfaction == $note)>{{ $note }} / 5</option>
                        @endforeach
                    </x-select-input>
                </form>
            </x-card>

            <x-card>
                <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Détails</h2>
                <p class="mt-3 text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-sand-500">Catégorie</p>
                <p class="mt-1 text-sm capitalize text-ink-900 dark:text-sand-50">{{ $conversation->categorie }}</p>
                <p class="mt-3 text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-sand-500">Agent assigné</p>
                <p class="mt-1 text-sm text-ink-900 dark:text-sand-50">{{ $conversation->agent->name ?? 'Non assignée' }}</p>
                <a href="{{ route('clients.show', $conversation->client) }}" class="mt-4 inline-flex items-center gap-1.5 text-sm font-medium text-ink-600 hover:underline dark:text-sand-300">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                    Voir la fiche client
                </a>
            </x-card>
        </div>
    </div>
</x-app-layout>
