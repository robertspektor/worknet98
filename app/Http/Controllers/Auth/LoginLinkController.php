<?php

namespace App\Http\Controllers\Auth;

use App\Auth\LoginLinks\LoginLinkIssuer;
use App\Http\Controllers\Controller;
use App\Http\Requests\RequestLoginLinkRequest;
use App\Localization\LocaleSwitcher;
use Illuminate\Http\RedirectResponse;

class LoginLinkController extends Controller
{
    public function store(RequestLoginLinkRequest $request, LoginLinkIssuer $issuer, LocaleSwitcher $switcher): RedirectResponse
    {
        $switcher->switch($request, $request->locale());
        $issuer->issue($request->email(), $request->locale());

        return back()->with('status', 'login-link-sent');
    }
}
