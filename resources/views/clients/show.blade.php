<x-app-layout :title="$client->nom_complet">
    <x-slot name="header">
        <x-page-header :title="$client->nom_complet" subtitle="Fiche client et historique des conversations." />
    </x-slot>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="space-y-4">
            <x-card>
                <p class="text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-sand-500">Entreprise</p>
                <p class="mt-1 text-sm text-ink-900 dark:text-sand-50">{{ $client->company->name ?? '—' }}</p>

                <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-sand-500">Email</p>
                <p class="mt-1 text-sm text-ink-900 dark:text-sand-50">{{ $client->email ?: '—' }}</p>

                <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-sand-500">Téléphone</p>
                <p class="mt-1 text-sm text-ink-900 dark:text-sand-50">{{ $client->telephone ?: '—' }}</p>

                <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-sand-500">Notes</p>
                <p class="mt-1 whitespace-pre-line text-sm text-ink-900 dark:text-sand-50">{{ $client->notes ?: '—' }}</p>

                @if (auth()->user()->isAdmin())
                    <a href="{{ route('clients.edit', $client) }}" class="mt-5 inline-block">
                        <x-secondary-button type="button">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" /></svg>
                            Modifier
                        </x-secondary-button>
                    </a>
                @endif
            </x-card>

            @if ($customFields->isNotEmpty())
                <x-card>
                    <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Champs personnalisés</h2>
                    <form method="POST" action="{{ route('clients.custom-fields.update', $client) }}" class="mt-3 space-y-3">
                        @csrf
                        @method('PATCH')
                        @foreach ($customFields as $field)
                            @php
                                $existing = $client->customFieldValues->firstWhere('custom_field_id', $field->id);
                            @endphp
                            <div>
                                <x-input-label :value="$field->label" />
                                @if ($field->type === 'select')
                                    <x-select-input name="values[{{ $field->id }}]">
                                        <option value="">—</option>
                                        @foreach ($field->options ?? [] as $option)
                                            <option value="{{ $option }}" @selected($existing?->value === $option)>{{ $option }}</option>
                                        @endforeach
                                    </x-select-input>
                                @else
                                    <x-text-input
                                        :type="$field->type === 'number' ? 'number' : ($field->type === 'date' ? 'date' : 'text')"
                                        name="values[{{ $field->id }}]"
                                        :value="$existing?->value"
                                    />
                                @endif
                            </div>
                        @endforeach
                        <x-secondary-button type="submit">Enregistrer</x-secondary-button>
                    </form>
                </x-card>
            @endif
        </div>

        <div class="lg:col-span-2">
            <x-card class="!p-0">
                <div class="flex items-center justify-between px-5 py-4">
                    <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Conversations</h2>
                    <a href="{{ route('conversations.create', ['client_id' => $client->id]) }}">
                        <x-primary-button type="button">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            Nouvelle conversation
                        </x-primary-button>
                    </a>
                </div>
                <div class="overflow-x-auto border-t border-sand-200 dark:border-ink-800">
                    <table class="min-w-full divide-y divide-sand-200 dark:divide-ink-800">
                        <thead>
                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-sand-500">
                                <th class="px-5 py-2.5">Sujet</th>
                                <th class="px-5 py-2.5">Statut</th>
                                <th class="px-5 py-2.5">Priorité</th>
                                <th class="px-5 py-2.5">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sand-100 dark:divide-ink-800">
                            @forelse ($client->conversations as $conv)
                                <tr class="cursor-pointer text-sm hover:bg-sand-50 dark:hover:bg-ink-800" onclick="window.location='{{ route('conversations.show', $conv) }}'">
                                    <td class="px-5 py-3 font-medium text-ink-900 dark:text-sand-50">{{ $conv->sujet }}</td>
                                    <td class="px-5 py-3"><x-status-badge :status="$conv->statut" /></td>
                                    <td class="px-5 py-3"><x-priority-badge :priority="$conv->priorite" /></td>
                                    <td class="px-5 py-3 text-ink-500 dark:text-sand-400">{{ $conv->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-14"><x-empty-state title="Aucune conversation pour ce client" /></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
