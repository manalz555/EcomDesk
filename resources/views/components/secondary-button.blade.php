<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 rounded-lg border border-sand-300 bg-white px-4 py-2 text-sm font-semibold text-ink-700 shadow-sm transition-colors hover:bg-sand-50 focus:outline-none focus:ring-2 focus:ring-ink-400 focus:ring-offset-2 dark:border-ink-700 dark:bg-ink-900 dark:text-sand-200 dark:hover:bg-ink-800 dark:focus:ring-offset-ink-950']) }}>
    {{ $slot }}
</button>
