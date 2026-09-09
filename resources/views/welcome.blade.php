<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        @include('partials.theme-init')
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'EcomDesk') }} — Vos clients écrivent partout, vous répondez à un seul endroit</title>
        <meta name="description" content="EcomDesk réunit WhatsApp, Instagram, Messenger, Telegram, l'email et le chat de votre site dans une boîte de réception partagée, avec des rôles, un suivi et des mesures.">
        <link rel="preconnect" href="https://fonts.bunny.net">
        {{-- Figtree porte l'interface ; Literata, serif douce et peu contrastée,
             ne sert qu'aux titres de la page publique. --}}
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|literata:300,400,500&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white font-sans antialiased dark:bg-ink-950">
        @include('landing._nav')

        <main>
            @include('landing._hero')
            @include('landing._probleme')
            @include('landing._cycle')
            @include('landing._automation')
            @include('landing._channels')
            @include('landing._cta')
        </main>

        @include('landing._footer')

        {{-- Le widget de chat, en conditions réelles : la même ligne qu'un
             site marchand collerait chez lui. Les messages envoyés ici
             arrivent dans la boîte de réception EcomDesk. --}}
        <script src="{{ asset('widget.js') }}" defer></script>
    </body>
</html>
