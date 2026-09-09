@php $user = auth()->user(); @endphp

@php
    $sectionLabelClass = 'rail-section text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-sand-500';
@endphp

<nav class="rail-scroll flex-1 space-y-6 overflow-y-auto overscroll-contain px-3 py-4">
    <div class="space-y-1">
        <p class="{{ $sectionLabelClass }}">Espace de travail</p>

        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <x-slot:icon>
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                </svg>
            </x-slot:icon>
            Tableau de bord
        </x-sidebar-link>

        @if (Route::has('conversations.index'))
            <x-sidebar-link :href="route('conversations.index')" :active="request()->routeIs('conversations.*')">
                <x-slot:icon>
                    <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3h16.5M3.75 3v6.75L1.5 15v4.125c0 .621.504 1.125 1.125 1.125h18.75c.621 0 1.125-.504 1.125-1.125V15l-2.25-5.25V3M3.75 3l2.25 6.75h4.5A2.25 2.25 0 0 1 12.75 12v0a2.25 2.25 0 0 0 2.25 2.25h0A2.25 2.25 0 0 0 17.25 12v0a2.25 2.25 0 0 1 2.25-2.25h4.5" />
                    </svg>
                </x-slot:icon>
                Conversations
            </x-sidebar-link>
        @endif

        @if (Route::has('clients.index'))
            <x-sidebar-link :href="route('clients.index')" :active="request()->routeIs('clients.*')">
                <x-slot:icon>
                    <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </x-slot:icon>
                Clients
            </x-sidebar-link>
        @endif

        @if ($user?->canManageTeam() && Route::has('analytics.index'))
            <x-sidebar-link :href="route('analytics.index')" :active="request()->routeIs('analytics.*')">
                <x-slot:icon>
                    <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                    </svg>
                </x-slot:icon>
                Analytique
            </x-sidebar-link>
        @endif
    </div>

    @if ($user?->canManageTeam())
        <div class="space-y-1">
            <p class="{{ $sectionLabelClass }}">Gestion</p>

            @if ($user?->isAdmin() && Route::has('agents.index'))
                <x-sidebar-link :href="route('agents.index')" :active="request()->routeIs('agents.*')">
                    <x-slot:icon>
                        <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477M12 3.75a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm9 0a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm-15 0a3 3 0 1 1 0 6 3 3 0 0 1 0-6Z" />
                        </svg>
                    </x-slot:icon>
                    Agents
                </x-sidebar-link>
            @endif

            @if (Route::has('teams.index'))
                <x-sidebar-link :href="route('teams.index')" :active="request()->routeIs('teams.*')">
                    <x-slot:icon>
                        <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                    </x-slot:icon>
                    Équipes
                </x-sidebar-link>
            @endif

            @if ($user?->isAdmin() && Route::has('companies.index'))
                <x-sidebar-link :href="route('companies.index')" :active="request()->routeIs('companies.*')">
                    <x-slot:icon>
                        <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5M4.5 3h15M5.25 3v18m13.5-18v18M8.25 6.75h.75m-.75 3h.75m-.75 3h.75m6-6h.75m-.75 3h.75m-.75 3h.75" />
                        </svg>
                    </x-slot:icon>
                    Entreprises
                </x-sidebar-link>
            @endif
        </div>
    @endif

    @if ($user?->isAdmin() && Route::has('settings.index'))
        <div class="space-y-1">
            <p class="{{ $sectionLabelClass }}">Espace</p>

            <x-sidebar-link :href="route('settings.index')" :active="request()->routeIs('settings.*')">
                <x-slot:icon>
                    <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.245a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 0 1 0 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 0 1 0-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </x-slot:icon>
                Paramètres
            </x-sidebar-link>

            @if (Route::has('integrations.index'))
                <x-sidebar-link :href="route('integrations.index')" :active="request()->routeIs('integrations.*')">
                    <x-slot:icon>
                        <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                        </svg>
                    </x-slot:icon>
                    Intégrations
                </x-sidebar-link>
            @endif
        </div>
    @endif
</nav>
