<?php

namespace App\Http\Middleware;

use App\Metrics\SessionRecorder;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordSession
{
    public function __construct(private readonly SessionRecorder $sessions) {}

    public function handle(Request $request, Closure $next): Response
    {
        $player = $request->user();

        if ($player instanceof User) {
            $this->sessions->touch($player);
        }

        return $next($request);
    }
}
