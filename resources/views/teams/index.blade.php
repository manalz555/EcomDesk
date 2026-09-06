<x-app-layout title="Équipes">
    <x-slot name="header">
        <x-page-header title="Équipes" subtitle="Organisez vos agents par équipe.">
            <x-slot name="actions">
                <a href="{{ route('teams.create') }}">
                    <x-primary-button type="button">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Nouvelle équipe
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
                        <th class="px-5 py-3">Description</th>
                        <th class="px-5 py-3">Membres</th>
                        <th class="px-5 py-3">Conversations</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sand-100 dark:divide-ink-800">
                    @forelse ($teams as $team)
                        <tr class="text-sm hover:bg-sand-50 dark:hover:bg-ink-800">
                            <td class="px-5 py-3 font-medium text-ink-900 dark:text-sand-50">{{ $team->name }}</td>
                            <td class="px-5 py-3 text-ink-500 dark:text-sand-400">{{ Str::limit($team->description, 50) ?: '—' }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex min-w-6 items-center justify-center rounded-full bg-sand-100 px-2 py-0.5 text-xs font-semibold text-ink-700 dark:bg-ink-800 dark:text-sand-200">{{ $team->users_count }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex min-w-6 items-center justify-center rounded-full bg-sand-100 px-2 py-0.5 text-xs font-semibold text-ink-700 dark:bg-ink-800 dark:text-sand-200">{{ $team->conversations_count }}</span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('teams.edit', $team) }}" class="rounded-lg p-1.5 text-ink-500 hover:bg-sand-100 hover:text-ink-900 dark:text-sand-400 dark:hover:bg-ink-700 dark:hover:text-sand-50" title="Modifier">
                                        <svg class="h-4.5 w-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" /></svg>
                                    </a>
                                    @if (auth()->user()->isAdmin())
                                        <form method="POST" action="{{ route('teams.destroy', $team) }}" onsubmit="return confirm('Supprimer cette équipe ?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="rounded-lg p-1.5 text-ink-500 hover:bg-sand-100 hover:text-rose-600 dark:text-sand-400 dark:hover:bg-ink-700">
                                                <svg class="h-4.5 w-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-14"><x-empty-state title="Aucune équipe créée" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mt-4">{{ $teams->links() }}</div>
</x-app-layout>
