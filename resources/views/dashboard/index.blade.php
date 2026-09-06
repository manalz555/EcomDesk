<x-app-layout title="Tableau de bord">
    <x-slot name="header">
        <x-page-header title="Tableau de bord" subtitle="Vue d'ensemble de l'activité du service client." />
    </x-slot>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-card>
            <p class="text-sm text-ink-500 dark:text-sand-400">Clients</p>
            <p class="mt-1 text-3xl font-semibold text-ink-900 dark:text-sand-50">{{ $totalClients }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-ink-500 dark:text-sand-400">Conversations</p>
            <p class="mt-1 text-3xl font-semibold text-ink-900 dark:text-sand-50">{{ $totalConversations }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-ink-500 dark:text-sand-400">En attente</p>
            <p class="mt-1 text-3xl font-semibold text-amber-600 dark:text-amber-400">{{ $enAttente }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-ink-500 dark:text-sand-400">Résolues</p>
            <p class="mt-1 text-3xl font-semibold text-emerald-600 dark:text-emerald-400">{{ $resolues }}</p>
        </x-card>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-1">
            <x-card>
                <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Répartition par catégorie</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($parCategorie as $categorie => $total)
                        <div class="flex items-center justify-between text-sm">
                            <span class="capitalize text-ink-600 dark:text-sand-300">{{ str_replace('_', ' ', $categorie) }}</span>
                            <span class="inline-flex min-w-6 items-center justify-center rounded-full bg-sand-100 px-2 py-0.5 text-xs font-semibold text-ink-700 dark:bg-ink-800 dark:text-sand-200">{{ $total }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-ink-400 dark:text-sand-500">Aucune conversation pour le moment.</p>
                    @endforelse
                </div>
            </x-card>

            <x-card>
                <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Statuts</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between"><x-status-badge status="nouveau" /><span class="font-medium text-ink-700 dark:text-sand-200">{{ $nouvelles }}</span></div>
                    <div class="flex items-center justify-between"><x-status-badge status="en_cours" /><span class="font-medium text-ink-700 dark:text-sand-200">{{ $enCours }}</span></div>
                    <div class="flex items-center justify-between"><x-status-badge status="en_attente" /><span class="font-medium text-ink-700 dark:text-sand-200">{{ $enAttente }}</span></div>
                    <div class="flex items-center justify-between"><x-status-badge status="resolu" /><span class="font-medium text-ink-700 dark:text-sand-200">{{ $resolues }}</span></div>
                </div>
            </x-card>
        </div>

        <div class="lg:col-span-2">
            <x-card class="!p-0">
                <div class="flex items-center justify-between px-5 py-4">
                    <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Dernières conversations</h2>
                    <a href="{{ route('conversations.index') }}" class="text-sm font-medium text-ink-600 hover:underline dark:text-sand-300">Tout voir</a>
                </div>
                <div class="overflow-x-auto border-t border-sand-200 dark:border-ink-800">
                    <table class="min-w-full divide-y divide-sand-200 dark:divide-ink-800">
                        <thead>
                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-sand-500">
                                <th class="px-5 py-2.5">Client</th>
                                <th class="px-5 py-2.5">Sujet</th>
                                <th class="px-5 py-2.5">Canal</th>
                                <th class="px-5 py-2.5">Statut</th>
                                <th class="px-5 py-2.5">Priorité</th>
                                <th class="px-5 py-2.5">Agent</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sand-100 dark:divide-ink-800">
                            @forelse ($dernieresConversations as $conv)
                                <tr class="cursor-pointer text-sm hover:bg-sand-50 dark:hover:bg-ink-800" onclick="window.location='{{ route('conversations.show', $conv) }}'">
                                    <td class="px-5 py-3 font-medium text-ink-900 dark:text-sand-50">{{ $conv->client->nom_complet }}</td>
                                    <td class="px-5 py-3 text-ink-600 dark:text-sand-300">{{ Str::limit($conv->sujet, 30) }}</td>
                                    <td class="px-5 py-3 capitalize text-ink-500 dark:text-sand-400">{{ $conv->canal }}</td>
                                    <td class="px-5 py-3"><x-status-badge :status="$conv->statut" /></td>
                                    <td class="px-5 py-3"><x-priority-badge :priority="$conv->priorite" /></td>
                                    <td class="px-5 py-3 text-ink-500 dark:text-sand-400">{{ $conv->agent->name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-ink-400 dark:text-sand-500">Aucune conversation enregistrée.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>

    <div class="mt-6">
        <x-card>
            <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Activité récente</h2>
            <ul class="mt-4 space-y-4">
                @forelse ($activites as $activite)
                    <li class="flex gap-3 text-sm">
                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-sand-400 dark:bg-sand-600"></span>
                        <div class="min-w-0">
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
    </div>
</x-app-layout>
