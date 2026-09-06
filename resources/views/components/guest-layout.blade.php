<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        @include('partials.theme-init')
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' · ' : '' }}{{ config('app.name', 'EcomDesk') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="flex min-h-screen bg-white dark:bg-ink-950">

            <div class="relative hidden w-1/2 flex-col justify-between overflow-hidden bg-ink-900 p-12 text-sand-50 lg:flex">
                <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 animate-float rounded-full bg-sand-500/10" style="animation-duration: 7s;"></div>
                <div class="pointer-events-none absolute -bottom-32 -left-16 h-96 w-96 animate-float rounded-full bg-sand-500/10" style="animation-duration: 9s; animation-delay: -3s;"></div>

                <a href="{{ route('home') }}" class="relative z-10">
                    <x-brand-mark size="lg" :on-dark="true" />
                </a>

                <div
                    class="relative z-10 max-w-md"
                    x-data="{
                        quotes: [
                            { quote: 'Chaque message client, sur chaque canal, dans une seule messagerie sereine.', sub: 'EcomDesk centralise les conversations de votre équipe pour qu\'aucune demande ne passe entre les mailles du filet.' },
                            { quote: 'Répondez plus vite, perdez moins de demandes, gardez une équipe alignée.', sub: 'Un tableau de bord unique pour suivre chaque conversation, du premier message à la résolution.' },
                            { quote: 'De WhatsApp à l\'email, tout arrive au même endroit — organisé, tracé, sous contrôle.', sub: 'Étiquettes, priorités et historique complet pour ne jamais perdre le fil.' },
                            { quote: 'Laissez l\'automatisation gérer les demandes courantes.', sub: 'Votre équipe se concentre sur ce qui compte vraiment, pendant que l\'IA prépare les réponses.' },
                        ],
                        index: 0,
                        visible: true,
                        init() {
                            setInterval(() => {
                                this.visible = false;
                                setTimeout(() => {
                                    this.index = (this.index + 1) % this.quotes.length;
                                    this.visible = true;
                                }, 500);
                            }, 3500);
                        },
                    }"
                >
                    <div class="min-h-32">
                        <p
                            x-show="visible"
                            x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="opacity-0 translate-y-3"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-500"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-3"
                            class="text-2xl font-medium leading-relaxed text-sand-50"
                        >
                            &laquo;&nbsp;<span x-text="quotes[index].quote"></span>&nbsp;&raquo;
                        </p>
                        <p
                            x-show="visible"
                            x-transition:enter="transition ease-out duration-500 delay-100"
                            x-transition:enter-start="opacity-0 translate-y-3"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-3"
                            class="mt-4 text-sm text-sand-400"
                            x-text="quotes[index].sub"
                        ></p>
                    </div>

                    <div class="mt-5 flex items-center gap-1.5">
                        <template x-for="(q, i) in quotes" :key="i">
                            <span class="h-1.5 rounded-full transition-all" :class="i === index ? 'w-5 bg-sand-100' : 'w-1.5 bg-sand-100/30'"></span>
                        </template>
                    </div>
                </div>

                <p class="relative z-10 text-xs text-sand-500">&copy; {{ date('Y') }} EcomDesk. Tous droits réservés.</p>
            </div>

            <div class="flex w-full flex-col justify-center px-6 py-12 sm:px-12 lg:w-1/2 lg:px-20">
                <div class="mx-auto w-full max-w-sm">
                    <div class="mb-8 flex items-center justify-between lg:hidden">
                        <a href="{{ route('home') }}">
                            <x-brand-mark />
                        </a>
                        <x-dark-mode-toggle />
                    </div>

                    <div class="hidden justify-end lg:flex">
                        <x-dark-mode-toggle class="mb-4" />
                    </div>

                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
