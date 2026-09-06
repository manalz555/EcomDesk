<x-app-layout title="Clients">
    <x-slot name="header">
        <x-page-header title="Clients" subtitle="Fiches clients et historique des échanges.">
            <x-slot name="actions">
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('custom-fields.index') }}"><x-secondary-button type="button">Champs personnalisés</x-secondary-button></a>
                    <a href="{{ route('companies.index') }}"><x-secondary-button type="button">Entreprises</x-secondary-button></a>
                @endif
                <a href="{{ route('clients.create') }}">
                    <x-primary-button type="button">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Nouveau client
                    </x-primary-button>
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <form method="GET" class="mb-4 max-w-sm">
        <div class="relative">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4.5 w-4.5 -translate-y-1/2 text-ink-400 dark:text-sand-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
            <x-text-input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un client…" class="pl-9" />
        </div>
    </form>

    <x-card class="!p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-sand-200 dark:divide-ink-800">
                <thead>
                    <tr class="text-left text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-sand-500">
                        <th class="px-5 py-3">Nom</th>
                        <th class="px-5 py-3">Entreprise</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Téléphone</th>
                        <th class="px-5 py-3">Conversations</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sand-100 dark:divide-ink-800">
                    @forelse ($clients as $client)
                        <tr class="text-sm hover:bg-sand-50 dark:hover:bg-ink-800">
                            <td class="px-5 py-3">
                                <a href="{{ route('clients.show', $client) }}" class="font-medium text-ink-900 hover:underline dark:text-sand-50">{{ $client->nom_complet }}</a>
                            </td>
                            <td class="px-5 py-3 text-ink-500 dark:text-sand-400">{{ $client->company->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-ink-500 dark:text-sand-400">{{ $client->email ?: '—' }}</td>
                            <td class="px-5 py-3 text-ink-500 dark:text-sand-400">{{ $client->telephone ?: '—' }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex min-w-6 items-center justify-center rounded-full bg-sand-100 px-2 py-0.5 text-xs font-semibold text-ink-700 dark:bg-ink-800 dark:text-sand-200">{{ $client->conversations_count }}</span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('clients.show', $client) }}" class="rounded-lg p-1.5 text-ink-500 hover:bg-sand-100 hover:text-ink-900 dark:text-sand-400 dark:hover:bg-ink-700 dark:hover:text-sand-50" title="Voir">
                                        <svg class="h-4.5 w-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                    </a>
                                    @if (auth()->user()->isAdmin())
                                        <a href="{{ route('clients.edit', $client) }}" class="rounded-lg p-1.5 text-ink-500 hover:bg-sand-100 hover:text-ink-900 dark:text-sand-400 dark:hover:bg-ink-700 dark:hover:text-sand-50" title="Modifier">
                                            <svg class="h-4.5 w-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" /></svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-14"><x-empty-state title="Aucun client trouvé" description="Ajoutez un client ou créez-en un depuis une nouvelle conversation." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mt-4">{{ $clients->links() }}</div>
</x-app-layout>
