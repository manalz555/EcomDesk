<x-app-layout title="Nouveau compte">
    <x-slot name="header">
        <x-page-header title="Nouveau compte" />
    </x-slot>

    <x-card class="max-w-lg">
        <form method="POST" action="{{ route('agents.store') }}" class="space-y-5">
            @csrf
            <div>
                <x-input-label for="name" value="Nom complet" />
                <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" type="email" name="email" :value="old('email')" required />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="password" value="Mot de passe initial" />
                <x-text-input id="password" type="password" name="password" required minlength="8" />
                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="role" value="Rôle" />
                <x-select-input id="role" name="role">
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" @selected(old('role') == $role)>{{ ucfirst($role) }}</option>
                    @endforeach
                </x-select-input>
                <x-input-error :messages="$errors->get('role')" class="mt-1.5" />
            </div>
            <div class="flex items-center gap-2">
                <x-primary-button>Créer</x-primary-button>
                <a href="{{ route('agents.index') }}"><x-secondary-button type="button">Annuler</x-secondary-button></a>
            </div>
        </form>
    </x-card>
</x-app-layout>
