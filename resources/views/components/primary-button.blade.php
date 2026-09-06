<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 rounded-lg bg-ink-900 px-4 py-2 text-sm font-semibold text-sand-50 shadow-soft transition-colors hover:bg-ink-800 focus:outline-none focus:ring-2 focus:ring-ink-500 focus:ring-offset-2 dark:bg-sand-100 dark:text-ink-900 dark:hover:bg-sand-200 dark:focus:ring-offset-ink-950']) }}>
    {{ $slot }}
</button>
