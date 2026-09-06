@props(['disabled' => false])

<textarea
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => 'block w-full rounded-lg border-sand-300 bg-white text-sm text-ink-900 shadow-sm placeholder:text-ink-400 focus:border-ink-500 focus:ring-ink-500 disabled:cursor-not-allowed disabled:opacity-60 dark:border-ink-700 dark:bg-ink-800 dark:text-sand-50 dark:placeholder:text-sand-500 dark:focus:border-sand-400 dark:focus:ring-sand-400']) }}
>{{ $slot }}</textarea>
