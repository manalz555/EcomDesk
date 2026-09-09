{{-- La contribution distinctive du projet. On montre le mecanisme lui-meme —
     un brouillon avec ses deux issues possibles — plutot que de le decrire. --}}

<section id="automation" class="border-y border-sand-200 bg-sand-50 py-24 dark:border-ink-800 dark:bg-ink-900/30">
    <div class="mx-auto grid max-w-6xl grid-cols-1 items-start gap-14 px-6 lg:grid-cols-2 lg:gap-20">

        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sand-700 dark:text-sand-500">
                Automatisation
            </p>

            <h2 class="mt-4 font-display text-3xl font-normal leading-snug tracking-tight text-ink-900 sm:text-4xl dark:text-sand-50">
                L'assistant propose.<br>Vous disposez.
            </h2>

            <p class="mt-6 text-lg leading-relaxed text-ink-500 dark:text-sand-400">
                Quand une demande correspond à l'une de vos règles, une réponse est rédigée
                automatiquement. Elle n'est pas envoyée : elle attend qu'un agent la lise,
                la corrige si besoin, et la valide.
            </p>

            <p class="mt-5 leading-relaxed text-ink-500 dark:text-sand-400">
                Dans un service client, une réponse fausse envoyée sans contrôle peut coûter
                un client. Automatiser l'envoi aurait été plus simple à construire — nous ne
                l'avons pas fait. L'agent ne part plus d'une page blanche, mais la décision
                reste la sienne.
            </p>

            <dl class="mt-10 space-y-5 border-t border-sand-200 pt-8 dark:border-ink-800">
                @foreach ([
                    ['t' => 'Des règles que vous écrivez',  'd' => 'Par canal, par catégorie, ou les deux. Activables et désactivables une par une.'],
                    ['t' => 'Rien n\'échappe à l\'historique', 'd' => 'Un brouillon validé porte le nom de l\'agent qui l\'a validé, pas celui de l\'assistant.'],
                    ['t' => 'Le service ne tombe jamais',    'd' => 'Si le fournisseur d\'IA est indisponible, une réponse de repli est produite et l\'agent garde la main.'],
                ] as $point)
                    <div class="grid grid-cols-1 gap-1 sm:grid-cols-[minmax(0,13rem)_minmax(0,1fr)] sm:gap-6">
                        <dt class="text-sm font-semibold text-ink-900 dark:text-sand-50">{{ $point['t'] }}</dt>
                        <dd class="text-sm leading-relaxed text-ink-500 dark:text-sand-400">{{ $point['d'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        {{-- Reproduction fidele de l'ecran reel de validation d'un brouillon. --}}
        <div>
            <div class="overflow-hidden rounded-xl border border-sand-200 bg-white shadow-card dark:border-ink-800 dark:bg-ink-900">

                <div class="border-b border-sand-200 px-5 py-4 dark:border-ink-800">
                    <p class="text-sm font-semibold text-ink-900 dark:text-sand-50">Colis pas encore reçu</p>
                    <p class="mt-0.5 text-xs text-ink-400 dark:text-sand-500">Kenza L. &middot; Telegram &middot; Livraison</p>
                </div>

                <div class="space-y-4 px-5 py-5">

                    {{-- Message du client --}}
                    <div class="max-w-[85%] rounded-lg rounded-tl-sm bg-sand-100 px-4 py-3 dark:bg-ink-800">
                        <p class="text-sm leading-relaxed text-ink-700 dark:text-sand-200">
                            Bonjour, ma commande devait arriver hier et je n'ai reçu aucune nouvelle.
                        </p>
                    </div>

                    {{-- Le brouillon en attente --}}
                    <div class="rounded-lg border border-dashed border-sand-400 bg-sand-50 p-4 dark:border-ink-600 dark:bg-ink-950/40">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded bg-sand-200 px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wider text-sand-900 dark:bg-ink-700 dark:text-sand-200">
                                Brouillon
                            </span>
                            <span class="text-xs text-ink-400 dark:text-sand-500">
                                Assistant EcomDesk &middot; non envoyé
                            </span>
                        </div>

                        <p class="mt-3 text-sm leading-relaxed text-ink-700 dark:text-sand-200">
                            Bonjour Kenza, merci de nous avoir signalé ce retard. Je vérifie
                            immédiatement où en est votre colis et je reviens vers vous dans la journée.
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="rounded-md bg-ink-900 px-3.5 py-2 text-xs font-semibold text-sand-50 dark:bg-sand-100 dark:text-ink-900">
                                Valider et envoyer
                            </span>
                            <span class="rounded-md border border-sand-300 px-3.5 py-2 text-xs font-semibold text-ink-600 dark:border-ink-700 dark:text-sand-300">
                                Modifier
                            </span>
                            <span class="rounded-md border border-sand-300 px-3.5 py-2 text-xs font-semibold text-ink-600 dark:border-ink-700 dark:text-sand-300">
                                Rejeter
                            </span>
                        </div>
                    </div>
                </div>

                <p class="border-t border-sand-200 bg-sand-50 px-5 py-3 text-xs leading-relaxed text-ink-400 dark:border-ink-800 dark:bg-ink-950/50 dark:text-sand-500">
                    Tant que personne n'a cliqué sur « Valider et envoyer », le client ne voit rien.
                </p>
            </div>
        </div>

    </div>
</section>
