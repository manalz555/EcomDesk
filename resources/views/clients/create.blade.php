<x-app-layout title="Nouveau client">
    <x-slot name="header">
        <x-page-header title="Nouveau client" />
    </x-slot>

    <x-card class="max-w-xl">
        <form method="POST" action="{{ route('clients.store') }}" class="space-y-5">
            @csrf
            <div>
                <x-input-label for="nom" value="Nom" />
                <x-text-input id="nom" type="text" name="nom" :value="old('nom')" required autofocus />
                <x-input-error :messages="$errors->get('nom')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="prenom" value="Prénom" />
                <x-text-input id="prenom" type="text" name="prenom" :value="old('prenom')" />
                <x-input-error :messages="$errors->get('prenom')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" type="email" name="email" :value="old('email')" />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="telephone" value="Téléphone" />
                <x-text-input id="telephone" type="text" name="telephone" :value="old('telephone')" />
                <x-input-error :messages="$errors->get('telephone')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="company_id" value="Entreprise" />
                <x-select-input id="company_id" name="company_id">
                    <option value="">Aucune</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" @selected(old('company_id') == $company->id)>{{ $company->name }}</option>
                    @endforeach
                </x-select-input>
                <x-input-error :messages="$errors->get('company_id')" class="mt-1.5" />
            </div>
            <div>
                <x-input-label for="notes" value="Notes" />
                <x-textarea-input id="notes" name="notes" rows="3">{{ old('notes') }}</x-textarea-input>
                <x-input-error :messages="$errors->get('notes')" class="mt-1.5" />
            </div>

            <details class="rounded-lg border border-sand-200 p-3 dark:border-ink-700">
                <summary class="cursor-pointer text-sm font-medium text-ink-700 dark:text-sand-200">Identifiants de canaux (avancé)</summary>
                <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <x-input-label for="whatsapp_id" value="WhatsApp ID" />
                        <x-text-input id="whatsapp_id" type="text" name="whatsapp_id" :value="old('whatsapp_id')" />
                    </div>
                    <div>
                        <x-input-label for="instagram_handle" value="Instagram" />
                        <x-text-input id="instagram_handle" type="text" name="instagram_handle" :value="old('instagram_handle')" />
                    </div>
                    <div>
                        <x-input-label for="messenger_psid" value="Messenger PSID" />
                        <x-text-input id="messenger_psid" type="text" name="messenger_psid" :value="old('messenger_psid')" />
                    </div>
                    <div>
                        <x-input-label for="telegram_chat_id" value="Telegram Chat ID" />
                        <x-text-input id="telegram_chat_id" type="text" name="telegram_chat_id" :value="old('telegram_chat_id')" />
                    </div>
                </div>
            </details>

            <div class="flex items-center gap-2">
                <x-primary-button>Enregistrer</x-primary-button>
                <a href="{{ route('clients.index') }}"><x-secondary-button type="button">Annuler</x-secondary-button></a>
            </div>
        </form>
    </x-card>
</x-app-layout>
