{{-- Pagination aux couleurs EcomDesk (sable/encre, clair + sombre) — remplace
     la vue Tailwind par défaut de Laravel (Paginator::useTailwind() dans
     AppServiceProvider rend cette vue pour tous les ->links()). --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between">

        {{-- Mobile : précédent / suivant uniquement --}}
        <div class="flex flex-1 justify-between gap-2 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex cursor-default items-center rounded-lg border border-sand-200 bg-white px-4 py-2 text-sm font-medium text-ink-300 dark:border-ink-700 dark:bg-ink-900 dark:text-sand-600">Précédent</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center rounded-lg border border-sand-200 bg-white px-4 py-2 text-sm font-medium text-ink-700 transition hover:bg-sand-100 dark:border-ink-700 dark:bg-ink-900 dark:text-sand-200 dark:hover:bg-ink-800">Précédent</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center rounded-lg border border-sand-200 bg-white px-4 py-2 text-sm font-medium text-ink-700 transition hover:bg-sand-100 dark:border-ink-700 dark:bg-ink-900 dark:text-sand-200 dark:hover:bg-ink-800">Suivant</a>
            @else
                <span class="inline-flex cursor-default items-center rounded-lg border border-sand-200 bg-white px-4 py-2 text-sm font-medium text-ink-300 dark:border-ink-700 dark:bg-ink-900 dark:text-sand-600">Suivant</span>
            @endif
        </div>

        {{-- Desktop : compteur + pages --}}
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <p class="text-sm text-ink-500 dark:text-sand-400">
                {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} sur {{ $paginator->total() }} résultats
            </p>

            <div class="flex items-center gap-1.5">
                {{-- Flèche précédente --}}
                @if ($paginator->onFirstPage())
                    <span class="flex h-9 w-9 cursor-default items-center justify-center rounded-lg text-ink-300 dark:text-sand-600" aria-hidden="true">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Page précédente" class="flex h-9 w-9 items-center justify-center rounded-lg text-ink-600 transition hover:bg-sand-100 dark:text-sand-300 dark:hover:bg-ink-800">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
                    </a>
                @endif

                {{-- Numéros de page --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="flex h-9 w-9 cursor-default items-center justify-center text-sm text-ink-400 dark:text-sand-500">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="flex h-9 w-9 cursor-default items-center justify-center rounded-lg bg-ink-900 text-sm font-semibold text-sand-50 dark:bg-sand-100 dark:text-ink-900">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" aria-label="Aller à la page {{ $page }}" class="flex h-9 w-9 items-center justify-center rounded-lg text-sm font-medium text-ink-600 transition hover:bg-sand-100 dark:text-sand-300 dark:hover:bg-ink-800">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Flèche suivante --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Page suivante" class="flex h-9 w-9 items-center justify-center rounded-lg text-ink-600 transition hover:bg-sand-100 dark:text-sand-300 dark:hover:bg-ink-800">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                    </a>
                @else
                    <span class="flex h-9 w-9 cursor-default items-center justify-center rounded-lg text-ink-300 dark:text-sand-600" aria-hidden="true">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
