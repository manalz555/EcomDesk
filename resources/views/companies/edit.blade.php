<x-app-layout title="Modifier entreprise">
    <x-slot name="header">
        <x-page-header :title="'Modifier ' . $company->name" />
    </x-slot>

    <x-card class="max-w-xl">
        <form method="POST" action="{{ route('companies.update', $company) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <div>
                <x-input-label for="name" value="Nom" />
                <x-text-input id="name" type="text" name="name" :value="old('name', $company->name)" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="domain" value="Domaine" />
                <x-text-input id="domain" type="text" name="domain" :value="old('domain', $company->domain)" placeholder="exemple.com" />
                <x-input-error :messages="$errors->get('domain')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="phone" value="Téléphone" />
                <x-text-input id="phone" type="text" name="phone" :value="old('phone', $company->phone)" />
                <x-input-error :messages="$errors->get('phone')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="notes" value="Notes" />
                <x-textarea-input id="notes" name="notes" rows="3">{{ old('notes', $company->notes) }}</x-textarea-input>
                <x-input-error :messages="$errors->get('notes')" class="mt-1.5" />
            </div>
            <div class="flex items-center gap-2">
                <x-primary-button>Enregistrer</x-primary-button>
                <a href="{{ route('companies.index') }}"><x-secondary-button type="button">Annuler</x-secondary-button></a>
            </div>
        </form>

        <form method="POST" action="{{ route('companies.destroy', $company) }}" onsubmit="return confirm('Supprimer cette entreprise ?');" class="mt-6 border-t border-sand-200 pt-5 dark:border-ink-800">
            @csrf
            @method('DELETE')
            <x-danger-button>Supprimer l'entreprise</x-danger-button>
        </form>
    </x-card>
</x-app-layout>
