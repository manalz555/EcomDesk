<x-guest-layout>
    <h1 class="text-2xl font-semibold tracking-tight text-ink-900 dark:text-sand-50">Mot de passe oublié</h1>
    <p class="mt-1 text-sm text-ink-500 dark:text-sand-400">
        Indiquez votre email, nous vous enverrons un lien de réinitialisation.
    </p>

    <x-auth-session-status class="mt-6" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <x-primary-button class="w-full">Envoyer le lien de réinitialisation</x-primary-button>

        <p class="text-center text-sm text-ink-500 dark:text-sand-400">
            <a href="{{ route('login') }}" class="font-medium text-ink-900 underline-offset-2 hover:underline dark:text-sand-100">Retour à la connexion</a>
        </p>
    </form>
</x-guest-layout>
