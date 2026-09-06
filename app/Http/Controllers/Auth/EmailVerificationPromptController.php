<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

/** Shown by the `verified` middleware to any logged-in user whose email isn't confirmed yet. */
class EmailVerificationPromptController extends Controller
{
    public function __invoke(Request $request): RedirectResponse|\Illuminate\View\View
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->route('dashboard')
            : view('auth.verify-email');
    }
}
