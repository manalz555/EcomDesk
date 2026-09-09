<section class="border-t border-sand-200 py-24 dark:border-ink-800">
    <div class="mx-auto max-w-2xl px-6 text-center">
        <h2 class="font-display text-3xl font-normal leading-snug tracking-tight text-ink-900 sm:text-4xl dark:text-sand-50">
            Essayez la boîte de réception
        </h2>
        <p class="mx-auto mt-4 max-w-md leading-relaxed text-ink-500 dark:text-sand-400">
            Créez un espace, invitez votre équipe, collez la ligne du chat sur votre boutique.
            Vos premières conversations arrivent dans la minute.
        </p>

        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
            @if (Route::has('register'))
                <a href="{{ route('register') }}"
                   class="rounded-lg bg-ink-900 px-6 py-3 text-sm font-semibold text-sand-50 transition hover:bg-ink-800 dark:bg-sand-100 dark:text-ink-900 dark:hover:bg-white">
                    Créer un espace
                </a>
            @endif
            <a href="{{ route('login') }}"
               class="rounded-lg border border-sand-300 px-6 py-3 text-sm font-semibold text-ink-700 transition hover:border-ink-900 hover:text-ink-900 dark:border-ink-700 dark:text-sand-200 dark:hover:border-sand-400 dark:hover:text-sand-50">
                Se connecter
            </a>
        </div>
    </div>
</section>
