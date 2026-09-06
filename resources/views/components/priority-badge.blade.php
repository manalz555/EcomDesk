@props(['priority'])

@php
    [$label, $classes] = match ($priority) {
        'haute' => ['Haute', 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-950 dark:text-rose-300 dark:ring-rose-400/20'],
        'moyenne' => ['Moyenne', 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-950 dark:text-amber-300 dark:ring-amber-400/20'],
        'faible' => ['Faible', 'bg-sand-200 text-ink-700 ring-ink-600/10 dark:bg-ink-700 dark:text-sand-200 dark:ring-sand-400/10'],
        default => [ucfirst((string) $priority), 'bg-sand-200 text-ink-700 ring-ink-600/10 dark:bg-ink-700 dark:text-sand-200 dark:ring-sand-400/10'],
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset $classes"]) }}>
    {{ $label }}
</span>
