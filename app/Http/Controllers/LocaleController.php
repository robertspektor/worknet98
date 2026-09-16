<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateLocaleRequest;
use App\Localization\LocaleSwitcher;
use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    public function update(UpdateLocaleRequest $request, LocaleSwitcher $switcher): RedirectResponse
    {
        $switcher->switch($request, $request->locale());

        return back();
    }
}
