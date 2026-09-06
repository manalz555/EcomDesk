@props(['value'])

<label {{ $attributes->merge(['class' => 'mb-1.5 block text-sm font-medium text-ink-700 dark:text-sand-200']) }}>
    {{ $value ?? $slot }}
</label>
