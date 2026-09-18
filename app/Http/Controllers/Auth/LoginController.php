<?php

namespace App\Http\Controllers\Auth;

use App\Auth\LoginLinks\LoginLinkRedeemer;
use App\Http\Controllers\Controller;
use App\Localization\LocaleSwitcher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function show(string $token): View
    {
        return view('public.redeem', ['token' => $token]);
    }

    public function store(Request $request, string $token, LoginLinkRedeemer $redeemer): Response
    {
        $player = $redeemer->redeem($token);

        if ($player === null) {
            return redirect()->route('landing')->with('status', 'login-link-invalid');
        }

        Auth::login($player, remember: true);
        $request->session()->regenerate();
        $request->session()->flash('status', 'signed-in');

        return Inertia::location(route('home'));
    }

    public function destroy(Request $request, LocaleSwitcher $switcher): RedirectResponse
    {
        $locale = app()->getLocale();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $switcher->switch($request, $locale);

        return redirect()->route('landing');
    }
}
