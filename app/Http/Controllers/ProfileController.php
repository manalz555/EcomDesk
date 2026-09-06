<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/** Lets the logged-in user edit their own name/email/avatar or delete their own account. */
class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        // Changing the email address invalidates the previous verification —
        // the new address hasn't been proven to belong to this user yet.
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return back()->with('success', 'Profil mis a jour.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        // Require the current password again as a confirmation step —
        // account deletion is irreversible.
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        // Destroys the session and rotates the CSRF token so nothing from
        // the deleted account's session can be reused.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
