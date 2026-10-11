<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function create()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // The public request form does not need an authenticated session. Clear any
        // stale/non-admin session so this URL always presents the administrator login.
        if (Auth::check()) {
            Auth::logout();
        }

        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);

        if (Auth::check()) {
            Auth::logout();
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            if (Auth::user()->isAdmin()) {
                $request->session()->forget('url.intended');

                return redirect()->route('admin.dashboard');
            }
            Auth::logout();
        }

        return back()->withErrors(['email' => 'These credentials do not belong to an administrator.'])->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
