<?php

namespace App\Http\Controllers\Api\V1;

use App\Game\ActionRefused;
use App\Http\Resources\ShiftStatusResource;
use App\Models\Shift;
use App\Work\ShiftClock;
use App\Work\ShiftRefusal;
use App\Work\ShiftStatus;
use App\Work\TodaysShift;
use Illuminate\Http\Request;

class ShiftController extends ApiController
{
    public function show(Request $request, TodaysShift $todaysShift): ShiftStatusResource
    {
        $employment = $this->player($request)->employment ?? throw new ActionRefused(ShiftRefusal::NotEmployed);

        return new ShiftStatusResource(new ShiftStatus($employment, $todaysShift->of($employment)));
    }

    public function clockIn(Request $request, ShiftClock $clock): ShiftStatusResource
    {
        return $this->statusOf($clock->clockIn($this->player($request)));
    }

    public function clockOut(Request $request, ShiftClock $clock): ShiftStatusResource
    {
        return $this->statusOf($clock->clockOut($this->player($request)));
    }

    private function statusOf(Shift $shift): ShiftStatusResource
    {
        return new ShiftStatusResource(new ShiftStatus($shift->employment, $shift));
    }
}
