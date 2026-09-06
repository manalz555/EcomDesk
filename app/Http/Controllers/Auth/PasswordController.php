<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/** "Change password" form on the profile page (different from NewPasswordController, which is for a forgotten password). */
class PasswordController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        // 'current_password' validation rule re-checks the logged-in user's
        // existing password before allowing a change.
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Mot de passe mis a jour.');
    }
}
