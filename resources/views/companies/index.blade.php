<x-app-layout title="Entreprises">
    <x-slot name="header">
        <x-page-header title="Entreprises" subtitle="Regroupez vos clients par entreprise.">
            <x-slot name="actions">
                <a href="{{ route('companies.create') }}">
                    <x-primary-button type="button">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Nouvelle entreprise
                    </x-primary-button>
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <form method="GET" class="mb-4 max-w-sm">
        <x-text-input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher une entreprise…" />
    </form>

    <x-card class="!p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-sand-200 dark:divide-ink-800">
                <thead>
                    <tr class="text-left text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-sand-500">
                        <th class="px-5 py-3">Nom</th>
                        <th class="px-5 py-3">Domaine</th>
                        <th class="px-5 py-3">Téléphone</th>
                        <th class="px-5 py-3">Clients</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sand-100 dark:divide-ink-800">
                    @forelse ($companies as $company)
                        <tr class="text-sm hover:bg-sand-50 dark:hover:bg-ink-800">
                            <td class="px-5 py-3 font-medium text-ink-900 dark:text-sand-50">{{ $company->name }}</td>
                            <td class="px-5 py-3 text-ink-500 dark:text-sand-400">{{ $company->domain ?: '—' }}</td>
                            <td class="px-5 py-3 text-ink-500 dark:text-sand-400">{{ $company->phone ?: '—' }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex min-w-6 items-center justify-center rounded-full bg-sand-100 px-2 py-0.5 text-xs font-semibold text-ink-700 dark:bg-ink-800 dark:text-sand-200">{{ $company->clients_count }}</span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('companies.edit', $company) }}" class="rounded-lg p-1.5 text-ink-500 hover:bg-sand-100 hover:text-ink-900 dark:text-sand-400 dark:hover:bg-ink-700 dark:hover:text-sand-50" title="Modifier">
                                    <svg class="h-4.5 w-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" /></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-14"><x-empty-state title="Aucune entreprise enregistrée" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mt-4">{{ $companies->links() }}</div>
</x-app-layout>
