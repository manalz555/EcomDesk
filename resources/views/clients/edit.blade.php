<x-app-layout title="Modifier client">
    <x-slot name="header">
        <x-page-header :title="'Modifier ' . $client->nom_complet" />
    </x-slot>

    <x-card class="max-w-xl">
        <form method="POST" action="{{ route('clients.update', $client) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <div>
                <x-input-label for="nom" value="Nom" />
                <x-text-input id="nom" type="text" name="nom" :value="old('nom', $client->nom)" required autofocus />
                <x-input-error :messages="$errors->get('nom')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="prenom" value="Prénom" />
                <x-text-input id="prenom" type="text" name="prenom" :value="old('prenom', $client->prenom)" />
                <x-input-error :messages="$errors->get('prenom')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" type="email" name="email" :value="old('email', $client->email)" />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="telephone" value="Téléphone" />
                <x-text-input id="telephone" type="text" name="telephone" :value="old('telephone', $client->telephone)" />
                <x-input-error :messages="$errors->get('telephone')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="company_id" value="Entreprise" />
                <x-select-input id="company_id" name="company_id">
                    <option value="">Aucune</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" @selected(old('company_id', $client->company_id) == $company->id)>{{ $company->name }}</option>
                    @endforeach
                </x-select-input>
                <x-input-error :messages="$errors->get('company_id')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="notes" value="Notes" />
                <x-textarea-input id="notes" name="notes" rows="3">{{ old('notes', $client->notes) }}</x-textarea-input>
                <x-input-error :messages="$errors->get('notes')" class="mt-1.5" />
            </div>

            <details class="rounded-lg border border-sand-200 p-3 dark:border-ink-700">
                <summary class="cursor-pointer text-sm font-medium text-ink-700 dark:text-sand-200">Identifiants de canaux (avancé)</summary>
                <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <x-input-label for="whatsapp_id" value="WhatsApp ID" />
                        <x-text-input id="whatsapp_id" type="text" name="whatsapp_id" :value="old('whatsapp_id', $client->whatsapp_id)" />
                    </div>
                    <div>
                        <x-input-label for="instagram_handle" value="Instagram" />
                        <x-text-input id="instagram_handle" type="text" name="instagram_handle" :value="old('instagram_handle', $client->instagram_handle)" />
                    </div>
                    <div>
                        <x-input-label for="messenger_psid" value="Messenger PSID" />
                        <x-text-input id="messenger_psid" type="text" name="messenger_psid" :value="old('messenger_psid', $client->messenger_psid)" />
                    </div>
                    <div>
                        <x-input-label for="telegram_chat_id" value="Telegram Chat ID" />
                        <x-text-input id="telegram_chat_id" type="text" name="telegram_chat_id" :value="old('telegram_chat_id', $client->telegram_chat_id)" />
                    </div>
                </div>
            </details>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <x-primary-button>Enregistrer</x-primary-button>
                    <a href="{{ route('clients.show', $client) }}"><x-secondary-button type="button">Annuler</x-secondary-button></a>
                </div>
            </div>
        </form>

        <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('Supprimer ce client ? Cette action est irréversible.');" class="mt-6 border-t border-sand-200 pt-5 dark:border-ink-800">
            @csrf
            @method('DELETE')
            <x-danger-button>
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                Supprimer le client
            </x-danger-button>
        </form>
    </x-card>
</x-app-layout>
