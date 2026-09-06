<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/** Login/logout — the `throttle:6,1` rate limit on these routes lives in routes/auth.php, not here. */
class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Identifiants incorrects.',
            ]);
        }

        // Auth::attempt() only checks the password — a deactivated account
        // (EnsureIsActive's job on every other route) must be rejected here
        // too, otherwise a disabled agent could still log in.
        if (! Auth::user()->actif) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Ce compte a ete desactive. Contactez un administrateur.',
            ]);
        }

        // Prevents session fixation: issues a fresh session ID after login.
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
