<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCanManageTeam
{
    /**
     * Autorise les administrateurs et les managers (supervision d'equipe).
     * A enregistrer dans bootstrap/app.php sous l'alias "manage-team".
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->canManageTeam()) {
            abort(403, 'Accès réservé aux administrateurs et managers.');
        }

        return $next($request);
    }
}
