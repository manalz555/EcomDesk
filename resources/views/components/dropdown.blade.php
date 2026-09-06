@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white dark:bg-ink-800'])

@php
// "top" opens upward from the trigger (for triggers anchored near the bottom of the viewport,
// e.g. the sidebar account menu) — everything else opens downward as usual.
$positionClasses = match ($align) {
    'top' => 'bottom-full end-0 mb-2 origin-bottom',
    'left' => 'top-full start-0 mt-2 ltr:origin-top-left rtl:origin-top-right',
    default => 'top-full end-0 mt-2 ltr:origin-top-right rtl:origin-top-left',
};

$width = match ($width) {
    '48' => 'w-48',
    '56' => 'w-56',
    default => $width,
};
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 {{ $width }} {{ $positionClasses }} rounded-lg shadow-card"
        style="display: none;"
        @click="open = false"
    >
        <div class="rounded-lg border border-sand-200 dark:border-ink-700 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
