<x-app-layout title="Intégrations">
    <x-slot name="header">
        <x-page-header title="Intégrations" subtitle="Connectez vos canaux et configurez l'automatisation par IA." />
    </x-slot>

    <div class="space-y-6">
        <x-card>
            <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Canaux &amp; services externes</h2>
            <p class="mt-1 text-sm text-ink-500 dark:text-sand-400">
                Renseignez une clé d'API pour activer une intégration. Sans clé, les canaux restent simulés.
            </p>

            <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach ($integrations as $integration)
                    @php $meta = $channels[$integration->channel] ?? ['label' => ucfirst($integration->channel), 'field' => 'Clé API']; @endphp
                    <div class="rounded-xl border border-sand-200 p-4 dark:border-ink-700" x-data="{ editing: false }">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-ink-900 dark:text-sand-50">{{ $meta['label'] }}</p>
                            @if ($integration->is_connected)
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-950 dark:text-emerald-300 dark:ring-emerald-400/20">Connecté</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-sand-200 px-2.5 py-0.5 text-xs font-medium text-ink-700 ring-1 ring-inset ring-ink-600/10 dark:bg-ink-700 dark:text-sand-200 dark:ring-sand-400/10">Non connecté</span>
                            @endif
                        </div>

                        @if ($integration->is_connected)
                            <p class="mt-2 text-xs text-ink-400 dark:text-sand-500">
                                Connecté {{ $integration->connected_at?->diffForHumans() }}
                            </p>
                            <div class="mt-3 flex items-center gap-2">
                                <x-secondary-button type="button" @click="editing = ! editing">Modifier la clé</x-secondary-button>
                                <form method="POST" action="{{ route('integrations.disconnect', $integration) }}" onsubmit="return confirm('Déconnecter cette intégration ?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-rose-600 hover:underline dark:text-rose-400">Déconnecter</button>
                                </form>
                            </div>
                        @endif

                        <form
                            method="POST"
                            action="{{ route('integrations.update', $integration) }}"
                            class="mt-3 space-y-2"
                            @if ($integration->is_connected) x-show="editing" x-cloak @endif
                        >
                            @csrf
                            @method('PATCH')
                            <x-input-label :value="$meta['field']" class="sr-only" />
                            <x-text-input type="password" name="api_key" placeholder="{{ $meta['field'] }}" class="w-full text-sm" autocomplete="off" />
                            <x-primary-button type="submit" class="w-full justify-center text-xs">
                                {{ $integration->is_connected ? 'Mettre à jour' : 'Connecter' }}
                            </x-primary-button>
                        </form>
                    </div>
                @endforeach
            </div>
        </x-card>

        <x-card>
            <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Règles d'automatisation</h2>
            <p class="mt-1 text-sm text-ink-500 dark:text-sand-400">
                Dès qu'une conversation correspond à une règle active (canal et/ou catégorie), l'IA génère un brouillon de
                réponse — jamais envoyé automatiquement. Un agent doit le valider (ou le modifier puis le valider) avant
                qu'il ne parte au client. Sans clé OpenAI connectée, un brouillon simulé est généré à la place.
            </p>

            <div class="mt-4 overflow-x-auto rounded-lg border border-sand-200 dark:border-ink-700">
                <table class="min-w-full divide-y divide-sand-200 dark:divide-ink-800">
                    <thead>
                        <tr class="text-left text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-sand-500">
                            <th class="px-4 py-2.5">Règle</th>
                            <th class="px-4 py-2.5">Agent</th>
                            <th class="px-4 py-2.5">Canal</th>
                            <th class="px-4 py-2.5">Catégorie</th>
                            <th class="px-4 py-2.5">Statut</th>
                            <th class="px-4 py-2.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sand-100 dark:divide-ink-800">
                        @forelse ($rules as $rule)
                            <tr class="text-sm">
                                <td class="px-4 py-3 font-medium text-ink-900 dark:text-sand-50">{{ $rule->name }}</td>
                                <td class="px-4 py-3 text-ink-500 dark:text-sand-400">{{ $rule->bot->name ?? '—' }}</td>
                                <td class="px-4 py-3 capitalize text-ink-500 dark:text-sand-400">{{ $rule->canal ? str_replace('_', ' ', $rule->canal) : 'Tous' }}</td>
                                <td class="px-4 py-3 capitalize text-ink-500 dark:text-sand-400">{{ $rule->categorie ?: 'Toutes' }}</td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('automation-rules.toggle', $rule) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset
                                            {{ $rule->enabled
                                                ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-950 dark:text-emerald-300 dark:ring-emerald-400/20'
                                                : 'bg-sand-200 text-ink-700 ring-ink-600/10 dark:bg-ink-700 dark:text-sand-200 dark:ring-sand-400/10' }}">
                                            {{ $rule->enabled ? 'Activée' : 'Désactivée' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('automation-rules.destroy', $rule) }}" onsubmit="return confirm('Supprimer cette règle ?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="rounded-lg p-1.5 text-ink-500 hover:bg-sand-100 hover:text-rose-600 dark:text-sand-400 dark:hover:bg-ink-700">
                                            <svg class="h-4.5 w-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-10 text-center text-sm text-ink-400 dark:text-sand-500">Aucune règle configurée.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($bots->isNotEmpty())
                <form method="POST" action="{{ route('automation-rules.store') }}" class="mt-5 space-y-4 rounded-lg border border-sand-200 p-4 dark:border-ink-700">
                    @csrf
                    <p class="text-sm font-medium text-ink-700 dark:text-sand-200">Nouvelle règle</p>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <x-input-label for="rule_name" value="Nom" />
                            <x-text-input id="rule_name" type="text" name="name" required />
                        </div>
                        <div>
                            <x-input-label for="bot_user_id" value="Agent automatisé" />
                            <x-select-input id="bot_user_id" name="bot_user_id" required>
                                @foreach ($bots as $bot)
                                    <option value="{{ $bot->id }}">{{ $bot->name }}</option>
                                @endforeach
                            </x-select-input>
                        </div>
                        <div>
                            <x-input-label for="rule_canal" value="Canal (optionnel)" />
                            <x-select-input id="rule_canal" name="canal">
                                <option value="">Tous les canaux</option>
                                @foreach ($canaux as $c)
                                    <option value="{{ $c }}">{{ ucfirst(str_replace('_', ' ', $c)) }}</option>
                                @endforeach
                            </x-select-input>
                        </div>
                        <div>
                            <x-input-label for="rule_categorie" value="Catégorie (optionnel)" />
                            <x-select-input id="rule_categorie" name="categorie">
                                <option value="">Toutes catégories</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c }}">{{ ucfirst($c) }}</option>
                                @endforeach
                            </x-select-input>
                        </div>
                    </div>

                    <div>
                        <x-input-label for="prompt_template" value="Instructions pour l'IA" />
                        <x-textarea-input id="prompt_template" name="prompt_template" rows="2" placeholder="Tu es l'assistant du service client d'EcomDesk..."></x-textarea-input>
                    </div>

                    <x-primary-button type="submit">Créer la règle</x-primary-button>
                </form>
            @endif
        </x-card>
    </div>
</x-app-layout>
