<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    /**
     * Autorise uniquement les utilisateurs ayant le role "admin".
     * A enregistrer dans bootstrap/app.php sous l'alias "admin".
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isAdmin()) {
            abort(403, "Acces reserve a l'administrateur.");
        }

        return $next($request);
    }
}
