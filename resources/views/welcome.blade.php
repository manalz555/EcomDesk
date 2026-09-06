<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        @include('partials.theme-init')
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'EcomDesk') }} — Toutes vos conversations client, un seul endroit</title>
        <meta name="description" content="EcomDesk centralise email, WhatsApp, Instagram, Messenger, Telegram et le chat en direct dans une seule messagerie pour votre équipe.">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white font-sans antialiased dark:bg-ink-950">
        @include('landing._nav')
        @include('landing._hero')
        @include('landing._channels')
        @include('landing._features')
        @include('landing._automation')
        @include('landing._cta')
        @include('landing._footer')

        {{-- Le widget de chat, en conditions réelles : la même ligne qu'un
             site marchand collerait chez lui. Les messages envoyés ici
             arrivent dans la boîte de réception EcomDesk. --}}
        <script src="{{ asset('widget.js') }}" defer></script>
    </body>
</html>
