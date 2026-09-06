<section id="features" class="mx-auto max-w-6xl px-6 py-24">
    <div class="mx-auto max-w-2xl text-center" data-reveal>
        <h2 class="text-3xl font-semibold tracking-tight text-ink-900 dark:text-sand-50">Tout ce qu'il faut pour un service client organisé</h2>
        <p class="mt-3 text-ink-500 dark:text-sand-400">Des fondations solides pour une équipe qui grandit, sans complexité inutile.</p>
    </div>

    <div class="mt-14 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            ['title' => 'Boîte de réception unifiée', 'desc' => 'Toutes les conversations, tous canaux confondus, dans une seule liste triable et filtrable.', 'path' => 'M3.75 3h16.5M3.75 3v6.75L1.5 15v4.125c0 .621.504 1.125 1.125 1.125h18.75c.621 0 1.125-.504 1.125-1.125V15l-2.25-5.25V3M3.75 3l2.25 6.75h4.5A2.25 2.25 0 0 1 12.75 12v0a2.25 2.25 0 0 0 2.25 2.25h0A2.25 2.25 0 0 0 17.25 12v0a2.25 2.25 0 0 1 2.25-2.25h4.5', 'glow' => '#C0A36C'],
            ['title' => 'Fiches clients enrichies', 'desc' => 'Historique complet, tags, champs personnalisés et regroupement par entreprise.', 'path' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z', 'glow' => '#D9A855'],
            ['title' => 'Automatisation & IA', 'desc' => 'Un agent automatisé prend en charge et répond aux demandes selon vos règles.', 'path' => 'M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z', 'glow' => '#B8863D'],
            ['title' => 'Analytique & rapports', 'desc' => 'Temps de réponse, volume par canal, performance par agent, satisfaction client.', 'path' => 'M3 13.125c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z', 'glow' => '#A8A177'],
            ['title' => 'Équipes & permissions', 'desc' => 'Organisez vos agents en équipes, avec des rôles Administrateur, Manager et Agent.', 'path' => 'M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477M12 3.75a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm9 0a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm-15 0a3 3 0 1 1 0 6 3 3 0 0 1 0-6Z', 'glow' => '#A69A8A'],
            ['title' => 'Sécurité & audit', 'desc' => 'Rôles stricts, mots de passe chiffrés et journal d\'activité pour tracer chaque action.', 'path' => 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z', 'glow' => '#C9967A'],
        ] as $i => $feature)
            <div
                data-reveal
                style="transition-delay: {{ $i * 90 }}ms;"
                class="group rounded-xl transition-transform duration-300 hover:-translate-y-1.5 hover:scale-[1.02]"
            >
                <div class="glow-card rounded-xl" style="--glow-color: {{ $feature['glow'] }};">
                    <div class="glow-card__spin"></div>
                    <div class="glow-card__inner rounded-xl border border-sand-200 bg-white p-6 shadow-soft transition-shadow duration-300 group-hover:shadow-card dark:border-ink-800 dark:bg-ink-900">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-sand-100 text-ink-700 dark:bg-ink-800 dark:text-sand-200">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $feature['path'] }}" />
                            </svg>
                        </span>
                        <h3 class="mt-4 text-sm font-semibold text-ink-900 dark:text-sand-50">{{ $feature['title'] }}</h3>
                        <p class="mt-1.5 text-sm text-ink-500 dark:text-sand-400">{{ $feature['desc'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
