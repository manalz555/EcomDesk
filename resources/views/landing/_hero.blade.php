@php
    // Depose une photo dans public/images/hero-1.jpg (a hero-4.jpg) et elle remplace
    // automatiquement le degrade de secours ci-dessous — aucun code a changer.
    $slides = collect([
        ['file' => 'hero-1.jpg', 'gradient' => 'linear-gradient(135deg, #2E2C28 0%, #57432C 60%, #87693D 100%)'],
        ['file' => 'hero-2.jpg', 'gradient' => 'linear-gradient(135deg, #1C1B18 0%, #413E39 50%, #6B5333 100%)'],
        ['file' => 'hero-3.jpg', 'gradient' => 'linear-gradient(135deg, #0E0D0B 0%, #57432C 55%, #A6854F 100%)'],
        ['file' => 'hero-4.jpg', 'gradient' => 'linear-gradient(135deg, #2E2C28 0%, #6B5333 45%, #C0A36C 100%)'],
    ])->map(function ($slide) {
        $path = public_path('images/'.$slide['file']);
        $slide['url'] = file_exists($path) ? asset('images/'.$slide['file']) : null;

        return $slide;
    });
@endphp

<section
    class="relative overflow-hidden"
    x-data="{
        active: 0,
        total: {{ $slides->count() }},
        timer: null,
        init() { this.restart(); },
        restart() { clearInterval(this.timer); this.timer = setInterval(() => this.next(), 5000); },
        next() { this.active = (this.active + 1) % this.total; },
        prev() { this.active = (this.active - 1 + this.total) % this.total; },
        go(i) { this.active = i; this.restart(); },
    }"
>
    {{-- Diaporama en arriere-plan --}}
    <div class="absolute inset-0 -z-20">
        @foreach ($slides as $i => $slide)
            <div
                class="absolute inset-0 bg-cover bg-center transition-opacity duration-1000 ease-in-out"
                :class="active === {{ $i }} ? 'opacity-100' : 'opacity-0'"
                style="{{ $slide['url'] ? "background-image: url('{$slide['url']}');" : "background-image: {$slide['gradient']};" }}"
            ></div>
        @endforeach
        {{-- Voile sombre a faible luminosite : garde le texte lisible sur n'importe quelle photo --}}
        <div class="absolute inset-0 bg-ink-950/55"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-ink-950/40 via-transparent to-sand-50 dark:to-ink-950"></div>
    </div>

    <div class="relative mx-auto max-w-4xl px-6 pt-20 pb-16 text-center sm:pt-28 sm:pb-20">
        <h1 class="text-4xl font-semibold tracking-tight text-white sm:text-6xl">
            Toutes vos conversations client,<br class="hidden sm:block"> dans une seule messagerie.
        </h1>
        <p class="mx-auto mt-5 max-w-xl text-lg text-sand-100">
            EcomDesk centralise email, WhatsApp, Instagram, Messenger, Telegram et le chat en direct dans un tableau de bord unique pour votre équipe.
        </p>

        <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="w-full rounded-lg bg-sand-100 px-5 py-2.5 text-center text-sm font-semibold text-ink-900 shadow-soft hover:bg-white sm:w-auto">
                    Commencer gratuitement
                </a>
            @endif
            <a href="{{ route('login') }}" class="w-full rounded-lg border border-white/30 px-5 py-2.5 text-center text-sm font-semibold text-white backdrop-blur hover:bg-white/10 sm:w-auto">
                Se connecter
            </a>
        </div>

        {{-- Fleches precedent / suivant --}}
        <button
            @click="prev(); restart()"
            aria-label="Image précédente"
            class="absolute left-2 top-1/2 hidden -translate-y-1/2 rounded-full border border-white/20 bg-white/10 p-2 text-white backdrop-blur transition hover:bg-white/20 sm:flex"
        >
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
        </button>
        <button
            @click="next(); restart()"
            aria-label="Image suivante"
            class="absolute right-2 top-1/2 hidden -translate-y-1/2 rounded-full border border-white/20 bg-white/10 p-2 text-white backdrop-blur transition hover:bg-white/20 sm:flex"
        >
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
        </button>

        {{-- Puces de navigation --}}
        <div class="mt-8 flex items-center justify-center gap-2">
            @foreach ($slides as $i => $slide)
                <button
                    @click="go({{ $i }})"
                    aria-label="Aller à l'image {{ $i + 1 }}"
                    class="h-1.5 rounded-full transition-all"
                    :class="active === {{ $i }} ? 'w-6 bg-white' : 'w-1.5 bg-white/40 hover:bg-white/60'"
                ></button>
            @endforeach
        </div>
    </div>

    <div class="relative mx-auto max-w-4xl px-6 pb-24">
        <div class="overflow-hidden rounded-2xl border border-sand-200 bg-white shadow-card dark:border-ink-800 dark:bg-ink-900">
            <div class="flex items-center gap-1.5 border-b border-sand-200 px-4 py-3 dark:border-ink-800">
                <span class="h-2.5 w-2.5 rounded-full bg-sand-300 dark:bg-ink-700"></span>
                <span class="h-2.5 w-2.5 rounded-full bg-sand-300 dark:bg-ink-700"></span>
                <span class="h-2.5 w-2.5 rounded-full bg-sand-300 dark:bg-ink-700"></span>
                <span class="ml-3 text-xs font-medium text-ink-400 dark:text-sand-500">Conversations</span>
            </div>
            <table class="min-w-full divide-y divide-sand-100 dark:divide-ink-800">
                <tbody class="divide-y divide-sand-100 text-left dark:divide-ink-800">
                    @foreach ([
                        ['client' => 'Sara Alaoui', 'sujet' => 'Ma commande n\'est pas encore arrivée', 'canal' => 'WhatsApp', 'statut' => 'nouveau', 'priorite' => 'haute'],
                        ['client' => 'Youssef Bennani', 'sujet' => 'Question sur le remboursement', 'canal' => 'Instagram', 'statut' => 'en_cours', 'priorite' => 'moyenne'],
                        ['client' => 'Imane Chraibi', 'sujet' => 'Merci pour votre réactivité !', 'canal' => 'Email', 'statut' => 'resolu', 'priorite' => 'faible'],
                    ] as $row)
                        <tr class="text-sm">
                            <td class="px-4 py-3 font-medium text-ink-900 dark:text-sand-50">{{ $row['client'] }}</td>
                            <td class="hidden px-4 py-3 text-ink-500 dark:text-sand-400 sm:table-cell">{{ $row['sujet'] }}</td>
                            <td class="hidden px-4 py-3 text-ink-400 dark:text-sand-500 md:table-cell">{{ $row['canal'] }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$row['statut']" /></td>
                            <td class="hidden px-4 py-3 sm:table-cell"><x-priority-badge :priority="$row['priorite']" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
