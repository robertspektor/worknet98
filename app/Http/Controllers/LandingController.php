<?php

namespace App\Http\Controllers;

use App\Landing\LandingFigures;
use App\World\LocalCity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LandingController extends Controller
{
    public function show(Request $request, LandingFigures $figures, LocalCity $cities): Response|RedirectResponse
    {
        if ($request->user() !== null) {
            return redirect()->route('home');
        }

        Inertia::setRootView('public.app');

        $locale = app()->getLocale();
        $city = $cities->nameFor($locale);
        $figures = $figures->forLocale($locale);

        return Inertia::render('landing', [
            'city' => $city,
            'figures' => $figures?->toArray(),
        ])->withViewData(['city' => $city, 'figures' => $figures]);
    }
}
