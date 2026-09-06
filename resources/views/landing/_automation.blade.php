@php
    // Depose une photo dans public/images/automation-1.jpg pour remplacer le degrade de secours.
    $automationImage = 'automation-1.jpg';
    $automationPath = public_path('images/'.$automationImage);
    $automationUrl = file_exists($automationPath) ? asset('images/'.$automationImage) : null;
    $automationGradient = 'linear-gradient(135deg, #1C1B18 0%, #57432C 55%, #A6854F 100%)';
@endphp

<section
    id="automation"
    class="relative overflow-hidden border-y border-sand-200 py-24 dark:border-ink-800"
    data-reveal
>
    <div
        class="absolute inset-0 -z-20 bg-cover bg-center"
        style="{{ $automationUrl ? "background-image: url('{$automationUrl}');" : "background-image: {$automationGradient};" }}"
    ></div>
    <div class="absolute inset-0 -z-10 bg-ink-950/70"></div>

    <div class="mx-auto grid max-w-6xl grid-cols-1 items-center gap-12 px-6 lg:grid-cols-2">
        <div>
            <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-medium text-sand-50 backdrop-blur">
                Assistant automatisé
            </span>
            <h2 class="mt-4 text-3xl font-semibold tracking-tight text-white">
                Laissez l'automatisation répondre à votre place
            </h2>
            <p class="mt-4 text-sand-100">
                Un agent automatisé rejoint votre équipe comme n'importe quel coéquipier : il peut être assigné à une conversation
                et y répondre selon des règles que vous définissez, par canal ou par catégorie — livraison, remboursement,
                questions produit… Chaque réponse automatisée reste visible et attribuée dans l'historique, comme toute autre
                réponse d'agent.
            </p>
            <ul class="mt-6 space-y-3 text-sm text-sand-100">
                @foreach ([
                    'Règles d\'activation par canal ou par catégorie',
                    'Historique entièrement traçable, rien n\'est envoyé sans laisser de trace',
                    'Reprise en main manuelle possible à tout moment par un agent',
                ] as $point)
                    <li class="flex items-start gap-2.5">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        {{ $point }}
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="rounded-2xl border border-sand-200 bg-white p-5 shadow-card dark:border-ink-800 dark:bg-ink-900">
            <div class="flex items-center gap-3 border-b border-sand-200 pb-4 dark:border-ink-800">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-ink-900 text-sm font-semibold text-sand-50 dark:bg-sand-100 dark:text-ink-900">EA</span>
                <div>
                    <p class="text-sm font-semibold text-ink-900 dark:text-sand-50">EcomDesk Assistant</p>
                    <p class="text-xs text-ink-400 dark:text-sand-500">Agent automatisé &middot; Livraison</p>
                </div>
                <span class="ml-auto inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-950 dark:text-emerald-300 dark:ring-emerald-400/20">Actif</span>
            </div>
            <div class="mt-4 space-y-3">
                <div class="rounded-lg bg-sand-100 p-3 text-sm text-ink-700 dark:bg-ink-800 dark:text-sand-200">
                    Bonjour, je n'ai pas encore reçu de nouvelles sur ma commande, pouvez-vous m'aider ?
                </div>
                <div class="rounded-lg bg-ink-900 p-3 text-sm text-sand-50 dark:bg-sand-100 dark:text-ink-900">
                    Bonjour ! Je vérifie le statut de votre commande tout de suite et reviens vers vous dans un instant.
                </div>
            </div>
        </div>
    </div>
</section>
