<?php

namespace App\Http\Controllers\Auth;

use App\Auth\LoginLinks\LoginLinkRedeemer;
use App\Http\Controllers\Controller;
use App\Localization\LocaleSwitcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    public function show(string $token): Response
    {
        return Inertia::render('auth/redeem-login-link', ['token' => $token]);
    }

    public function store(Request $request, string $token, LoginLinkRedeemer $redeemer): RedirectResponse
    {
        $player = $redeemer->redeem($token);

        if ($player === null) {
            return redirect()->route('home')->with('status', 'login-link-invalid');
        }

        Auth::login($player, remember: true);
        $request->session()->regenerate();

        return redirect()->route('home')->with('status', 'signed-in');
    }

    public function destroy(Request $request, LocaleSwitcher $switcher): RedirectResponse
    {
        $locale = app()->getLocale();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $switcher->switch($request, $locale);

        return redirect()->route('home');
    }
}
