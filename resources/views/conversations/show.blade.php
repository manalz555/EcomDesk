@php
    // Un seul fil chronologique plutot que trois blocs separes : le message
    // d'ouverture, les reponses et les notes internes appartiennent a la meme
    // histoire. Les notes restent visuellement distinctes — elles ne partent
    // jamais chez le client — mais gardent leur place dans le temps.
    $fil = $conversation->reponses
        ->where('is_draft', false)
        ->map(fn ($r) => ['genre' => $r->is_client ? 'client' : 'agent', 'at' => $r->created_at, 'objet' => $r])
        ->concat(
            $conversation->notes->map(fn ($n) => ['genre' => 'note', 'at' => $n->created_at, 'objet' => $n])
        )
        ->sortBy('at')
        ->values();

    $brouillons = $conversation->reponses->where('is_draft', true);

    $initiale = fn (?string $nom) => mb_strtoupper(mb_substr(trim((string) $nom) ?: '?', 0, 1));
@endphp

<x-app-layout :title="$conversation->sujet">
    <x-slot name="header">
        <x-page-header
            :title="$conversation->sujet"
            :subtitle="$conversation->client->nom_complet
                .' · '.ucfirst(str_replace('_', ' ', $conversation->canal))
                .' · ouverte le '.$conversation->created_at->format('d/m/Y à H:i')"
        />
    </x-slot>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">

        {{-- ============================================ LE FIL ============ --}}
        <div class="overflow-hidden rounded-xl border border-sand-200 bg-white dark:border-ink-800 dark:bg-ink-900">

            {{-- Bandeau : etat, prise en charge, etiquettes --}}
            <div class="flex flex-wrap items-center gap-x-3 gap-y-2 border-b border-sand-200 px-5 py-3.5 dark:border-ink-800">
                <x-status-badge :status="$conversation->statut" />
                <x-priority-badge :priority="$conversation->priorite" />
                <span class="text-xs text-ink-400 dark:text-sand-500">{{ ucfirst($conversation->categorie) }}</span>

                <div class="ms-auto flex items-center gap-2">
                    @if (! $conversation->agent_id)
                        <form method="POST" action="{{ route('conversations.assign', $conversation) }}">
                            @csrf
                            <x-secondary-button type="submit">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                                Prendre en charge
                            </x-secondary-button>
                        </form>
                    @else
                        <span class="text-xs text-ink-400 dark:text-sand-500">
                            Suivie par <span class="font-medium text-ink-700 dark:text-sand-200">{{ $conversation->agent->name }}</span>
                        </span>
                    @endif
                </div>
            </div>

            {{-- Etiquettes --}}
            <div class="flex flex-wrap items-center gap-2 border-b border-sand-200 px-5 py-2.5 dark:border-ink-800">
                @foreach ($conversation->tags as $tag)
                    <span class="inline-flex items-center gap-1.5 rounded-full py-1 pl-2.5 pr-1 text-xs font-medium" style="background-color: {{ $tag->color }}22; color: {{ $tag->color }};">
                        {{ $tag->name }}
                        <form method="POST" action="{{ route('conversations.tags.destroy', [$conversation, $tag]) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="rounded-full p-0.5 hover:bg-black/10" aria-label="Retirer l'étiquette {{ $tag->name }}">
                                <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                            </button>
                        </form>
                    </span>
                @endforeach

                <div x-data="{ ajout: false }" class="inline-flex items-center">
                    <button type="button" x-show="!ajout" @click="ajout = true; $nextTick(() => $refs.champTag.focus())" class="inline-flex items-center gap-1 rounded-full border border-dashed border-sand-300 px-2.5 py-1 text-xs font-medium text-ink-500 transition hover:border-ink-400 hover:text-ink-700 dark:border-ink-700 dark:text-sand-400 dark:hover:border-sand-500 dark:hover:text-sand-200">
                        <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Étiquette
                    </button>
                    <form x-show="ajout" x-cloak method="POST" action="{{ route('conversations.tags.store', $conversation) }}" @click.outside="ajout = false" class="inline-flex items-center gap-1">
                        @csrf
                        <input x-ref="champTag" type="text" name="nom" list="liste-tags" placeholder="Nom de l'étiquette" class="w-36 rounded-full border-sand-300 bg-white px-2.5 py-1 text-xs text-ink-900 focus:border-ink-500 focus:ring-ink-500 dark:border-ink-700 dark:bg-ink-800 dark:text-sand-50" required>
                        <datalist id="liste-tags">
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->name }}">
                            @endforeach
                        </datalist>
                        <button type="submit" class="text-xs font-medium text-ink-600 dark:text-sand-300">Ajouter</button>
                    </form>
                </div>
            </div>

            {{-- ------------------------------------------------ messages --}}
            <ol class="divide-y divide-sand-100 dark:divide-ink-800">

                {{-- Message d'ouverture : c'est le premier message du fil, pas un encart a part. --}}
                <li class="flex gap-3.5 px-5 py-4">
                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-sand-200 text-xs font-semibold text-ink-700 dark:bg-ink-700 dark:text-sand-200">
                        {{ $initiale($conversation->client->prenom ?: $conversation->client->nom) }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-baseline gap-x-2">
                            <span class="text-sm font-semibold text-ink-900 dark:text-sand-50">{{ $conversation->client->nom_complet }}</span>
                            <span class="text-[11px] font-medium uppercase tracking-wide text-ink-400 dark:text-sand-500">Client</span>
                            <span class="ms-auto text-xs text-ink-400 dark:text-sand-500">{{ $conversation->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="mt-1.5 whitespace-pre-line text-sm leading-relaxed text-ink-800 dark:text-sand-100">{{ $conversation->contenu }}</p>
                    </div>
                </li>

                @foreach ($fil as $entree)
                    @php $objet = $entree['objet']; @endphp

                    @if ($entree['genre'] === 'note')
                        {{-- Note interne : meme place dans le temps, mais elle ne quitte jamais l'equipe. --}}
                        <li class="flex gap-3.5 bg-amber-50/70 px-5 py-4 dark:bg-amber-950/25">
                            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-200 text-xs font-semibold text-amber-900 dark:bg-amber-900 dark:text-amber-200">
                                {{ $initiale($objet->user?->name) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-baseline gap-x-2">
                                    <span class="text-sm font-semibold text-amber-900 dark:text-amber-200">{{ $objet->user?->name ?? 'Équipe' }}</span>
                                    <span class="text-[11px] font-medium uppercase tracking-wide text-amber-700 dark:text-amber-400">Note interne · invisible du client</span>
                                    <span class="ms-auto text-xs text-amber-600/80 dark:text-amber-500/80">{{ $objet->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <p class="mt-1.5 whitespace-pre-line text-sm leading-relaxed text-amber-900 dark:text-amber-100">{{ $objet->contenu }}</p>
                            </div>
                        </li>

                    @elseif ($entree['genre'] === 'client')
                        <li class="flex gap-3.5 px-5 py-4">
                            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-sand-200 text-xs font-semibold text-ink-700 dark:bg-ink-700 dark:text-sand-200">
                                {{ $initiale($conversation->client->prenom ?: $conversation->client->nom) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-baseline gap-x-2">
                                    <span class="text-sm font-semibold text-ink-900 dark:text-sand-50">{{ $conversation->client->nom_complet }}</span>
                                    <span class="text-[11px] font-medium uppercase tracking-wide text-ink-400 dark:text-sand-500">Client</span>
                                    <span class="ms-auto text-xs text-ink-400 dark:text-sand-500">{{ $objet->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <p class="mt-1.5 whitespace-pre-line text-sm leading-relaxed text-ink-800 dark:text-sand-100">{{ $objet->contenu }}</p>
                            </div>
                        </li>

                    @else
                        {{-- Reponse envoyee par l'equipe. --}}
                        <li class="flex gap-3.5 px-5 py-4">
                            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-ink-900 text-xs font-semibold text-sand-50 dark:bg-sand-100 dark:text-ink-900">
                                {{ $initiale($objet->agent?->name) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-baseline gap-x-2">
                                    <span class="text-sm font-semibold text-ink-900 dark:text-sand-50">{{ $objet->agent?->name ?? 'Agent' }}</span>
                                    @if ($objet->agent?->is_bot)
                                        <span class="text-[11px] font-medium uppercase tracking-wide text-sand-700 dark:text-sand-400">Assistant</span>
                                    @endif
                                    <span class="ms-auto text-xs text-ink-400 dark:text-sand-500">{{ $objet->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <p class="mt-1.5 whitespace-pre-line text-sm leading-relaxed text-ink-800 dark:text-sand-100">{{ $objet->contenu }}</p>

                                @if ($objet->attachments->isNotEmpty())
                                    <div class="mt-2.5 flex flex-wrap gap-2">
                                        @foreach ($objet->attachments as $piece)
                                            <a href="{{ route('conversations.attachments.download', [$conversation, $piece]) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-sand-200 px-2.5 py-1.5 text-xs font-medium text-ink-600 transition hover:bg-sand-50 dark:border-ink-700 dark:text-sand-300 dark:hover:bg-ink-800">
                                                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                                                {{ $piece->original_name }}
                                                <span class="text-ink-400 dark:text-sand-500">{{ $piece->humanSize() }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </li>
                    @endif
                @endforeach

                {{-- ------------------------------------------ brouillon IA --}}
                @foreach ($brouillons as $brouillon)
                    <li class="px-5 py-4">
                        <div class="rounded-lg border border-dashed border-sand-400 bg-sand-50 p-4 dark:border-ink-600 dark:bg-ink-950/40">
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                <svg class="h-4 w-4 shrink-0 text-sand-700 dark:text-sand-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" /></svg>
                                <span class="text-sm font-semibold text-ink-900 dark:text-sand-50">Brouillon proposé par l'assistant</span>
                                <span class="rounded-full bg-sand-200 px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-sand-900 dark:bg-ink-700 dark:text-sand-200">Non envoyé</span>
                            </div>
                            <p class="mt-1 text-xs text-ink-500 dark:text-sand-400">
                                Le client ne verra rien tant que vous n'aurez pas validé. Vous pouvez corriger le texte avant de l'envoyer.
                            </p>

                            <form id="valider-brouillon-{{ $brouillon->id }}" method="POST" action="{{ route('conversations.drafts.approve', [$conversation, $brouillon]) }}" class="mt-3">
                                @csrf
                                <x-textarea-input name="contenu" rows="4">{{ $brouillon->contenu }}</x-textarea-input>
                            </form>

                            <div class="mt-3 flex flex-wrap items-center gap-3">
                                <x-primary-button type="submit" form="valider-brouillon-{{ $brouillon->id }}">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                    Valider et envoyer
                                </x-primary-button>

                                <form method="POST" action="{{ route('conversations.drafts.discard', [$conversation, $brouillon]) }}" onsubmit="return confirm('Rejeter ce brouillon ? Il ne sera pas envoyé au client.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-rose-600 hover:underline dark:text-rose-400">Rejeter</button>
                                </form>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ol>

            {{-- ------------------------------------------------ composeur --}}
            {{-- Une seule zone de saisie, deux destinations : le client, ou
                 seulement l'equipe. L'onglet actif change la couleur du bloc
                 pour qu'on ne se trompe jamais de destinataire. --}}
            <div x-data="{ onglet: 'reponse' }"
                 class="border-t border-sand-200 dark:border-ink-800"
                 :class="onglet === 'note' ? 'bg-amber-50/70 dark:bg-amber-950/25' : 'bg-sand-50/60 dark:bg-ink-950/30'">

                <div class="flex gap-1 px-5 pt-3">
                    <button type="button" @click="onglet = 'reponse'"
                            class="rounded-t-lg px-3 py-2 text-sm font-medium transition"
                            :class="onglet === 'reponse'
                                ? 'bg-white text-ink-900 shadow-soft dark:bg-ink-900 dark:text-sand-50'
                                : 'text-ink-500 hover:text-ink-800 dark:text-sand-400 dark:hover:text-sand-200'">
                        Répondre au client
                    </button>
                    <button type="button" @click="onglet = 'note'"
                            class="rounded-t-lg px-3 py-2 text-sm font-medium transition"
                            :class="onglet === 'note'
                                ? 'bg-white text-amber-900 shadow-soft dark:bg-ink-900 dark:text-amber-200'
                                : 'text-ink-500 hover:text-ink-800 dark:text-sand-400 dark:hover:text-sand-200'">
                        Note interne
                    </button>
                </div>

                {{-- Reponse au client --}}
                <form x-show="onglet === 'reponse'" method="POST" action="{{ route('conversations.reponses.store', $conversation) }}" class="px-5 pb-5 pt-3" enctype="multipart/form-data">
                    @csrf
                    <x-textarea-input name="contenu" rows="3" placeholder="Écrire une réponse au client…" required></x-textarea-input>

                    <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                        {{-- Le controle natif affiche « Choose Files / No file chosen » dans la
                             langue du navigateur, que le CSS ne peut pas traduire. On le masque
                             (sans le retirer du formulaire ni du clavier) et on habille un label. --}}
                        <div class="min-w-0 flex-1" x-data="{ fichiers: [] }">
                            <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-sand-100 px-3 py-1.5 text-xs font-medium text-ink-700 transition hover:bg-sand-200 focus-within:ring-2 focus-within:ring-ink-900 focus-within:ring-offset-2 dark:bg-ink-800 dark:text-sand-200 dark:hover:bg-ink-700 dark:focus-within:ring-sand-400 dark:focus-within:ring-offset-ink-900">
                                <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" />
                                </svg>
                                Joindre un fichier
                                <input type="file" name="pieces_jointes[]" multiple class="sr-only"
                                       x-on:change="fichiers = Array.from($event.target.files).map(f => f.name)">
                            </label>
                            <p class="mt-1 truncate text-xs text-ink-400 dark:text-sand-500"
                               x-text="fichiers.length
                                    ? (fichiers.length === 1 ? fichiers[0] : fichiers.length + ' fichiers sélectionnés')
                                    : '5 fichiers maximum, 10 Mo chacun'"></p>
                        </div>

                        <x-primary-button class="shrink-0">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.126A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.876L5.999 12Zm0 0h7.5" /></svg>
                            Envoyer au client
                        </x-primary-button>
                    </div>
                </form>

                {{-- Note interne --}}
                <form x-show="onglet === 'note'" x-cloak method="POST" action="{{ route('conversations.notes.store', $conversation) }}" class="px-5 pb-5 pt-3">
                    @csrf
                    <x-textarea-input name="contenu" rows="3" placeholder="Note visible uniquement par l'équipe…" required></x-textarea-input>

                    <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                        <p class="text-xs text-amber-700 dark:text-amber-400">
                            Cette note reste dans le fil, mais n'est jamais transmise au client.
                        </p>
                        <x-secondary-button type="submit" class="shrink-0">Ajouter la note</x-secondary-button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ============================================ LE RAIL =========== --}}
        <aside class="space-y-4">
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
                <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Le client</h2>

                <dl class="mt-3 space-y-2.5 text-sm">
                    <div class="flex items-baseline justify-between gap-3">
                        <dt class="shrink-0 text-xs uppercase tracking-wider text-ink-400 dark:text-sand-500">Nom</dt>
                        <dd class="truncate text-ink-900 dark:text-sand-50">{{ $conversation->client->nom_complet }}</dd>
                    </div>
                    @if ($conversation->client->email)
                        <div class="flex items-baseline justify-between gap-3">
                            <dt class="shrink-0 text-xs uppercase tracking-wider text-ink-400 dark:text-sand-500">Email</dt>
                            <dd class="truncate text-ink-700 dark:text-sand-200">{{ $conversation->client->email }}</dd>
                        </div>
                    @endif
                    @if ($conversation->client->telephone)
                        <div class="flex items-baseline justify-between gap-3">
                            <dt class="shrink-0 text-xs uppercase tracking-wider text-ink-400 dark:text-sand-500">Téléphone</dt>
                            <dd class="truncate text-ink-700 dark:text-sand-200">{{ $conversation->client->telephone }}</dd>
                        </div>
                    @endif
                    @if ($conversation->client->company)
                        <div class="flex items-baseline justify-between gap-3">
                            <dt class="shrink-0 text-xs uppercase tracking-wider text-ink-400 dark:text-sand-500">Entreprise</dt>
                            <dd class="truncate text-ink-700 dark:text-sand-200">{{ $conversation->client->company->name }}</dd>
                        </div>
                    @endif
                    <div class="flex items-baseline justify-between gap-3">
                        <dt class="shrink-0 text-xs uppercase tracking-wider text-ink-400 dark:text-sand-500">Conversations</dt>
                        <dd class="text-ink-700 dark:text-sand-200">{{ $conversation->client->conversations()->count() }}</dd>
                    </div>
                </dl>

                <a href="{{ route('clients.show', $conversation->client) }}" class="mt-4 inline-flex items-center gap-1.5 text-sm font-medium text-ink-600 hover:underline dark:text-sand-300">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                    Voir la fiche complète
                </a>
            </x-card>
        </aside>
    </div>
</x-app-layout>
