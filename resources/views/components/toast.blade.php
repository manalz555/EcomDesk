@if (session('success') || session('error'))
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 4000)"
        x-show="show"
        x-transition
        x-cloak
        class="fixed right-4 top-4 z-50 w-full max-w-sm rounded-lg border p-4 shadow-card
            {{ session('success')
                ? 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-200'
                : 'border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-800 dark:bg-rose-950 dark:text-rose-200' }}"
    >
        <div class="flex items-start gap-2.5">
            <span class="text-sm font-medium">{{ session('success') ?? session('error') }}</span>
            <button @click="show = false" type="button" class="ml-auto shrink-0 opacity-60 hover:opacity-100">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
            </button>
        </div>
    </div>
@endif

@if ($errors->any())
    <div
        x-data="{ show: true }"
        x-show="show"
        x-transition
        x-cloak
        class="fixed right-4 top-4 z-50 w-full max-w-sm rounded-lg border border-rose-200 bg-rose-50 p-4 shadow-card text-rose-800 dark:border-rose-800 dark:bg-rose-950 dark:text-rose-200"
    >
        <div class="flex items-start gap-2.5">
            <ul class="space-y-0.5 text-sm font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button @click="show = false" type="button" class="ml-auto shrink-0 opacity-60 hover:opacity-100">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
            </button>
        </div>
    </div>
@endif
