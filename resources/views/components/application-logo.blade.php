@props(['onDark' => false])

<svg viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
    <rect width="40" height="40" rx="11" class="{{ $onDark ? 'fill-sand-100' : 'fill-ink-900 dark:fill-sand-100' }}" />
    <path d="M13 13.5C13 12.6716 13.6716 12 14.5 12H26.5C27.3284 12 28 12.6716 28 13.5C28 14.3284 27.3284 15 26.5 15H16V18.5H24.5C25.3284 18.5 26 19.1716 26 20C26 20.8284 25.3284 21.5 24.5 21.5H16V25H26.5C27.3284 25 28 25.6716 28 26.5C28 27.3284 27.3284 28 26.5 28H14.5C13.6716 28 13 27.3284 13 26.5V13.5Z" class="{{ $onDark ? 'fill-ink-900' : 'fill-sand-100 dark:fill-ink-900' }}" />
</svg>
