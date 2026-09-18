<?php

namespace App\Http\Controllers\Api\V1;

use App\Metrics\FunnelStep;
use App\Metrics\PlayerEvents;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DesktopReachedController extends ApiController
{
    public function store(Request $request, PlayerEvents $events): JsonResponse
    {
        $events->record($this->player($request), FunnelStep::DesktopReached);

        return response()->json(status: 204);
    }
}
