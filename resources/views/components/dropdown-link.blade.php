@props(['href'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'block w-full px-4 py-2 text-start text-sm text-ink-600 hover:bg-sand-100 dark:text-sand-200 dark:hover:bg-ink-700']) }}>
    {{ $slot }}
</a>
