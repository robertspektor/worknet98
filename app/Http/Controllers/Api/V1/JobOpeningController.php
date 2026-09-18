<?php

namespace App\Http\Controllers\Api\V1;

use App\Careers\JobBoard;
use App\Http\Resources\JobOpeningResource;
use App\Metrics\FunnelStep;
use App\Metrics\PlayerEvents;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class JobOpeningController extends ApiController
{
    public function index(Request $request, JobBoard $board, PlayerEvents $events): AnonymousResourceCollection
    {
        $player = $this->player($request);
        $events->record($player, FunnelStep::WorkNetOpened);

        return JobOpeningResource::collection($board->openingsFor($player));
    }
}
