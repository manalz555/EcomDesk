<section class="mx-auto max-w-6xl px-6 py-20" data-reveal>
    <div class="rounded-2xl bg-ink-900 px-8 py-14 text-center shadow-card dark:bg-sand-100">
        <h2 class="text-3xl font-semibold tracking-tight text-sand-50 dark:text-ink-900">Prêt à centraliser vos conversations ?</h2>
        <p class="mx-auto mt-3 max-w-md text-sand-300 dark:text-ink-600">
            Créez votre espace EcomDesk et invitez votre équipe en quelques minutes.
        </p>
        <div class="mt-7 flex flex-col items-center justify-center gap-3 sm:flex-row">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn-shimmer btn-pulse w-full rounded-lg bg-sand-100 px-5 py-2.5 text-center text-sm font-semibold text-ink-900 shadow-soft transition-transform hover:scale-[1.04] hover:bg-sand-200 dark:bg-ink-900 dark:text-sand-50 dark:hover:bg-ink-800 sm:w-auto">
                    Commencer gratuitement
                </a>
            @endif
            <a href="{{ route('login') }}" class="w-full rounded-lg border border-sand-50/30 px-5 py-2.5 text-center text-sm font-semibold text-sand-50 hover:bg-white/5 dark:border-ink-900/20 dark:text-ink-900 dark:hover:bg-black/5 sm:w-auto">
                Se connecter
            </a>
        </div>
    </div>
</section>
