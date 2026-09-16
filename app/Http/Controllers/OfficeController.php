<?php

namespace App\Http\Controllers;

use App\Http\Resources\WorkplaceResource;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OfficeController extends Controller
{
    public function show(Request $request): Response
    {
        /** @var User $player */
        $player = $request->user();

        return Inertia::render('office', [
            'workplace' => (new WorkplaceResource($player->employment))->resolve($request),
        ]);
    }
}
