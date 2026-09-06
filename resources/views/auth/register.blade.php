<x-guest-layout>
    <h1 class="text-2xl font-semibold tracking-tight text-ink-900 dark:text-sand-50">Créer un compte</h1>
    <p class="mt-1 text-sm text-ink-500 dark:text-sand-400">Rejoignez votre espace EcomDesk.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <x-input-label for="name" value="Nom complet" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="password" value="Mot de passe" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <x-primary-button class="w-full">Créer mon compte</x-primary-button>

        <p class="text-center text-sm text-ink-500 dark:text-sand-400">
            Déjà un compte ?
            <a href="{{ route('login') }}" class="font-medium text-ink-900 underline-offset-2 hover:underline dark:text-sand-100">Se connecter</a>
        </p>
    </form>
</x-guest-layout>
