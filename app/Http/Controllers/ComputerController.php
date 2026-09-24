<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\World\LocalCity;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ComputerController extends Controller
{
    public function show(Request $request, LocalCity $cities): Response
    {
        /** @var User $player */
        $player = $request->user();

        return Inertia::render('computer', [
            'city' => $cities->nameFor($player->locale),
        ]);
    }
}
