<?php

namespace App\Http\Middleware;

use App\Auth\LastSeen;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordLastSeen
{
    public function __construct(private readonly LastSeen $lastSeen) {}

    public function handle(Request $request, Closure $next): Response
    {
        $player = $request->user();

        if ($player instanceof User) {
            $this->lastSeen->touch($player);
        }

        return $next($request);
    }
}
