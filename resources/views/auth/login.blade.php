<x-guest-layout>
    <h1 class="text-2xl font-semibold tracking-tight text-ink-900 dark:text-sand-50">Connexion</h1>
    <p class="mt-1 text-sm text-ink-500 dark:text-sand-400">Accédez à votre espace EcomDesk.</p>

    <x-auth-session-status class="mt-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="password" value="Mot de passe" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="flex items-center gap-2 text-sm text-ink-600 dark:text-sand-300">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-sand-300 text-ink-900 focus:ring-ink-500 dark:border-ink-600 dark:bg-ink-800">
                Se souvenir de moi
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-ink-600 underline-offset-2 hover:underline dark:text-sand-300">
                    Mot de passe oublié ?
                </a>
            @endif
        </div>

        <x-primary-button class="w-full">Se connecter</x-primary-button>

        @if (Route::has('register'))
            <p class="text-center text-sm text-ink-500 dark:text-sand-400">
                Pas encore de compte ?
                <a href="{{ route('register') }}" class="font-medium text-ink-900 underline-offset-2 hover:underline dark:text-sand-100">Créer un compte</a>
            </p>
        @endif
    </form>
</x-guest-layout>
