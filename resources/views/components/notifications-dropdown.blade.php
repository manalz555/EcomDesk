@php
    $notifications = auth()->user()->notifications()->latest()->take(8)->get();
    $unreadCount = auth()->user()->unreadNotifications()->count();
@endphp

<x-dropdown align="right" width="w-80">
    <x-slot name="trigger">
        <button type="button" class="relative inline-flex items-center justify-center rounded-lg p-2 text-ink-500 hover:bg-sand-200/70 hover:text-ink-900 dark:text-sand-300 dark:hover:bg-ink-800 dark:hover:text-sand-50" aria-label="Notifications">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>
            @if ($unreadCount > 0)
                <span class="absolute right-1 top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-semibold text-white">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            @endif
        </button>
    </x-slot>

    <x-slot name="content">
        <div class="flex items-center justify-between px-4 py-2.5">
            <span class="text-sm font-semibold text-ink-900 dark:text-sand-50">Notifications</span>
            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="text-xs font-medium text-ink-500 hover:underline dark:text-sand-400">
                        Tout marquer comme lu
                    </button>
                </form>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto border-t border-sand-200 dark:border-ink-700">
            @forelse ($notifications as $notification)
                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                    @csrf
                    <button type="submit" class="flex w-full items-start gap-2 px-4 py-3 text-left text-sm hover:bg-sand-100 dark:hover:bg-ink-700 {{ $notification->read_at ? '' : 'bg-sand-50 dark:bg-ink-800' }}">
                        <span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full {{ $notification->read_at ? 'bg-transparent' : 'bg-sand-600 dark:bg-sand-400' }}"></span>
                        <span class="min-w-0">
                            <span class="block truncate font-medium text-ink-900 dark:text-sand-50">
                                {{ ($notification->data['type'] ?? null) === 'draft_ready' ? 'Brouillon IA à valider' : 'Nouvelle conversation' }}
                                — {{ $notification->data['client_nom'] ?? '' }}
                            </span>
                            <span class="block truncate text-ink-500 dark:text-sand-400">{{ $notification->data['sujet'] ?? '' }}</span>
                            <span class="text-xs text-ink-400 dark:text-sand-500">{{ $notification->created_at->diffForHumans() }}</span>
                        </span>
                    </button>
                </form>
            @empty
                <p class="px-4 py-6 text-center text-sm text-ink-400 dark:text-sand-500">Aucune notification pour le moment.</p>
            @endforelse
        </div>
    </x-slot>
</x-dropdown>
