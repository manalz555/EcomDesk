<x-app-layout title="Conversations">
    <x-slot name="header">
        <x-page-header title="Conversations" subtitle="Toutes les demandes clients, tous canaux confondus.">
            <x-slot name="actions">
                <a href="{{ route('conversations.create') }}">
                    <x-primary-button type="button">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Nouvelle conversation
                    </x-primary-button>
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    @php
        // Nombre de filtres actifs, affiché en pastille sur le bouton « Filtrer ».
        $filtresActifs = collect(['statut', 'priorite', 'canal', 'non_assignees', 'mes_conversations'])
            ->filter(fn ($f) => filled(request($f)))
            ->count();
    @endphp

    <form method="GET" class="mb-4 flex flex-wrap items-center gap-2">
        <div class="relative">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4.5 w-4.5 -translate-y-1/2 text-ink-400 dark:text-sand-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
            <x-text-input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher…" class="w-56 pl-9" />
        </div>

        {{-- Bouton « Filtrer » : regroupe toutes les options dans un panneau,
             comme sur une boutique e-commerce, plutôt qu'une rangée de champs. --}}
        <div x-data="{ open: false }" class="relative">
            <button
                type="button"
                @click="open = !open"
                class="flex items-center gap-2 rounded-lg border border-sand-300 bg-white px-3.5 py-2 text-sm font-medium text-ink-700 transition hover:bg-sand-100 dark:border-ink-700 dark:bg-ink-900 dark:text-sand-200 dark:hover:bg-ink-800"
            >
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                </svg>
                Filtrer
                @if ($filtresActifs > 0)
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-ink-900 text-[11px] font-semibold text-sand-50 dark:bg-sand-100 dark:text-ink-900">{{ $filtresActifs }}</span>
                @endif
                <svg class="h-3.5 w-3.5 text-ink-400 transition-transform dark:text-sand-500" :class="open && 'rotate-180'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </button>

            <div
                x-show="open"
                x-cloak
                @click.outside="open = false"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1"
                class="absolute left-0 z-30 mt-2 w-80 rounded-xl border border-sand-200 bg-white p-4 shadow-xl shadow-ink-900/10 dark:border-ink-700 dark:bg-ink-900"
            >
                <div class="space-y-3.5">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-sand-500">Statut</label>
                        <x-select-input name="statut" class="w-full">
                            <option value="">Tous les statuts</option>
                            @foreach ($statuts as $s)
                                <option value="{{ $s }}" @selected(request('statut') == $s)>{{ str_replace('_', ' ', ucfirst($s)) }}</option>
                            @endforeach
                        </x-select-input>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-sand-500">Priorité</label>
                        <x-select-input name="priorite" class="w-full">
                            <option value="">Toutes priorités</option>
                            @foreach ($priorites as $p)
                                <option value="{{ $p }}" @selected(request('priorite') == $p)>{{ ucfirst($p) }}</option>
                            @endforeach
                        </x-select-input>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-sand-500">Canal</label>
                        <x-select-input name="canal" class="w-full">
                            <option value="">Tous canaux</option>
                            @foreach ($canaux as $c)
                                <option value="{{ $c }}" @selected(request('canal') == $c)>{{ ucfirst(str_replace('_', ' ', $c)) }}</option>
                            @endforeach
                        </x-select-input>
                    </div>
                    <div class="space-y-2 border-t border-sand-200 pt-3 dark:border-ink-700">
                        <label class="flex items-center gap-2.5 text-sm text-ink-700 dark:text-sand-200">
                            <input type="checkbox" name="non_assignees" value="1" @checked(request('non_assignees')) class="rounded border-sand-300 text-ink-900 focus:ring-ink-500 dark:border-ink-600 dark:bg-ink-800">
                            Non assignées
                        </label>
                        <label class="flex items-center gap-2.5 text-sm text-ink-700 dark:text-sand-200">
                            <input type="checkbox" name="mes_conversations" value="1" @checked(request('mes_conversations')) class="rounded border-sand-300 text-ink-900 focus:ring-ink-500 dark:border-ink-600 dark:bg-ink-800">
                            Mes conversations
                        </label>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between border-t border-sand-200 pt-3.5 dark:border-ink-700">
                    <a href="{{ route('conversations.index') }}" class="text-xs font-medium text-ink-500 underline-offset-2 hover:text-ink-900 hover:underline dark:text-sand-400 dark:hover:text-sand-50">Réinitialiser</a>
                    <x-primary-button>Appliquer</x-primary-button>
                </div>
            </div>
        </div>

        {{-- Preserve the active sort when a filter changes. --}}
        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="direction" value="{{ $direction }}">
    </form>

    <x-card class="!p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-sand-200 dark:divide-ink-800">
                <thead>
                    <tr class="text-left text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-sand-500">
                        <th class="px-5 py-3">Client</th>
                        <th class="px-5 py-3"><x-sortable-header field="sujet" label="Sujet" :sort="$sort" :direction="$direction" /></th>
                        <th class="px-5 py-3">Canal</th>
                        <th class="px-5 py-3">Catégorie</th>
                        <th class="px-5 py-3">Étiquettes</th>
                        <th class="px-5 py-3"><x-sortable-header field="statut" label="Statut" :sort="$sort" :direction="$direction" /></th>
                        <th class="px-5 py-3"><x-sortable-header field="priorite" label="Priorité" :sort="$sort" :direction="$direction" /></th>
                        <th class="px-5 py-3">Agent</th>
                        <th class="px-5 py-3"><x-sortable-header field="date" label="Date" :sort="$sort" :direction="$direction" /></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sand-100 dark:divide-ink-800">
                    @forelse ($conversations as $conv)
                        <tr class="cursor-pointer text-sm hover:bg-sand-50 dark:hover:bg-ink-800" onclick="window.location='{{ route('conversations.show', $conv) }}'">
                            <td class="px-5 py-3 font-medium text-ink-900 dark:text-sand-50">{{ $conv->client->nom_complet }}</td>
                            <td class="px-5 py-3 text-ink-600 dark:text-sand-300">{{ Str::limit($conv->sujet, 35) }}</td>
                            <td class="px-5 py-3 capitalize text-ink-500 dark:text-sand-400">{{ str_replace('_', ' ', $conv->canal) }}</td>
                            <td class="px-5 py-3 capitalize text-ink-500 dark:text-sand-400">{{ $conv->categorie }}</td>
                            <td class="px-5 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($conv->tags as $tag)
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium" style="background-color: {{ $tag->color }}22; color: {{ $tag->color }};">{{ $tag->name }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-5 py-3"><x-status-badge :status="$conv->statut" /></td>
                            <td class="px-5 py-3"><x-priority-badge :priority="$conv->priorite" /></td>
                            <td class="px-5 py-3 text-ink-500 dark:text-sand-400">{{ $conv->agent->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-ink-500 dark:text-sand-400">{{ $conv->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-5 py-14"><x-empty-state title="Aucune conversation trouvée" description="Ajustez vos filtres ou créez une nouvelle conversation." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mt-4">{{ $conversations->links() }}</div>
</x-app-layout>
