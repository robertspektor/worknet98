<?php

namespace App\Http\Controllers;

use App\Localization\SupportedLocales;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function show(Request $request, SupportedLocales $locales): View|RedirectResponse
    {
        if ($request->user() !== null) {
            return redirect()->route('home');
        }

        return view('public.landing', ['locales' => $locales->all()]);
    }
}
