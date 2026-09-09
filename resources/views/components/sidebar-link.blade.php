@props(['href', 'active' => false, 'icon' => null])

<a href="{{ $href }}"
    {{ $attributes->merge(['class' => 'flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-colors lg:px-2 ' . ($active
        ? 'bg-ink-900 text-sand-50 dark:bg-sand-100 dark:text-ink-900'
        : 'text-ink-600 hover:bg-sand-200/60 hover:text-ink-900 dark:text-sand-300 dark:hover:bg-ink-800 dark:hover:text-sand-50')]) }}
>
    <span class="flex h-5 w-5 shrink-0 items-center justify-center">{{ $icon }}</span>
    <span class="rail-label">{{ $slot }}</span>
</a>
