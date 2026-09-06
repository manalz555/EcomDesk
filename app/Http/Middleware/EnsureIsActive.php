<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsActive
{
    /**
     * Deconnecte automatiquement un agent dont le compte a ete desactive.
     * A enregistrer dans bootstrap/app.php sous l'alias "actif".
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && ! $request->user()->actif) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Votre compte a ete desactive. Contactez un administrateur.',
            ]);
        }

        return $next($request);
    }
}
