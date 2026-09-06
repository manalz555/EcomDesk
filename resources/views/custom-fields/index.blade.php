<x-app-layout title="Champs personnalisés">
    <x-slot name="header">
        <x-page-header title="Champs personnalisés" subtitle="Ajoutez des informations sur mesure aux fiches clients." />
    </x-slot>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <x-card>
            <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Nouveau champ</h2>
            <form method="POST" action="{{ route('custom-fields.store') }}" class="mt-4 space-y-4" x-data="{ type: 'text' }">
                @csrf
                <div>
                    <x-input-label for="label" value="Nom du champ" />
                    <x-text-input id="label" type="text" name="label" :value="old('label')" required autofocus />
                    <x-input-error :messages="$errors->get('label')" class="mt-1.5" />
                </div>
                <div>
                    <x-input-label for="type" value="Type" />
                    <x-select-input id="type" name="type" x-model="type">
                        <option value="text">Texte</option>
                        <option value="number">Nombre</option>
                        <option value="date">Date</option>
                        <option value="select">Liste déroulante</option>
                    </x-select-input>
                </div>
                <div x-show="type === 'select'" x-cloak>
                    <x-input-label for="options" value="Options (séparées par une virgule)" />
                    <x-text-input id="options" type="text" name="options" :value="old('options')" placeholder="Or, Argent, Platine" />
                </div>
                <x-primary-button>Ajouter le champ</x-primary-button>
            </form>
        </x-card>

        <div class="lg:col-span-2">
            <x-card class="!p-0">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-sand-200 dark:divide-ink-800">
                        <thead>
                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-sand-500">
                                <th class="px-5 py-3">Nom</th>
                                <th class="px-5 py-3">Type</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sand-100 dark:divide-ink-800">
                            @forelse ($customFields as $field)
                                <tr class="text-sm">
                                    <td class="px-5 py-3 font-medium text-ink-900 dark:text-sand-50">{{ $field->label }}</td>
                                    <td class="px-5 py-3 capitalize text-ink-500 dark:text-sand-400">{{ $field->type }}</td>
                                    <td class="px-5 py-3 text-right">
                                        <form method="POST" action="{{ route('custom-fields.destroy', $field) }}" onsubmit="return confirm('Supprimer ce champ ? Les valeurs associées seront perdues.');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="rounded-lg p-1.5 text-ink-500 hover:bg-sand-100 hover:text-rose-600 dark:text-sand-400 dark:hover:bg-ink-700">
                                                <svg class="h-4.5 w-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-5 py-14"><x-empty-state title="Aucun champ personnalisé" description="Créez-en un pour enrichir vos fiches clients." /></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
