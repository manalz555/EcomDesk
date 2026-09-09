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
        <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden bg-sand-50 text-ink-900 dark:bg-ink-950 dark:text-sand-50">

            <x-toast />

            <div
                x-show="sidebarOpen"
                x-cloak
                @click="sidebarOpen = false"
                class="fixed inset-0 z-30 bg-ink-900/40 lg:hidden"
            ></div>

            <aside
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                class="rail fixed inset-y-0 left-0 z-40 flex w-64 shrink-0 flex-col overflow-x-hidden border-r border-sand-200 bg-white dark:border-ink-800 dark:bg-ink-900 lg:translate-x-0 lg:hover:shadow-2xl lg:hover:shadow-ink-900/10"
            >
                <div class="flex h-16 shrink-0 items-center border-b border-sand-200 px-5 dark:border-ink-800 lg:px-[18px]">
                    <a href="{{ route('dashboard') }}">
                        <x-brand-mark rail />
                    </a>
                </div>

                @include('layouts.partials.sidebar-nav')

                <div class="shrink-0 border-t border-sand-200 p-3 dark:border-ink-800">
                    <x-dropdown align="top" width="56">
                        <x-slot name="trigger">
                            <button type="button" class="flex w-full items-center rounded-lg px-2 py-2 text-left hover:bg-sand-100 dark:hover:bg-ink-800">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-ink-900 text-sm font-semibold text-sand-50 dark:bg-sand-100 dark:text-ink-900">
                                    {{ Illuminate\Support\Str::of(auth()->user()->name)->substr(0, 1)->upper() }}
                                </span>
                                <span class="rail-label min-w-0 flex-1">
                                    <span class="block truncate text-sm font-medium text-ink-900 dark:text-sand-50">{{ auth()->user()->name }}</span>
                                    <span class="block truncate text-xs capitalize text-ink-400 dark:text-sand-400">{{ auth()->user()->isAdmin() ? 'Administrateur' : (auth()->user()->isManager() ? 'Manager' : 'Agent') }}</span>
                                </span>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @if (Route::has('profile.edit'))
                                <x-dropdown-link :href="route('profile.edit')">Profil</x-dropdown-link>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a
                                    href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="block w-full px-4 py-2 text-start text-sm font-medium text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/40"
                                >
                                    Déconnexion
                                </a>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </aside>

            <div class="flex flex-1 flex-col overflow-hidden lg:ml-[76px]">

                <header class="flex h-16 shrink-0 items-center gap-4 border-b border-sand-200 bg-white/80 px-4 backdrop-blur dark:border-ink-800 dark:bg-ink-900/80 sm:px-6">
                    <button @click="sidebarOpen = true" type="button" class="text-ink-500 hover:text-ink-900 dark:text-sand-300 dark:hover:text-sand-50 lg:hidden">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                        </svg>
                    </button>

                    <div class="min-w-0 flex-1">
                        @if (Route::has('search.index'))
                            <form action="{{ route('search.index') }}" method="GET" class="max-w-md">
                                <label for="topbar-search" class="sr-only">Rechercher</label>
                                <div class="relative">
                                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4.5 w-4.5 -translate-y-1/2 text-ink-400 dark:text-sand-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                    </svg>
                                    <input
                                        id="topbar-search"
                                        type="search"
                                        name="q"
                                        placeholder="Rechercher une conversation, un client…"
                                        class="w-full rounded-lg border-sand-200 bg-sand-50 py-2 pl-9 text-sm text-ink-900 placeholder:text-ink-400 focus:border-ink-400 focus:ring-ink-400 dark:border-ink-700 dark:bg-ink-800 dark:text-sand-50 dark:placeholder:text-sand-500"
                                    />
                                </div>
                            </form>
                        @endif
                    </div>

                    <div class="flex shrink-0 items-center gap-1">
                        <x-dark-mode-toggle />
                        <x-notifications-dropdown />
                    </div>
                </header>

                @isset($header)
                    <div class="border-b border-sand-200 bg-white px-4 py-5 dark:border-ink-800 dark:bg-ink-900 sm:px-6">
                        {{ $header }}
                    </div>
                @endisset

                <main class="scrollbar-gold flex-1 overflow-y-auto overscroll-contain">
                    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
