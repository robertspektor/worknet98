<?php

namespace App\Http\Controllers\Auth;

use App\Auth\LoginLinks\LoginLinkIssuer;
use App\Auth\Registration\PlayerRegistrar;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignInRequest;
use App\Localization\LocaleSwitcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class SignInController extends Controller
{
    public function store(SignInRequest $request, PlayerRegistrar $registrar, LoginLinkIssuer $issuer, LocaleSwitcher $switcher): RedirectResponse
    {
        $switcher->switch($request, $request->locale());
        $player = $registrar->register($request->email(), $request->locale());

        if ($player === null) {
            $issuer->issue($request->email(), $request->locale());

            return back()->with('status', 'login-link-sent');
        }

        Auth::login($player, remember: true);
        $request->session()->regenerate();

        return redirect()->route('home')->with('status', 'signed-in');
    }
}
