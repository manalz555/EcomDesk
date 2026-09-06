<x-app-layout title="Paramètres">
    <x-slot name="header">
        <x-page-header title="Paramètres" subtitle="Profil de l'entreprise, horaires et apparence." />
    </x-slot>

    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="max-w-3xl space-y-6">
        @csrf
        @method('PATCH')

        <x-card>
            <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Profil de l'entreprise</h2>
            <p class="mt-1 text-sm text-ink-500 dark:text-sand-400">Le nom et le logo affichés dans l'application.</p>

            <div class="mt-5 flex items-center gap-4">
                <span class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-sand-200 bg-sand-50 dark:border-ink-700 dark:bg-ink-800">
                    @if ($settings->logoUrl())
                        <img src="{{ $settings->logoUrl() }}" alt="Logo" class="h-full w-full object-cover">
                    @else
                        <svg class="h-7 w-7 text-ink-300 dark:text-sand-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M18 10.5h.008v.008H18V10.5Zm-12 6h12a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 18 4.5H6A2.25 2.25 0 0 0 3.75 6.75v9.75A2.25 2.25 0 0 0 6 18.75Z" /></svg>
                    @endif
                </span>
                <div class="flex-1">
                    <x-input-label for="logo" value="Logo (PNG, JPG — 2 Mo max)" />
                    <input id="logo" type="file" name="logo" accept="image/*" class="mt-1 block w-full text-xs text-ink-500 file:mr-3 file:rounded-lg file:border-0 file:bg-sand-100 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-ink-700 hover:file:bg-sand-200 dark:text-sand-400 dark:file:bg-ink-800 dark:file:text-sand-200 dark:hover:file:bg-ink-700">
                    <x-input-error :messages="$errors->get('logo')" class="mt-1.5" />
                </div>
            </div>

            <div class="mt-5">
                <x-input-label for="company_name" value="Nom de l'entreprise" />
                <x-text-input id="company_name" type="text" name="company_name" :value="old('company_name', $settings->company_name)" required class="max-w-sm" />
                <x-input-error :messages="$errors->get('company_name')" class="mt-1.5" />
            </div>
        </x-card>

        <x-card>
            <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Horaires d'ouverture</h2>
            <p class="mt-1 text-sm text-ink-500 dark:text-sand-400">Utilisés pour indiquer la disponibilité de votre équipe (information seulement).</p>

            <div class="mt-4 space-y-2">
                @foreach ($jours as $jour)
                    @php $horaire = $settings->business_hours[$jour] ?? ['enabled' => false, 'start' => '09:00', 'end' => '18:00']; @endphp
                    <div class="flex items-center gap-3 rounded-lg border border-sand-200 px-3 py-2 dark:border-ink-700">
                        <label class="flex w-32 shrink-0 items-center gap-2 text-sm text-ink-700 dark:text-sand-200">
                            <input type="checkbox" name="business_hours[{{ $jour }}][enabled]" value="1" @checked($horaire['enabled']) class="rounded border-sand-300 text-ink-900 focus:ring-ink-500 dark:border-ink-600 dark:bg-ink-800">
                            {{ ucfirst($jour) }}
                        </label>
                        <input type="time" name="business_hours[{{ $jour }}][start]" value="{{ $horaire['start'] }}" class="rounded-lg border-sand-300 bg-white text-sm text-ink-900 focus:border-ink-500 focus:ring-ink-500 dark:border-ink-700 dark:bg-ink-800 dark:text-sand-50">
                        <span class="text-sm text-ink-400 dark:text-sand-500">à</span>
                        <input type="time" name="business_hours[{{ $jour }}][end]" value="{{ $horaire['end'] }}" class="rounded-lg border-sand-300 bg-white text-sm text-ink-900 focus:border-ink-500 focus:ring-ink-500 dark:border-ink-700 dark:bg-ink-800 dark:text-sand-50">
                    </div>
                @endforeach
            </div>
        </x-card>

        <x-card>
            <h2 class="text-sm font-semibold text-ink-900 dark:text-sand-50">Apparence</h2>
            <div class="mt-4">
                <x-input-label for="default_theme" value="Thème par défaut pour les nouvelles sessions" />
                <x-select-input id="default_theme" name="default_theme" class="max-w-xs">
                    <option value="system" @selected($settings->default_theme === 'system')>Système (auto)</option>
                    <option value="light" @selected($settings->default_theme === 'light')>Clair</option>
                    <option value="dark" @selected($settings->default_theme === 'dark')>Sombre</option>
                </x-select-input>
            </div>
        </x-card>

        <div>
            <x-primary-button>Enregistrer les paramètres</x-primary-button>
        </div>
    </form>
</x-app-layout>
