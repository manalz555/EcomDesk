<header class="sticky top-0 z-40 border-b border-sand-200 bg-white/80 backdrop-blur dark:border-ink-800 dark:bg-ink-950/80">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
        <x-brand-mark />

        <nav class="hidden items-center gap-8 md:flex">
            <a href="#probleme" class="text-sm font-medium text-ink-600 transition hover:text-ink-900 dark:text-sand-300 dark:hover:text-sand-50">Le problème</a>
            <a href="#cycle" class="text-sm font-medium text-ink-600 transition hover:text-ink-900 dark:text-sand-300 dark:hover:text-sand-50">Le parcours</a>
            <a href="#automation" class="text-sm font-medium text-ink-600 transition hover:text-ink-900 dark:text-sand-300 dark:hover:text-sand-50">Automatisation</a>
            <a href="#canaux" class="text-sm font-medium text-ink-600 transition hover:text-ink-900 dark:text-sand-300 dark:hover:text-sand-50">Canaux</a>
        </nav>

        <div class="flex items-center gap-2">
            <x-dark-mode-toggle />
            <a href="{{ route('login') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-ink-700 hover:bg-sand-100 dark:text-sand-200 dark:hover:bg-ink-800">
                Connexion
            </a>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="rounded-lg bg-ink-900 px-4 py-2 text-sm font-semibold text-sand-50 shadow-soft hover:bg-ink-800 dark:bg-sand-100 dark:text-ink-900 dark:hover:bg-sand-200">
                    Créer un compte
                </a>
            @endif
        </div>
    </div>
</header>
