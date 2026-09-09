@php
    // Les six canaux d'entree, avec leur ordonnee dans le schema (viewBox 760x302).
    // Pastilles de 34 px de haut, 16 px d'ecart : 6*34 + 5*16 = 284, depart a y=9.
    $entrees = [
        ['label' => 'WhatsApp',  'y' => 9],
        ['label' => 'Instagram', 'y' => 59],
        ['label' => 'Messenger', 'y' => 109],
        ['label' => 'Telegram',  'y' => 159],
        ['label' => 'Email',     'y' => 209],
        ['label' => 'Chat du site', 'y' => 259],
    ];
@endphp

<section id="probleme" class="border-y border-sand-200 bg-sand-50 py-20 dark:border-ink-800 dark:bg-ink-900/30">
    <div class="mx-auto max-w-6xl px-6">

        <div class="max-w-2xl">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sand-700 dark:text-sand-500">
                Le problème
            </p>
            <h2 class="mt-4 font-display text-3xl font-normal leading-snug tracking-tight text-ink-900 sm:text-4xl dark:text-sand-50">
                Chaque canal devient un silo
            </h2>
            <p class="mt-4 text-lg leading-relaxed text-ink-500 dark:text-sand-400">
                Une même commande peut générer un message WhatsApp, un commentaire Instagram
                et un email. Sans point de convergence, les demandes se perdent, deux agents
                répondent à la même personne, et rien n'est mesurable.
            </p>
        </div>

        {{-- Schema : six entrees convergent vers une seule sortie. --}}
        <div class="mt-12 overflow-x-auto">
            <svg viewBox="0 0 760 302" role="img"
                 aria-label="Six canaux d'entrée — WhatsApp, Instagram, Messenger, Telegram, email et chat du site — convergent vers une boîte de réception partagée unique."
                 class="mx-auto h-auto w-full min-w-[620px] max-w-3xl text-ink-300 dark:text-ink-600">

                {{-- Traits de convergence, traces avant les pastilles pour passer dessous --}}
                @foreach ($entrees as $entree)
                    @php $cy = $entree['y'] + 17; @endphp
                    <path d="M 176 {{ $cy }} C 330 {{ $cy }}, 410 151, 562 151"
                          fill="none" stroke="currentColor" stroke-width="1.25" />
                @endforeach

                {{-- Pastilles des canaux --}}
                @foreach ($entrees as $entree)
                    <g>
                        <rect x="1" y="{{ $entree['y'] }}" width="174" height="34" rx="6"
                              class="fill-white stroke-sand-300 dark:fill-ink-900 dark:stroke-ink-700"
                              stroke-width="1" />
                        <text x="18" y="{{ $entree['y'] + 22 }}"
                              class="fill-ink-600 dark:fill-sand-300"
                              font-size="13.5" font-weight="500">{{ $entree['label'] }}</text>
                    </g>
                @endforeach

                {{-- Destination unique --}}
                <rect x="562" y="115" width="196" height="72" rx="8"
                      class="fill-ink-900 dark:fill-sand-100" />
                <text x="660" y="145" text-anchor="middle"
                      class="fill-sand-50 dark:fill-ink-900"
                      font-size="14" font-weight="600">Une seule</text>
                <text x="660" y="166" text-anchor="middle"
                      class="fill-sand-50 dark:fill-ink-900"
                      font-size="14" font-weight="600">boîte de réception</text>
            </svg>
        </div>

        <div class="mt-12 grid grid-cols-1 gap-px overflow-hidden rounded-lg border border-sand-200 bg-sand-200 sm:grid-cols-3 dark:border-ink-800 dark:bg-ink-800">
            @foreach ([
                ['t' => 'Rien ne se perd',        'd' => 'Un message arrivé sur un canal peu consulté remonte au même endroit que les autres.'],
                ['t' => 'Une seule personne répond', 'd' => 'L\'affectation à un agent est explicite : plus de réponses en double.'],
                ['t' => 'Tout devient mesurable', 'd' => 'Délai de première réponse, taux de résolution, satisfaction — par canal et par agent.'],
            ] as $bloc)
                <div class="bg-white px-6 py-6 dark:bg-ink-900">
                    <h3 class="text-sm font-semibold text-ink-900 dark:text-sand-50">{{ $bloc['t'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-ink-500 dark:text-sand-400">{{ $bloc['d'] }}</p>
                </div>
            @endforeach
        </div>

    </div>
</section>
