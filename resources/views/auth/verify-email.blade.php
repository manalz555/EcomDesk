<x-guest-layout>
    <h1 class="text-2xl font-semibold tracking-tight text-ink-900 dark:text-sand-50">Vérifiez votre email</h1>
    <p class="mt-3 text-sm text-ink-500 dark:text-sand-400">
        Merci de votre inscription ! Avant de commencer, pouvez-vous vérifier votre adresse email en cliquant sur le lien
        que nous venons de vous envoyer ? Si vous ne l'avez pas reçu, nous pouvons vous en renvoyer un.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
            Un nouveau lien de vérification a été envoyé à l'adresse indiquée lors de l'inscription.
        </div>
    @endif

    <div class="mt-6 flex items-center justify-between gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>Renvoyer l'email de vérification</x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm font-medium text-ink-500 underline-offset-2 hover:underline dark:text-sand-400">
                Déconnexion
            </button>
        </form>
    </div>
</x-guest-layout>
