{{-- Remplace l'ancienne grille de six cartes decoratives : ici la numerotation
     porte une information reelle — c'est l'ordre dans lequel une conversation
     traverse la plateforme, et chaque etape nomme la fonctionnalite concrete. --}}

<section id="cycle" class="mx-auto max-w-6xl px-6 py-24">

    <div class="max-w-2xl">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sand-700 dark:text-sand-500">
            Le parcours d'une demande
        </p>
        <h2 class="mt-4 font-display text-3xl font-normal leading-snug tracking-tight text-ink-900 sm:text-4xl dark:text-sand-50">
            De la question du client<br class="hidden sm:block"> à la mesure du service
        </h2>
    </div>

    <ol class="mt-14 border-t border-sand-200 dark:border-ink-800">
        @foreach ([
            [
                'n' => '01',
                't' => 'La demande arrive',
                'd' => 'Quel que soit le canal, elle devient une conversation : sujet, contenu, catégorie et priorité. Une fiche client est créée à la volée si la personne est inconnue.',
                'tags' => ['7 canaux', 'Création à la volée', 'Catégorisation'],
            ],
            [
                'n' => '02',
                't' => 'Un agent la prend en charge',
                'd' => 'Une conversation sans agent est un état normal, visible dans un filtre dédié. N\'importe quel agent actif peut s\'en saisir — une seule fois, jamais deux.',
                'tags' => ['Auto-affectation', 'Filtre « non assignées »', 'Assignation par équipe'],
            ],
            [
                'n' => '03',
                't' => 'L\'assistant propose un brouillon',
                'd' => 'Si une règle d\'automatisation correspond au canal et à la catégorie, une réponse est rédigée automatiquement — et laissée en attente de validation.',
                'tags' => ['Règles par canal', 'Jamais envoyé seul', 'Notification à l\'équipe'],
            ],
            [
                'n' => '04',
                't' => 'L\'agent répond',
                'd' => 'Il valide le brouillon, le corrige, ou écrit lui-même. Il peut joindre des fichiers, poser une note interne invisible du client, et poser des étiquettes.',
                'tags' => ['Pièces jointes privées', 'Notes internes', 'Étiquettes'],
            ],
            [
                'n' => '05',
                't' => 'La conversation se résout',
                'd' => 'Le statut suit le travail réel — répondre fait passer « nouveau » à « en cours ». La satisfaction du client est notée de 1 à 5.',
                'tags' => ['4 statuts', '3 priorités', 'Satisfaction 1–5'],
            ],
            [
                'n' => '06',
                't' => 'L\'équipe se mesure',
                'd' => 'Délai moyen de première réponse, taux de résolution, volume par jour, répartition par canal et performance par agent. Chaque action sensible est tracée.',
                'tags' => ['Analytique', 'Performance par agent', 'Journal d\'audit'],
            ],
        ] as $etape)
            <li class="grid grid-cols-1 gap-x-10 gap-y-3 border-b border-sand-200 py-8 sm:grid-cols-[auto_minmax(0,20rem)_minmax(0,1fr)] dark:border-ink-800">

                <span class="font-display text-sm text-sand-600 dark:text-sand-500">{{ $etape['n'] }}</span>

                <h3 class="text-lg font-semibold leading-snug text-ink-900 dark:text-sand-50">
                    {{ $etape['t'] }}
                </h3>

                <div>
                    <p class="max-w-xl leading-relaxed text-ink-500 dark:text-sand-400">
                        {{ $etape['d'] }}
                    </p>
                    <ul class="mt-3 flex flex-wrap gap-x-5 gap-y-1">
                        @foreach ($etape['tags'] as $tag)
                            <li class="text-xs font-medium uppercase tracking-wider text-ink-400 dark:text-sand-500">
                                {{ $tag }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </li>
        @endforeach
    </ol>

</section>
