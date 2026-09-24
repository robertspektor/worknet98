<?php

namespace App\Http\Controllers\Auth;

use App\Auth\LoginLinks\ReturningPlayerLink;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginLinkRequest;
use App\Localization\LocaleSwitcher;
use Illuminate\Http\RedirectResponse;

class LoginLinkController extends Controller
{
    public function store(LoginLinkRequest $request, ReturningPlayerLink $link, LocaleSwitcher $switcher): RedirectResponse
    {
        $switcher->switch($request, $request->locale());
        $link->issue($request->email(), $request->locale());

        return back()->with('status', 'login-link-sent');
    }
}
