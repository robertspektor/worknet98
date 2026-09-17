<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ScheduleResource;
use App\Workplace\ScheduleBoard;
use Illuminate\Http\Request;

class ScheduleController extends ApiController
{
    public function show(Request $request, ScheduleBoard $board): ScheduleResource
    {
        return new ScheduleResource($board->for($this->employment($request)->branch()));
    }
}
