<x-app-layout title="Analytique">
    <x-slot name="header">
        <x-page-header title="Analytique" subtitle="Temps de réponse, volume, satisfaction et performance de l'équipe." />
    </x-slot>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-card>
            <p class="text-sm text-ink-500 dark:text-sand-400">Temps de réponse moyen</p>
            <p class="mt-1 text-3xl font-semibold text-ink-900 dark:text-sand-50">
                {{ $tempsReponseMoyen !== null ? $tempsReponseMoyen.' min' : '—' }}
            </p>
        </x-card>
        <x-card>
            <p class="text-sm text-ink-500 dark:text-sand-400">Taux de résolution</p>
            <p class="mt-1 text-3xl font-semibold text-emerald-600 dark:text-emerald-400">{{ $tauxResolution }}%</p>
        </x-card>
        <x-card>
            <p class="text-sm text-ink-500 dark:text-sand-400">Satisfaction moyenne</p>
            <p class="mt-1 text-3xl font-semibold text-ink-900 dark:text-sand-50">
                {{ $satisfactionMoyenne ? round($satisfactionMoyenne, 1).' / 5' : '—' }}
            </p>
            <p class="mt-0.5 text-xs text-ink-400 dark:text-sand-500">{{ $nombreAvis }} avis</p>
        </x-card>
        <x-card>
            <p class="text-sm text-ink-500 dark:text-sand-400">Volume total</p>
            <p class="mt-1 text-3xl font-semibold text-ink-900 dark:text-sand-50">{{ $totalConversations }}</p>
        </x-card>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
        <x-card class="lg:col-span-2">
            <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Volume de conversations (14 derniers jours)</h2>
            <div class="mt-4 h-64">
                <canvas id="chart-volume"></canvas>
            </div>
        </x-card>

        <x-card>
            <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Messages par canal</h2>
            <div class="mt-4 h-64">
                <canvas id="chart-canal"></canvas>
            </div>
        </x-card>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <x-card>
            <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Répartition par catégorie</h2>
            <div class="mt-4 h-56">
                <canvas id="chart-categorie"></canvas>
            </div>
        </x-card>

        <x-card>
            <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Répartition par statut</h2>
            <div class="mt-4 h-56">
                <canvas id="chart-statut"></canvas>
            </div>
        </x-card>
    </div>

    <x-card class="mt-4 !p-0">
        <div class="px-5 py-4">
            <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Performance par agent</h2>
        </div>
        <div class="overflow-x-auto border-t border-sand-200 dark:border-ink-800">
            <table class="min-w-full divide-y divide-sand-200 dark:divide-ink-800">
                <thead>
                    <tr class="text-left text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-sand-500">
                        <th class="px-5 py-2.5">Agent</th>
                        <th class="px-5 py-2.5">Conversations</th>
                        <th class="px-5 py-2.5">Résolues</th>
                        <th class="px-5 py-2.5">Temps de réponse moyen</th>
                        <th class="px-5 py-2.5">Satisfaction</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sand-100 dark:divide-ink-800">
                    @forelse ($performanceParAgent as $ligne)
                        <tr class="text-sm">
                            <td class="px-5 py-3 font-medium text-ink-900 dark:text-sand-50">{{ $ligne['agent']->name }}</td>
                            <td class="px-5 py-3 text-ink-600 dark:text-sand-300">{{ $ligne['conversations'] }}</td>
                            <td class="px-5 py-3 text-ink-600 dark:text-sand-300">{{ $ligne['resolues'] }}</td>
                            <td class="px-5 py-3 text-ink-600 dark:text-sand-300">{{ $ligne['temps_reponse'] !== null ? $ligne['temps_reponse'].' min' : '—' }}</td>
                            <td class="px-5 py-3 text-ink-600 dark:text-sand-300">{{ $ligne['satisfaction'] !== null ? $ligne['satisfaction'].' / 5' : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-14"><x-empty-state title="Aucune donnée disponible" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    @push('scripts')
        @vite('resources/js/analytics.js')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const isDark = document.documentElement.classList.contains('dark');
                const gridColor = isDark ? 'rgba(231,230,228,0.08)' : 'rgba(14,13,11,0.06)';
                const textColor = isDark ? '#A8A5A0' : '#7C7871';
                // Couleur du fond des cartes : sert de lisere entre les segments.
                const surface = isDark ? '#0E0D0B' : '#FFFFFF';
                const teinteNeutre = isDark ? '#5A564F' : '#A8A5A0';

                Chart.defaults.font.family = 'Figtree, ui-sans-serif, system-ui, sans-serif';
                Chart.defaults.color = textColor;

                new Chart(document.getElementById('chart-volume'), {
                    type: 'line',
                    data: {
                        labels: @json(array_map(fn($d) => \Illuminate\Support\Carbon::parse($d)->translatedFormat('d M'), array_keys($volumeParJour))),
                        datasets: [{
                            label: 'Conversations',
                            data: @json(array_values($volumeParJour)),
                            borderColor: '#87693D',
                            backgroundColor: 'rgba(135,105,61,0.12)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 2,
                        }],
                    },
                    options: {
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { display: false } },
                            y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: gridColor } },
                        },
                    },
                });

                // Palette categorielle : sept teintes distinctes, declinees pour chaque
                // fond. L'ancienne serie etait une rampe de bruns — deux segments voisins
                // n'y etaient separes que de 7 unites de difference perceptuelle, sous le
                // plancher de 15 : on ne les distinguait pas, meme avec une vision normale.
                const COULEURS_CANAL = isDark
                    ? { email: '#3987e5', whatsapp: '#d95926', instagram: '#199e70', messenger: '#c98500',
                        telegram: '#d55181', live_chat: '#008300', formulaire: '#9085e9' }
                    : { email: '#2a78d6', whatsapp: '#eb6834', instagram: '#1baf7a', messenger: '#eda100',
                        telegram: '#e87ba4', live_chat: '#008300', formulaire: '#4a3aa7' };

                // Chaque couleur suit son canal, jamais sa position : si un canal n'a
                // recu aucun message, les autres gardent la leur.
                const canaux = @json(array_keys($parCanal->toArray()));

                new Chart(document.getElementById('chart-canal'), {
                    type: 'doughnut',
                    data: {
                        labels: canaux.map(c => c.replace('_', ' ')),
                        datasets: [{
                            data: @json(array_values($parCanal->toArray())),
                            backgroundColor: canaux.map(c => COULEURS_CANAL[c] ?? teinteNeutre),
                            // Un liseré de la couleur du fond separe les segments voisins.
                            borderColor: surface,
                            borderWidth: 2,
                        }],
                    },
                    options: { plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12 } } } },
                });

                new Chart(document.getElementById('chart-categorie'), {
                    type: 'bar',
                    data: {
                        labels: @json(array_keys($parCategorie->toArray())),
                        datasets: [{
                            data: @json(array_values($parCategorie->toArray())),
                            backgroundColor: '#87693D',
                            borderRadius: 6,
                            maxBarThickness: 28,
                        }],
                    },
                    options: {
                        indexAxis: 'y',
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: gridColor } },
                            y: { grid: { display: false } },
                        },
                    },
                });

                // Couleurs d'etat, reservees aux statuts et jamais reutilisees comme
                // teintes de serie. Elles reprennent celles du tableau de bord, pour
                // qu'un statut garde la meme couleur d'un ecran a l'autre.
                const COULEURS_STATUT = {
                    nouveau: '#0EA5E9', en_cours: '#F59E0B', en_attente: '#FB923C', resolu: '#10B981',
                };
                const statuts = @json(array_keys($parStatut->toArray()));

                new Chart(document.getElementById('chart-statut'), {
                    type: 'doughnut',
                    data: {
                        labels: statuts.map(s => s.replace('_', ' ')),
                        datasets: [{
                            data: @json(array_values($parStatut->toArray())),
                            backgroundColor: statuts.map(s => COULEURS_STATUT[s] ?? teinteNeutre),
                            borderColor: surface,
                            borderWidth: 2,
                        }],
                    },
                    options: { plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12 } } } },
                });
            });
        </script>
    @endpush
</x-app-layout>
