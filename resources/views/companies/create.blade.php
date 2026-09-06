<x-app-layout title="Nouvelle entreprise">
    <x-slot name="header">
        <x-page-header title="Nouvelle entreprise" />
    </x-slot>

    <x-card class="max-w-xl">
        <form method="POST" action="{{ route('companies.store') }}" class="space-y-5">
            @csrf
            <div>
                <x-input-label for="name" value="Nom" />
                <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="domain" value="Domaine" />
                <x-text-input id="domain" type="text" name="domain" :value="old('domain')" placeholder="exemple.com" />
                <x-input-error :messages="$errors->get('domain')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="phone" value="Téléphone" />
                <x-text-input id="phone" type="text" name="phone" :value="old('phone')" />
                <x-input-error :messages="$errors->get('phone')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="notes" value="Notes" />
                <x-textarea-input id="notes" name="notes" rows="3">{{ old('notes') }}</x-textarea-input>
                <x-input-error :messages="$errors->get('notes')" class="mt-1.5" />
            </div>
            <div class="flex items-center gap-2">
                <x-primary-button>Enregistrer</x-primary-button>
                <a href="{{ route('companies.index') }}"><x-secondary-button type="button">Annuler</x-secondary-button></a>
            </div>
        </form>
    </x-card>
</x-app-layout>
