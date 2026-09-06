<section>
    <header>
        <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Mot de passe</h2>
        <p class="mt-1 text-sm text-ink-500 dark:text-sand-400">Utilisez un mot de passe long et unique pour rester en sécurité.</p>
    </header>

    <form method="POST" action="{{ route('password.update') }}" class="mt-5 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <x-input-label for="current_password" value="Mot de passe actuel" />
            <x-text-input id="current_password" type="password" name="current_password" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="password" value="Nouveau mot de passe" />
            <x-text-input id="password" type="password" name="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1.5" />
        </div>

        <x-primary-button>Enregistrer</x-primary-button>
    </form>
</section>
