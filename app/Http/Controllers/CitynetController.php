<?php

namespace App\Http\Controllers;

use App\Citynet\CityOverviews;
use App\Citynet\EventChronicle;
use App\Http\Resources\CityOverviewResource;
use App\Http\Resources\WorldEventResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CitynetController extends Controller
{
    public function show(Request $request, CityOverviews $cities, EventChronicle $chronicle): Response
    {
        return Inertia::render('citynet', [
            'mayor' => (string) config('game.mayor_name'),
            'cities' => CityOverviewResource::collection($cities->all())->resolve($request),
            'events' => WorldEventResource::collection($chronicle->latest())->resolve($request),
        ]);
    }
}
