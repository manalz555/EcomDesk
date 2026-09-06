<x-app-layout title="Agents">
    <x-slot name="header">
        <x-page-header title="Comptes de l'équipe" subtitle="Gérez les accès et rôles de votre équipe.">
            <x-slot name="actions">
                <a href="{{ route('agents.create') }}">
                    <x-primary-button type="button">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Nouveau compte
                    </x-primary-button>
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <x-card class="!p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-sand-200 dark:divide-ink-800">
                <thead>
                    <tr class="text-left text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-sand-500">
                        <th class="px-5 py-3">Nom</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Rôle</th>
                        <th class="px-5 py-3">Conversations</th>
                        <th class="px-5 py-3">Statut</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sand-100 dark:divide-ink-800">
                    @forelse ($agents as $agent)
                        <tr class="text-sm hover:bg-sand-50 dark:hover:bg-ink-800">
                            <td class="px-5 py-3 font-medium text-ink-900 dark:text-sand-50">{{ $agent->name }}</td>
                            <td class="px-5 py-3 text-ink-500 dark:text-sand-400">{{ $agent->email }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize
                                    {{ $agent->role === 'admin' ? 'bg-ink-900 text-sand-50 dark:bg-sand-100 dark:text-ink-900' : 'bg-sand-100 text-ink-700 dark:bg-ink-800 dark:text-sand-200' }}">
                                    {{ $agent->role === 'admin' ? 'Administrateur' : ($agent->role === 'manager' ? 'Manager' : 'Agent') }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex min-w-6 items-center justify-center rounded-full bg-sand-100 px-2 py-0.5 text-xs font-semibold text-ink-700 dark:bg-ink-800 dark:text-sand-200">{{ $agent->conversations_count }}</span>
                            </td>
                            <td class="px-5 py-3">
                                @if ($agent->actif)
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-950 dark:text-emerald-300 dark:ring-emerald-400/20">Actif</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-sand-200 px-2.5 py-0.5 text-xs font-medium text-ink-700 ring-1 ring-inset ring-ink-600/10 dark:bg-ink-700 dark:text-sand-200 dark:ring-sand-400/10">Désactivé</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('agents.edit', $agent) }}" class="rounded-lg p-1.5 text-ink-500 hover:bg-sand-100 hover:text-ink-900 dark:text-sand-400 dark:hover:bg-ink-700 dark:hover:text-sand-50" title="Modifier">
                                    <svg class="h-4.5 w-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" /></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-14"><x-empty-state title="Aucun compte enregistré" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mt-4">{{ $agents->links() }}</div>

    <x-card class="mt-6">
        <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Rôles &amp; permissions</h2>
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-sand-500">Administrateur</p>
                <p class="mt-1 text-sm text-ink-600 dark:text-sand-300">Accès complet : clients, conversations, comptes, équipes, entreprises, paramètres.</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-sand-500">Manager</p>
                <p class="mt-1 text-sm text-ink-600 dark:text-sand-300">Gère les équipes et supervise les conversations, sans accès aux comptes ni aux paramètres sensibles.</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-sand-500">Agent</p>
                <p class="mt-1 text-sm text-ink-600 dark:text-sand-300">Consulte et répond aux conversations, crée des fiches clients, sans droits de suppression.</p>
            </div>
        </div>
    </x-card>
</x-app-layout>
