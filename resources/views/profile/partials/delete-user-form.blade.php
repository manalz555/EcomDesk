<section x-data="{ confirmingDeletion: false }">
    <header>
        <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Supprimer le compte</h2>
        <p class="mt-1 text-sm text-ink-500 dark:text-sand-400">
            Cette action est définitive. Toutes les données associées à votre compte seront supprimées.
        </p>
    </header>

    <x-danger-button type="button" class="mt-5" @click="confirmingDeletion = true">
        Supprimer mon compte
    </x-danger-button>

    <div
        x-show="confirmingDeletion"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-ink-900/50 p-4"
    >
        <div @click.outside="confirmingDeletion = false" class="w-full max-w-md rounded-xl bg-white p-6 shadow-card dark:bg-ink-900">
            <h3 class="text-base font-semibold text-ink-900 dark:text-sand-50">Confirmer la suppression</h3>
            <p class="mt-2 text-sm text-ink-500 dark:text-sand-400">
                Saisissez votre mot de passe pour confirmer la suppression définitive de votre compte.
            </p>

            <form method="POST" action="{{ route('profile.destroy') }}" class="mt-4 space-y-4">
                @csrf
                @method('DELETE')

                <div>
                    <x-input-label for="password_delete" value="Mot de passe" class="sr-only" />
                    <x-text-input id="password_delete" type="password" name="password" placeholder="Mot de passe" class="w-full" />
                    <x-input-error :messages="$errors->userDeletion->get('password') ?? []" class="mt-1.5" />
                </div>

                <div class="flex justify-end gap-2">
                    <x-secondary-button type="button" @click="confirmingDeletion = false">Annuler</x-secondary-button>
                    <x-danger-button>Supprimer définitivement</x-danger-button>
                </div>
            </form>
        </div>
    </div>
</section>
