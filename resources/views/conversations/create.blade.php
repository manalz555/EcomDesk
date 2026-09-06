<x-app-layout title="Nouvelle conversation">
    <x-slot name="header">
        <x-page-header title="Nouvelle conversation" />
    </x-slot>

    <x-card class="max-w-2xl" x-data="{ clientId: '{{ $clientSelectionne }}' }">
        <form method="POST" action="{{ route('conversations.store') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="client_id" value="Client existant" />
                <x-select-input id="client_id" name="client_id" x-model="clientId">
                    <option value="">— Créer un nouveau client —</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" @selected($clientSelectionne == $client->id)>{{ $client->nom_complet }}</option>
                    @endforeach
                </x-select-input>
            </div>

            <div x-show="!clientId" x-cloak class="rounded-lg border border-sand-200 bg-sand-50 p-4 dark:border-ink-700 dark:bg-ink-800">
                <p class="mb-3 text-sm font-medium text-ink-700 dark:text-sand-200">Nouveau client</p>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <x-text-input type="text" name="nouveau_client_nom" placeholder="Nom" />
                    <x-text-input type="text" name="nouveau_client_prenom" placeholder="Prénom" />
                    <x-text-input type="email" name="nouveau_client_email" placeholder="Email" />
                    <x-text-input type="text" name="nouveau_client_telephone" placeholder="Téléphone" />
                </div>
            </div>

            <div>
                <x-input-label for="sujet" value="Sujet" />
                <x-text-input id="sujet" type="text" name="sujet" :value="old('sujet')" required />
                <x-input-error :messages="$errors->get('sujet')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="contenu" value="Message" />
                <x-textarea-input id="contenu" name="contenu" rows="4" required>{{ old('contenu') }}</x-textarea-input>
                <x-input-error :messages="$errors->get('contenu')" class="mt-1.5" />
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <x-input-label for="canal" value="Canal" />
                    <x-select-input id="canal" name="canal" required>
                        @foreach ($canaux as $c)<option value="{{ $c }}">{{ ucfirst($c) }}</option>@endforeach
                    </x-select-input>
                </div>
                <div>
                    <x-input-label for="categorie" value="Catégorie" />
                    <x-select-input id="categorie" name="categorie" required>
                        @foreach ($categories as $c)<option value="{{ $c }}">{{ ucfirst($c) }}</option>@endforeach
                    </x-select-input>
                </div>
                <div>
                    <x-input-label for="priorite" value="Priorité" />
                    <x-select-input id="priorite" name="priorite" required>
                        @foreach ($priorites as $p)<option value="{{ $p }}" @selected($p == 'moyenne')>{{ ucfirst($p) }}</option>@endforeach
                    </x-select-input>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <x-primary-button>Créer la conversation</x-primary-button>
                <a href="{{ route('conversations.index') }}"><x-secondary-button type="button">Annuler</x-secondary-button></a>
            </div>
        </form>
    </x-card>
</x-app-layout>
