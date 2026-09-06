@props(['field', 'label', 'sort', 'direction'])

@php
    $isActive = $sort === $field;
    $nextDirection = $isActive && $direction === 'asc' ? 'desc' : 'asc';
@endphp

<a
    href="{{ request()->fullUrlWithQuery(['sort' => $field, 'direction' => $nextDirection]) }}"
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 hover:text-ink-700 dark:hover:text-sand-200']) }}
>
    {{ $label }}
    <svg class="h-3 w-3 {{ $isActive ? '' : 'opacity-30' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
        @if ($isActive && $direction === 'asc')
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
        @else
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        @endif
    </svg>
</a>
