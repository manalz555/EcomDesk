<section>
    <header>
        <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Informations du profil</h2>
        <p class="mt-1 text-sm text-ink-500 dark:text-sand-400">Votre prénom, nom et adresse email.</p>
    </header>

    <form method="POST" action="{{ route('profile.update') }}" class="mt-5 space-y-5">
        @csrf
        @method('PATCH')

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <x-input-label for="prenom" value="Prénom" />
                <x-text-input id="prenom" type="text" name="prenom" :value="old('prenom', $user->prenom)" autofocus />
                <x-input-error :messages="$errors->get('prenom')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="name" value="Nom complet" />
                <x-text-input id="name" type="text" name="name" :value="old('name', $user->name)" required />
                <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
            </div>
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" type="email" name="email" :value="old('email', $user->email)" required />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <p class="mt-2 text-sm text-ink-500 dark:text-sand-400">
                    Votre adresse email n'est pas vérifiée.
                    <button form="send-verification" class="font-medium text-ink-900 underline-offset-2 hover:underline dark:text-sand-100">
                        Renvoyer l'email de vérification
                    </button>
                </p>
            @endif
        </div>

        <x-primary-button>Enregistrer</x-primary-button>
    </form>

    <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
        @csrf
    </form>
</section>
