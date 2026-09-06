<x-app-layout title="Nouvelle équipe">
    <x-slot name="header">
        <x-page-header title="Nouvelle équipe" />
    </x-slot>

    <x-card class="max-w-xl">
        <form method="POST" action="{{ route('teams.store') }}" class="space-y-5">
            @csrf
            <div>
                <x-input-label for="name" value="Nom" />
                <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="description" value="Description" />
                <x-textarea-input id="description" name="description" rows="2">{{ old('description') }}</x-textarea-input>
                <x-input-error :messages="$errors->get('description')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label value="Membres" />
                <div class="mt-1 max-h-56 space-y-1 overflow-y-auto rounded-lg border border-sand-200 p-3 dark:border-ink-700">
                    @forelse ($members as $member)
                        <label class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm hover:bg-sand-50 dark:hover:bg-ink-800">
                            <input type="checkbox" name="members[]" value="{{ $member->id }}" class="rounded border-sand-300 text-ink-900 focus:ring-ink-500 dark:border-ink-600 dark:bg-ink-800">
                            <span class="text-ink-700 dark:text-sand-200">{{ $member->name }}</span>
                            <span class="text-xs capitalize text-ink-400 dark:text-sand-500">{{ $member->role }}</span>
                        </label>
                    @empty
                        <p class="px-2 py-1.5 text-sm text-ink-400 dark:text-sand-500">Aucun membre disponible.</p>
                    @endforelse
                </div>
            </div>
            <div class="flex items-center gap-2">
                <x-primary-button>Créer l'équipe</x-primary-button>
                <a href="{{ route('teams.index') }}"><x-secondary-button type="button">Annuler</x-secondary-button></a>
            </div>
        </form>
    </x-card>
</x-app-layout>
