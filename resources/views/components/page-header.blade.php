@props(['title', 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center justify-between gap-3']) }}>
    <div>
        <h1 class="text-xl font-semibold tracking-tight text-ink-900 dark:text-sand-50">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-0.5 text-sm text-ink-500 dark:text-sand-400">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex items-center gap-2">
            {{ $actions }}
        </div>
    @endisset
</div>
