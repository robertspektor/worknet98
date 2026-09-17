<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\TourPlanResource;
use App\Logistics\TourBoard;
use Illuminate\Http\Request;

class TourPlanController extends ApiController
{
    public function show(Request $request, TourBoard $board): TourPlanResource
    {
        return new TourPlanResource([
            'days' => $board->days(),
            'drivers' => $board->driversOf($this->employment($request)->branch()),
        ]);
    }
}
