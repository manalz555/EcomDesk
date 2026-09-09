@php
    // Les quatre statuts et leur part du total : une barre de proportion dit
    // en un coup d'oeil ce qu'une colonne de nombres oblige a comparer.
    $repartition = [
        ['cle' => 'nouveau',    'libelle' => 'Nouveau',    'n' => $nouvelles, 'barre' => 'bg-sky-500'],
        ['cle' => 'en_cours',   'libelle' => 'En cours',   'n' => $enCours,   'barre' => 'bg-amber-500'],
        ['cle' => 'en_attente', 'libelle' => 'En attente', 'n' => $enAttente, 'barre' => 'bg-orange-400'],
        ['cle' => 'resolu',     'libelle' => 'Résolu',     'n' => $resolues,  'barre' => 'bg-emerald-500'],
    ];
    $totalStatuts = max(1, array_sum(array_column($repartition, 'n')));
    $maxCategorie = max(1, $parCategorie->max() ?? 1);
@endphp

<x-app-layout title="Tableau de bord">
    <x-slot name="header">
        <x-page-header title="Tableau de bord" subtitle="Vue d'ensemble de l'activité du service client." />
    </x-slot>

    {{-- ================================================== À TRAITER ==== --}}
    {{-- Chaque compteur est un lien vers la liste deja filtree : le chiffre
         ne se contente pas d'informer, il ouvre le travail correspondant. --}}
    <section>
        <h2 class="text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-sand-500">À traiter</h2>

        <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
            <a href="{{ route('conversations.index', ['non_assignees' => 1]) }}"
               class="group rounded-xl border border-sand-200 bg-white p-5 transition hover:border-ink-300 dark:border-ink-800 dark:bg-ink-900 dark:hover:border-ink-600">
                <p class="text-sm text-ink-500 dark:text-sand-400">Non assignées</p>
                <p class="mt-1 text-3xl font-semibold tabular-nums text-ink-900 dark:text-sand-50">{{ $nonAssignees }}</p>
                <p class="mt-1 text-xs text-ink-400 group-hover:text-ink-600 dark:text-sand-500 dark:group-hover:text-sand-300">
                    Personne ne s'en occupe encore
                </p>
            </a>

            <a href="{{ route('conversations.index') }}"
               class="group rounded-xl border p-5 transition {{ $brouillonsAValider > 0
                    ? 'border-sand-400 bg-sand-50 hover:border-sand-600 dark:border-sand-600/50 dark:bg-ink-900'
                    : 'border-sand-200 bg-white hover:border-ink-300 dark:border-ink-800 dark:bg-ink-900 dark:hover:border-ink-600' }}">
                <p class="flex items-center gap-1.5 text-sm text-ink-500 dark:text-sand-400">
                    <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" /></svg>
                    Brouillons à valider
                </p>
                <p class="mt-1 text-3xl font-semibold tabular-nums text-ink-900 dark:text-sand-50">{{ $brouillonsAValider }}</p>
                <p class="mt-1 text-xs text-ink-400 group-hover:text-ink-600 dark:text-sand-500 dark:group-hover:text-sand-300">
                    Rédigés par l'assistant, non envoyés
                </p>
            </a>

            <a href="{{ route('conversations.index', ['mes_conversations' => 1]) }}"
               class="group rounded-xl border border-sand-200 bg-white p-5 transition hover:border-ink-300 dark:border-ink-800 dark:bg-ink-900 dark:hover:border-ink-600">
                <p class="text-sm text-ink-500 dark:text-sand-400">Mes conversations ouvertes</p>
                <p class="mt-1 text-3xl font-semibold tabular-nums text-ink-900 dark:text-sand-50">{{ $mesConversations }}</p>
                <p class="mt-1 text-xs text-ink-400 group-hover:text-ink-600 dark:text-sand-500 dark:group-hover:text-sand-300">
                    Qui vous sont assignées
                </p>
            </a>
        </div>
    </section>

    {{-- ================================================== ÉTAT DU FLUX == --}}
    <section class="mt-8 grid grid-cols-1 gap-4 lg:grid-cols-3">

        <div class="space-y-4">
            <x-card>
                <div class="flex items-baseline justify-between">
                    <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Répartition par statut</h2>
                    <span class="text-xs tabular-nums text-ink-400 dark:text-sand-500">{{ $totalConversations }} au total</span>
                </div>

                {{-- Barre de proportion : la part de chaque statut se lit sans comparer des nombres. --}}
                <div class="mt-3 flex h-2 overflow-hidden rounded-full bg-sand-100 dark:bg-ink-800">
                    @foreach ($repartition as $part)
                        @if ($part['n'] > 0)
                            <div class="{{ $part['barre'] }}"
                                 style="width: {{ round($part['n'] / $totalStatuts * 100, 2) }}%"
                                 title="{{ $part['libelle'] }} : {{ $part['n'] }}"></div>
                        @endif
                    @endforeach
                </div>

                <ul class="mt-4 space-y-2.5">
                    @foreach ($repartition as $part)
                        <li class="flex items-center gap-2.5 text-sm">
                            <span class="h-2 w-2 shrink-0 rounded-full {{ $part['barre'] }}"></span>
                            <span class="text-ink-600 dark:text-sand-300">{{ $part['libelle'] }}</span>
                            <span class="ms-auto tabular-nums font-medium text-ink-900 dark:text-sand-50">{{ $part['n'] }}</span>
                            <span class="w-10 text-right text-xs tabular-nums text-ink-400 dark:text-sand-500">
                                {{ round($part['n'] / $totalStatuts * 100) }}%
                            </span>
                        </li>
                    @endforeach
                </ul>
            </x-card>

            <x-card>
                <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Motifs de contact</h2>

                <ul class="mt-4 space-y-2.5">
                    @forelse ($parCategorie->sortDesc() as $categorie => $total)
                        <li class="text-sm">
                            <div class="flex items-baseline justify-between gap-3">
                                <span class="capitalize text-ink-600 dark:text-sand-300">{{ str_replace('_', ' ', $categorie) }}</span>
                                <span class="tabular-nums font-medium text-ink-900 dark:text-sand-50">{{ $total }}</span>
                            </div>
                            <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-sand-100 dark:bg-ink-800">
                                <div class="h-full rounded-full bg-sand-500 dark:bg-sand-600" style="width: {{ round($total / $maxCategorie * 100, 2) }}%"></div>
                            </div>
                        </li>
                    @empty
                        <p class="text-sm text-ink-400 dark:text-sand-500">Aucune conversation pour le moment.</p>
                    @endforelse
                </ul>
            </x-card>
        </div>

        {{-- ---------------------------------------- dernières conversations --}}
        <div class="lg:col-span-2">
            <x-card class="!p-0">
                <div class="flex items-center justify-between px-5 py-4">
                    <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Dernières conversations</h2>
                    <a href="{{ route('conversations.index') }}" class="text-sm font-medium text-ink-600 hover:underline dark:text-sand-300">Tout voir</a>
                </div>

                <ul class="divide-y divide-sand-100 border-t border-sand-200 dark:divide-ink-800 dark:border-ink-800">
                    @forelse ($dernieresConversations as $conv)
                        <li class="flex items-center gap-4 px-5 py-3 transition hover:bg-sand-50 dark:hover:bg-ink-800/60">
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('conversations.show', $conv) }}"
                                   class="block truncate text-sm font-medium text-ink-900 hover:underline dark:text-sand-50">
                                    {{ $conv->sujet }}
                                </a>
                                <p class="mt-0.5 truncate text-xs text-ink-500 dark:text-sand-400">
                                    {{ $conv->client->nom_complet }}
                                    &middot; <span class="capitalize">{{ str_replace('_', ' ', $conv->canal) }}</span>
                                    &middot; {{ $conv->agent->name ?? 'non assignée' }}
                                </p>
                            </div>
                            <div class="flex shrink-0 items-center gap-2">
                                <x-priority-badge :priority="$conv->priorite" />
                                <x-status-badge :status="$conv->statut" />
                            </div>
                        </li>
                    @empty
                        <li class="px-5 py-10 text-center text-sm text-ink-400 dark:text-sand-500">Aucune conversation enregistrée.</li>
                    @endforelse
                </ul>
            </x-card>
        </div>
    </section>

    {{-- ================================================== ACTIVITÉ ====== --}}
    <section class="mt-8">
        <x-card>
            <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Activité récente</h2>
            <p class="text-xs text-ink-400 dark:text-sand-500">Issue du journal d'audit : chaque action sensible y laisse une trace.</p>

            <ul class="mt-4 space-y-3.5">
                @forelse ($activites as $activite)
                    <li class="flex gap-3 text-sm">
                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-sand-400 dark:bg-sand-600"></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-ink-700 dark:text-sand-200">{{ $activite->description }}</p>
                            <p class="text-xs text-ink-400 dark:text-sand-500">
                                {{ $activite->actor->name ?? 'Système' }} &middot; {{ $activite->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </li>
                @empty
                    <p class="text-sm text-ink-400 dark:text-sand-500">Aucune activité pour le moment.</p>
                @endforelse
            </ul>
        </x-card>
    </section>
</x-app-layout>
