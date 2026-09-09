@php
    // Jeu de secours : la page reste presentable sur une installation vierge,
    // avant tout seed. Meme forme que les vraies conversations.
    $exemples = collect([
        (object) ['sujet' => 'Ma commande n\'est pas encore arrivée', 'canal' => 'whatsapp', 'statut' => 'nouveau', 'client_nom' => 'Sara A.'],
        (object) ['sujet' => 'Question sur le remboursement',        'canal' => 'instagram','statut' => 'en_cours','client_nom' => 'Youssef B.'],
        (object) ['sujet' => 'Le produit reçu ne correspond pas',    'canal' => 'email',    'statut' => 'en_cours','client_nom' => 'Imane C.'],
        (object) ['sujet' => 'Disponibilité en taille M ?',          'canal' => 'live_chat','statut' => 'nouveau', 'client_nom' => 'Visiteur'],
        (object) ['sujet' => 'Merci pour votre réactivité',          'canal' => 'telegram', 'statut' => 'resolu',  'client_nom' => 'Hamza S.'],
    ]);

    $lignes = ($apercu ?? collect())->isNotEmpty()
        ? $apercu->map(fn ($c) => (object) [
            'sujet' => $c->sujet,
            'canal' => $c->canal,
            'statut' => $c->statut,
            // Nom de famille reduit a son initiale : la page est publique.
            'client_nom' => trim(($c->client?->prenom ?: $c->client?->nom ?: 'Client').' '.mb_substr($c->client?->prenom ? ($c->client?->nom ?? '') : '', 0, 1).($c->client?->prenom && $c->client?->nom ? '.' : '')),
        ])
        : $exemples;

    $canaux = [
        'email' => 'Email', 'whatsapp' => 'WhatsApp', 'instagram' => 'Instagram',
        'messenger' => 'Messenger', 'telegram' => 'Telegram',
        'live_chat' => 'Chat en direct', 'formulaire' => 'Formulaire',
    ];
@endphp

<section class="mx-auto max-w-6xl px-6 pt-20 pb-16 sm:pt-28 sm:pb-24">
    <div class="grid grid-cols-1 items-center gap-14 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.05fr)] lg:gap-20">

        {{-- ---------------------------------------------------- la these --}}
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sand-700 dark:text-sand-500">
                Support client multicanal
            </p>

            <h1 class="mt-5 text-balance font-display text-[2.15rem] font-normal leading-[1.12] tracking-tight text-ink-900 sm:text-5xl lg:text-[3.4rem] dark:text-sand-50">
                Vos clients écrivent<br class="hidden sm:block"> partout. Vous répondez<br class="hidden sm:block"> à un seul endroit.
            </h1>

            <p class="mt-6 max-w-md text-lg leading-relaxed text-ink-500 dark:text-sand-400">
                EcomDesk réunit WhatsApp, Instagram, Messenger, Telegram, l'email et le chat
                de votre site dans une boîte de réception partagée — avec des rôles, un suivi
                et des mesures.
            </p>

            <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                       class="rounded-lg bg-ink-900 px-6 py-3 text-center text-sm font-semibold text-sand-50 transition hover:bg-ink-800 dark:bg-sand-100 dark:text-ink-900 dark:hover:bg-white">
                        Créer un espace
                    </a>
                @endif
                <a href="{{ route('login') }}"
                   class="rounded-lg border border-sand-300 px-6 py-3 text-center text-sm font-semibold text-ink-700 transition hover:border-ink-900 hover:text-ink-900 dark:border-ink-700 dark:text-sand-200 dark:hover:border-sand-400 dark:hover:text-sand-50">
                    Se connecter
                </a>
            </div>

            {{-- L'argument central de la page : le chat d'en bas a droite est reel. --}}
            <p class="mt-10 flex max-w-sm items-start gap-3 border-l-2 border-sand-500 pl-4 text-sm leading-relaxed text-ink-500 dark:text-sand-400">
                <span>
                    Le chat en bas à droite de cette page n'est pas une image.
                    Écrivez-y : votre message arrive dans la boîte de réception ci-contre,
                    et un agent peut vous répondre.
                </span>
            </p>
        </div>

        {{-- ------------------------------------------- la vraie boite --}}
        <div>
            <div class="overflow-hidden rounded-xl border border-sand-200 bg-white shadow-card dark:border-ink-800 dark:bg-ink-900">

                <div class="flex items-baseline justify-between border-b border-sand-200 px-5 py-4 dark:border-ink-800">
                    <p class="text-sm font-semibold text-ink-900 dark:text-sand-50">Boîte de réception</p>
                    <p class="text-xs text-ink-400 dark:text-sand-500">
                        {{ $totalConversations ?? 5 }} conversation{{ ($totalConversations ?? 5) > 1 ? 's' : '' }}
                    </p>
                </div>

                <ul class="divide-y divide-sand-100 dark:divide-ink-800">
                    @foreach ($lignes as $ligne)
                        <li class="flex items-start gap-4 px-5 py-4">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-ink-900 dark:text-sand-50">
                                    {{ $ligne->client_nom }}
                                </p>
                                <p class="mt-0.5 truncate text-sm text-ink-500 dark:text-sand-400">
                                    {{ $ligne->sujet }}
                                </p>
                            </div>
                            <div class="flex shrink-0 flex-col items-end gap-1.5">
                                <x-status-badge :status="$ligne->statut" />
                                <span class="text-[11px] text-ink-400 dark:text-sand-500">
                                    {{ $canaux[$ligne->canal] ?? $ligne->canal }}
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <p class="border-t border-sand-200 bg-sand-50 px-5 py-3 text-xs text-ink-400 dark:border-ink-800 dark:bg-ink-950/50 dark:text-sand-500">
                    Données de démonstration &middot; noms de famille réduits à leur initiale
                </p>
            </div>
        </div>

    </div>
</section>
