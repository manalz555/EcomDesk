<x-app-layout title="Modifier le compte">
    <x-slot name="header">
        <x-page-header :title="'Modifier ' . $agent->name" />
    </x-slot>

    <x-card class="max-w-lg">
        <form method="POST" action="{{ route('agents.update', $agent) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <div>
                <x-input-label for="name" value="Nom complet" />
                <x-text-input id="name" type="text" name="name" :value="old('name', $agent->name)" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" type="email" name="email" :value="old('email', $agent->email)" required />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="role" value="Rôle" />
                <x-select-input id="role" name="role" :disabled="$agent->id === auth()->id()">
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" @selected(old('role', $agent->role) == $role)>{{ ucfirst($role) }}</option>
                    @endforeach
                </x-select-input>
                @if ($agent->id === auth()->id())
                    <input type="hidden" name="role" value="{{ $agent->role }}">
                    <p class="mt-1.5 text-xs text-ink-400 dark:text-sand-500">Vous ne pouvez pas modifier votre propre rôle.</p>
                @endif
                <x-input-error :messages="$errors->get('role')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="actif" value="Statut du compte" />
                <x-select-input id="actif" name="actif" :disabled="$agent->id === auth()->id()">
                    <option value="1" @selected($agent->actif)>Actif</option>
                    <option value="0" @selected(!$agent->actif)>Désactivé</option>
                </x-select-input>
                @if ($agent->id === auth()->id())
                    <input type="hidden" name="actif" value="1">
                    <p class="mt-1.5 text-xs text-ink-400 dark:text-sand-500">Vous ne pouvez pas désactiver votre propre compte.</p>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <x-primary-button>Enregistrer</x-primary-button>
                <a href="{{ route('agents.index') }}"><x-secondary-button type="button">Annuler</x-secondary-button></a>
            </div>
        </form>
    </x-card>
</x-app-layout>
