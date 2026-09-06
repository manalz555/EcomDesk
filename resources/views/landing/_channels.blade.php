<section id="channels" class="border-y border-sand-200 bg-sand-100/60 py-14 dark:border-ink-800 dark:bg-ink-900/40" data-reveal>
    <div class="mx-auto max-w-5xl px-6">
        <p class="text-center text-sm font-medium uppercase tracking-wider text-ink-400 dark:text-sand-500">
            Connectez tous vos canaux, sans changer d'outil
        </p>

        <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
            @php
                $channels = [
                    ['label' => 'Email', 'delay' => '0s'],
                    ['label' => 'WhatsApp', 'delay' => '0.3s'],
                    ['label' => 'Instagram', 'delay' => '0.6s'],
                    ['label' => 'Messenger', 'delay' => '0.9s'],
                    ['label' => 'Telegram', 'delay' => '1.2s'],
                    ['label' => 'Live Chat', 'delay' => '1.5s'],
                ];
            @endphp

            @foreach ($channels as $channel)
                <div class="flex flex-col items-center gap-2 rounded-xl px-3 py-4 text-center">
                    <span class="flex h-12 w-12 items-center justify-center animate-float" style="animation-delay: {{ $channel['delay'] }};">
                        @switch($channel['label'])
                            @case('Email')
                                <svg viewBox="0 0 48 48" class="h-11 w-11 drop-shadow-sm">
                                    <rect x="3" y="9" width="42" height="30" rx="6" fill="#4A8FE7" />
                                    <path d="M6 13 L24 27 L42 13" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            @break

                            @case('WhatsApp')
                                <svg viewBox="0 0 48 48" class="h-11 w-11 drop-shadow-sm">
                                    <circle cx="24" cy="24" r="21" fill="#25D366" />
                                    <path d="M24 13.5a10.5 10.5 0 0 0-9 15.9L13.5 34.5l5.3-1.4a10.5 10.5 0 1 0 5.2-19.6Z" fill="white" />
                                    <path d="M24 15.8a8.2 8.2 0 0 0-6.9 12.6l.3.5-.8 3 3.1-.8.5.3a8.2 8.2 0 1 0 3.8-15.6Z" fill="#25D366" />
                                    <path d="M20.6 19.6c-.3-.6-.5-.6-.8-.6h-.6c-.2 0-.6.1-.9.4-.3.3-1.2 1.1-1.2 2.8s1.2 3.2 1.4 3.5c.2.3 2.4 3.7 5.9 5 2.9 1.1 3.5.9 4.1.8.6-.1 1.9-.8 2.2-1.5.3-.7.3-1.4.2-1.5-.1-.2-.3-.3-.6-.4-.3-.2-1.9-1-2.2-1.1-.3-.1-.5-.2-.7.2-.2.3-.8 1-1 1.3-.2.2-.4.3-.7.1-.3-.2-1.4-.5-2.6-1.6-1-.9-1.6-2-1.8-2.3-.2-.3 0-.5.1-.6.1-.1.3-.4.5-.5.2-.2.2-.4.3-.6.1-.2 0-.4 0-.6-.1-.2-.7-1.9-1-2.6Z" fill="white" />
                                </svg>
                            @break

                            @case('Instagram')
                                <svg viewBox="0 0 48 48" class="h-11 w-11 drop-shadow-sm">
                                    <defs>
                                        <linearGradient id="ig-grad" x1="0" y1="48" x2="48" y2="0">
                                            <stop offset="0%" stop-color="#FEDA75" />
                                            <stop offset="35%" stop-color="#D62976" />
                                            <stop offset="70%" stop-color="#962FBF" />
                                            <stop offset="100%" stop-color="#4F5BD5" />
                                        </linearGradient>
                                    </defs>
                                    <rect x="3" y="3" width="42" height="42" rx="12" fill="url(#ig-grad)" />
                                    <rect x="13" y="13" width="22" height="22" rx="7" fill="none" stroke="white" stroke-width="2.6" />
                                    <circle cx="24" cy="24" r="6" fill="none" stroke="white" stroke-width="2.6" />
                                    <circle cx="33" cy="15" r="1.8" fill="white" />
                                </svg>
                            @break

                            @case('Messenger')
                                <svg viewBox="0 0 48 48" class="h-11 w-11 drop-shadow-sm">
                                    <defs>
                                        <linearGradient id="msg-grad" x1="0" y1="48" x2="48" y2="0">
                                            <stop offset="0%" stop-color="#00B2FF" />
                                            <stop offset="50%" stop-color="#2E7DFF" />
                                            <stop offset="100%" stop-color="#B620E0" />
                                        </linearGradient>
                                    </defs>
                                    <path d="M24 4C12.4 4 3 12.6 3 23.4c0 5.9 2.8 11.1 7.3 14.7v7l6.7-3.7c1.9.5 4 .8 6 .8 11.6 0 21-8.6 21-19.4S35.6 4 24 4Z" fill="url(#msg-grad)" />
                                    <path d="M13 27.5 21 19l5 5 8-8.5-8 9-5-5-8 8Z" fill="white" />
                                </svg>
                            @break

                            @case('Telegram')
                                <svg viewBox="0 0 48 48" class="h-11 w-11 drop-shadow-sm">
                                    <circle cx="24" cy="24" r="21" fill="#26A5E4" />
                                    <path d="M11 23.8 34.5 14.6c1.1-.4 2.1.3 1.7 2l-3.9 18.4c-.3 1.3-1.1 1.6-2.2 1l-6.1-4.5-2.9 2.8c-.3.3-.6.5-1.2.5l.4-6.2 11.3-10.2c.5-.4-.1-.7-.7-.3L16.9 26.1l-6-1.9c-1.3-.4-1.3-1.3.3-1.9Z" fill="white" />
                                </svg>
                            @break

                            @default
                                <svg viewBox="0 0 48 48" class="h-11 w-11 drop-shadow-sm">
                                    <circle cx="24" cy="24" r="21" fill="#C0A36C" />
                                    <path d="M14 18a4 4 0 0 1 4-4h12a4 4 0 0 1 4 4v7a4 4 0 0 1-4 4h-8l-5 4v-4h-3a4 4 0 0 1-4-4v-7Z" fill="white" />
                                    <circle cx="30" cy="15" r="3.5" fill="#34D399" stroke="white" stroke-width="1.5" />
                                </svg>
                        @endswitch
                    </span>
                    <span class="text-xs font-medium text-ink-600 dark:text-sand-300">{{ $channel['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>
