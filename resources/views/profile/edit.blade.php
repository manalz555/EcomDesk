<x-app-layout title="Profil">
    <x-slot name="header">
        <x-page-header title="Profil" subtitle="Gérez les informations et la sécurité de votre compte." />
    </x-slot>

    <div class="max-w-2xl space-y-6">
        <x-card class="flex items-center gap-4">
            <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-ink-900 text-xl font-semibold text-sand-50 dark:bg-sand-100 dark:text-ink-900">
                {{ Illuminate\Support\Str::of($user->name)->substr(0, 1)->upper() }}
            </span>
            <div class="min-w-0">
                <p class="truncate text-base font-semibold text-ink-900 dark:text-sand-50">
                    {{ trim($user->prenom.' '.$user->name) }}
                </p>
                <p class="truncate text-sm text-ink-500 dark:text-sand-400">{{ $user->email }}</p>
                <div class="mt-2 flex items-center gap-2">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize
                        {{ $user->isAdmin() ? 'bg-ink-900 text-sand-50 dark:bg-sand-100 dark:text-ink-900' : 'bg-sand-100 text-ink-700 dark:bg-ink-800 dark:text-sand-200' }}">
                        {{ $user->isAdmin() ? 'Administrateur' : ($user->isManager() ? 'Manager' : 'Agent') }}
                    </span>
                    <span class="text-xs text-ink-400 dark:text-sand-500">Membre depuis {{ $user->created_at->translatedFormat('F Y') }}</span>
                </div>
            </div>
        </x-card>

        <x-card>
            @include('profile.partials.update-profile-information-form')
        </x-card>

        <x-card>
            @include('profile.partials.update-password-form')
        </x-card>

        <x-card>
            @include('profile.partials.delete-user-form')
        </x-card>
    </div>
</x-app-layout>
