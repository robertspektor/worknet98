<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Terminal\ResidentCard;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class TerminalController extends Controller
{
    public function show(Request $request, ResidentCard $card): Response
    {
        Inertia::setRootView('public.terminal');

        /* The way home still has to tell a new resident from a returning one. */
        $request->session()->keep(['status']);

        /** @var User $player */
        $player = $request->user();

        return Inertia::render('terminal', $card->of($player));
    }

    public function leave(Request $request): HttpResponse
    {
        $isNew = $request->session()->get('status') === 'registered';
        $request->session()->flash('status', $isNew ? 'arriving' : 'returning');

        return Inertia::location(route('home'));
    }
}
