@props(['title', 'description' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center rounded-xl border border-dashed border-sand-300 py-14 text-center dark:border-ink-700']) }}>
    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-sand-100 text-ink-400 dark:bg-ink-800 dark:text-sand-500">
        {{ $icon ?? '' }}
    </div>
    <p class="text-sm font-medium text-ink-700 dark:text-sand-200">{{ $title }}</p>
    @if ($description)
        <p class="mt-1 max-w-sm text-sm text-ink-400 dark:text-sand-500">{{ $description }}</p>
    @endif
</div>
