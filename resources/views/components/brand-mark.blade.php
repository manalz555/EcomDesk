@props(['size' => 'default', 'onDark' => false, 'rail' => false])

@php
    $iconSize = $size === 'lg' ? 'h-10 w-10' : 'h-8 w-8';
    $textSize = $size === 'lg' ? 'text-2xl' : 'text-lg';
    // "onDark" is for panels that are always dark regardless of the light/dark toggle
    // (e.g. the guest-layout branding panel) — text colors must not follow the theme there.
    $textColor = $onDark ? 'text-sand-50' : 'text-ink-900 dark:text-sand-50';
    $accentColor = $onDark ? 'text-sand-400' : 'text-sand-600 dark:text-sand-400';
    // "rail" collapses the wordmark into the hover-expand sidebar rail on desktop —
    // stays fully visible on mobile, where the sidebar is an all-or-nothing overlay.
    $textWrapClass = $rail
        ? 'ml-2.5 max-w-[160px] overflow-hidden whitespace-nowrap opacity-100 transition-all duration-300 ease-in-out lg:ml-0 lg:max-w-0 lg:opacity-0 lg:group-hover/rail:ml-2.5 lg:group-hover/rail:max-w-[160px] lg:group-hover/rail:opacity-100'
        : '';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center' . ($rail ? '' : ' gap-2.5')]) }}>
    <x-application-logo class="{{ $iconSize }} shrink-0" :on-dark="$onDark" />
    <span class="{{ $textWrapClass }} {{ $textSize }} font-semibold tracking-tight {{ $textColor }}">
        Ecom<span class="{{ $accentColor }}">Desk</span>
    </span>
</span>
