@props(['status'])

@php
    [$label, $classes] = match ($status) {
        'nouveau' => ['Nouveau', 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-950 dark:text-sky-300 dark:ring-sky-400/20'],
        'en_cours' => ['En cours', 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-950 dark:text-amber-300 dark:ring-amber-400/20'],
        'en_attente' => ['En attente', 'bg-sand-200 text-ink-700 ring-ink-600/10 dark:bg-ink-700 dark:text-sand-200 dark:ring-sand-400/10'],
        'resolu' => ['Résolu', 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-950 dark:text-emerald-300 dark:ring-emerald-400/20'],
        default => [ucfirst((string) $status), 'bg-sand-200 text-ink-700 ring-ink-600/10 dark:bg-ink-700 dark:text-sand-200 dark:ring-sand-400/10'],
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset $classes"]) }}>
    {{ $label }}
</span>
