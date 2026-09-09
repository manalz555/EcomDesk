{{-- Remplace la rangee de logos de marques flottants : ces logos etaient
     redessines a la main (approximatifs, et marques deposees de tiers), et
     n'apportaient aucune information. Un etat de raccordement honnete est
     plus utile a un prospect — et defendable devant un jury. --}}

<section id="canaux" class="mx-auto max-w-6xl px-6 py-24">

    <div class="max-w-2xl">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sand-700 dark:text-sand-500">
            Canaux
        </p>
        <h2 class="mt-4 font-display text-3xl font-normal leading-snug tracking-tight text-ink-900 sm:text-4xl dark:text-sand-50">
            Un canal branché pour de vrai,<br class="hidden sm:block"> six prêts à l'être
        </h2>
        <p class="mt-4 text-lg leading-relaxed text-ink-500 dark:text-sand-400">
            Le chat du site fonctionne de bout en bout aujourd'hui : réception, affectation,
            réponse, affichage chez le visiteur. Les autres canaux réutilisent exactement le
            même circuit — seul le point d'entrée change.
        </p>
    </div>

    <ul class="mt-12 grid grid-cols-1 gap-px overflow-hidden rounded-lg border border-sand-200 bg-sand-200 sm:grid-cols-2 lg:grid-cols-3 dark:border-ink-800 dark:bg-ink-800">
        @foreach ([
            ['nom' => 'Chat du site',  'etat' => 'operationnel', 'note' => 'Une ligne de code à coller. Fonctionne sur cette page même.'],
            ['nom' => 'Telegram',      'etat' => 'prochain',     'note' => 'Inscription la plus simple : prochain canal à brancher.'],
            ['nom' => 'WhatsApp',      'etat' => 'prevu',        'note' => 'Nécessite une validation WhatsApp Business.'],
            ['nom' => 'Instagram',     'etat' => 'prevu',        'note' => 'Messages directs, via validation Meta.'],
            ['nom' => 'Messenger',     'etat' => 'prevu',        'note' => 'Même procédure de validation que Instagram.'],
            ['nom' => 'Email',         'etat' => 'prevu',        'note' => 'Réception par boîte dédiée, à raccorder.'],
        ] as $canal)
            <li class="bg-white px-6 py-6 dark:bg-ink-900">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-sm font-semibold text-ink-900 dark:text-sand-50">{{ $canal['nom'] }}</h3>

                    @if ($canal['etat'] === 'operationnel')
                        <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600 dark:bg-emerald-400"></span>
                            Opérationnel
                        </span>
                    @elseif ($canal['etat'] === 'prochain')
                        <span class="inline-flex shrink-0 items-center rounded-full bg-sand-200 px-2.5 py-0.5 text-[11px] font-semibold text-sand-900 dark:bg-ink-700 dark:text-sand-200">
                            En cours
                        </span>
                    @else
                        <span class="inline-flex shrink-0 items-center rounded-full border border-sand-300 px-2.5 py-0.5 text-[11px] font-medium text-ink-400 dark:border-ink-700 dark:text-sand-500">
                            Prévu
                        </span>
                    @endif
                </div>

                <p class="mt-2 text-sm leading-relaxed text-ink-500 dark:text-sand-400">{{ $canal['note'] }}</p>
            </li>
        @endforeach
    </ul>

</section>
