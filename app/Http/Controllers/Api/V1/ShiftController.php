<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ShiftStatusResource;
use App\Models\Employment;
use App\Work\ShiftClock;
use App\Work\ShiftStatusQuery;
use Illuminate\Http\Request;

class ShiftController extends ApiController
{
    public function __construct(private readonly ShiftStatusQuery $statuses) {}

    public function show(Request $request): ShiftStatusResource
    {
        return $this->statusOf($this->employment($request));
    }

    public function clockIn(Request $request, ShiftClock $clock): ShiftStatusResource
    {
        return $this->statusOf($clock->clockIn($this->player($request))->employment);
    }

    public function heartbeat(Request $request, ShiftClock $clock): ShiftStatusResource
    {
        return $this->statusOf($clock->heartbeat($this->player($request))->employment);
    }

    public function clockOut(Request $request, ShiftClock $clock): ShiftStatusResource
    {
        return $this->statusOf($clock->clockOut($this->player($request))->employment);
    }

    private function statusOf(Employment $employment): ShiftStatusResource
    {
        return new ShiftStatusResource($this->statuses->for($employment));
    }
}
